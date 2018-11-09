<?php
/**
 * @author rlk
 */

namespace api\controllers;

use common\components\NoticeJob;
use common\models\content\ArticleCommentModel;
use common\models\content\ArticleForward;
use common\models\content\ArticleModel;
use common\models\content\ArticlePraiseModel;
use common\models\content\AuthorAttentionModel;
use common\models\content\ParagraphContentModel;
use common\models\content\ParagraphModel;
use common\models\User;
use common\services\content\ArticleService;
use common\services\content\FilterService;
use common\services\content\TagService;
use common\services\EventService;
use common\services\RetCode;
use common\services\UserService;
use yii\base\Event;
use yii\data\Pagination;
use yii\rest\Controller;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class ArticleController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/article/list",
     *     tags={"内容管理"},
     *     summary="内容列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "type",description = "all 所有的 my 我关注的 myCreation 我的作品 ",required = true, default = "all", type = "string"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $type = Yii::$app->request->get('type');
        if(($type == 'my' || $type='myCreation') && Yii::$app->user->isGuest == true) {
            throw new ForbiddenHttpException("必须登录用户才能访问");
        }
        $ret = ArticleService::articleOldList($page, $size, $type);
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$ret['total']}");
        return RetCode::response(200, $ret);
    }


    /**
     * @SWG\Get(
     *     path="/article/detail",
     *     tags={"内容管理"},
     *     summary="内容详情",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "articleId",description = "内容编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionDetail()
    {
        $data = [];
        $articleId = Yii::$app->request->get('articleId');
        $article = ArticleModel::findOne($articleId);
        if($article->deleteFlag == 1 || !$article) {
            throw new HttpException(RetCode::DELETE, RetCode::$responseMsg[RetCode::DELETE]);
        }
        
        $author = User::findOne($article->authorId);
        if(!$author) {
            $author = [];
        } else {
            $author = $author->toArray();
        }

        $paragraph = ParagraphModel::find()->where(['articleId' => $article->articleId])->orderBy('orderId asc')->all();
        foreach ($paragraph as $kk => $vv) {
            $data['paragraph'][] = ParagraphContentModel::find()->where(['paragraphId' => $vv->paragraphId])->orderBy('orderId asc')->all();
        }

        $data['author'] = $author;
        $data['author']['attentionStatus'] = self::getAttentionStatus($article);
        $data['article'] = $article->toArray();
        $data['article']['praiseStatus'] = self::getPraiseStatus($article);
        $data['article']['praiseTotal'] = ArticleService::getPraiseTotal($article);
        $data['article']['tags'] = TagService::tagAll($articleId);
        return RetCode::response(RetCode::SUCCESS, $data);
    }

    /**
     * @SWG\Post(
     *     path="/article/create",
     *     tags={"内容管理"},
     *     summary="创建文章",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "article",description = "导入的文章信息",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 100,description = "导入的文章信息失败"),
     * )
     *
     */
    public function actionCreate()
    {

        $authorId = Yii::$app->user->id;
        $article = Yii::$app->request->post('article');
        $params = [];
        $params['article']['tagNameArr'] = $article['articleInfo']['tagNames'] ?? [];
        $params['article']['type'] = 3;
        $params['article']['title'] = $article['articleInfo']['title'] ?? '';
        $params['article']['subTitle'] = $article['articleInfo']['subTitle'] ?? '';
        $params['article']['summary'] = $article['articleInfo']['summary'] ?? '';
        $params['article']['orderId'] = $article['articleInfo']['orderId'] ?? 0;
        $params['article']['coverType'] = $article['articleInfo']['coverType'] ?? 0;
        if (isset($article['articleInfo']['covers']) && is_array($article['articleInfo']['covers'])) {
            $params['article']['covers'] = $article['articleInfo']['covers'];
            $params['article']['headImg'] = $article['articleInfo']['covers'][0]['url'];
        } else {
            $params['article']['covers'] = [];
            $params['article']['headImg'] = $article['articleInfo']['headImg'] ?? '';
        }
        $params['article']['cTime'] = $article['articleInfo']['cTime'] ?? '';
        $params['contentInfo'] = $article['paragraph'];
        $params['authorId'] = $authorId;
        Yii::info($article, __CLASS__.'::'.__FUNCTION__);
        $articleService = new ArticleService;
        $articleService->filterArticle($params);
        try {
            $article = $articleService->createArticle($params);

            //事件触发(新增药丸)
            $event = new EventService();
            $event->params = ['user' => User::findOne($authorId), 'type' => 'publish', 'article' => $article];
            Event::trigger('\common\services\EventService', EventService::EVENT_AFTER_PUBLISH, $event);

            return RetCode::response(RetCode::SUCCESS);
        } catch (\Exception $e) {
            Yii::error($e->getCode() . '--' .$e->getMessage(), __CLASS__.'::'.__FUNCTION__);
            return RetCode::response($e->getCode(), [], [], $e->getMessage());
        }
    }

    public static function getPraiseStatus(ArticleModel $article)
    {
        if (!Yii::$app->user->isGuest) {
            if (ArticlePraiseModel::find()->where("articleId = :articleId and uid = :uid", [
                ':articleId' => $article->articleId,
                ':uid' => Yii::$app->user->id
            ])->one()) {
                return true;
            }
        }

        return false;
    }

    public static function getAttentionStatus(ArticleModel $article)
    {
        if (!Yii::$app->user->isGuest) {
            if (AuthorAttentionModel::find()->where("authorId = :authorId and uid = :uid", [
                ':authorId' => $article->authorId,
                ':uid' => Yii::$app->user->id
            ])->one()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @SWG\Post(
     *     path="/article/praise",
     *     tags={"内容管理"},
     *     summary="点赞",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "articleId",description = "内容编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionPraise()
    {
        $articleId = Yii::$app->request->post('articleId');
        $uid = Yii::$app->user->id;
        if(ArticlePraiseModel::find()->where(['articleId' => $articleId, 'uid' => $uid])->exists()) {
            return RetCode::response(RetCode::PRAISE_REPEAT);
        }

        $model = new ArticlePraiseModel;
        $model->uid = $uid;
        $model->articleId = $articleId;
        if(!$model->save()) {
            Yii::error($model->errors, __CLASS__.'::'.__FUNCTION__);
            return RetCode::response(RetCode::DB_ERROR);
        }

        // 点赞消息
        Yii::$app->queue->push(new NoticeJob([
            'type' => 'like',
            'content' => [
                'uid' => $uid,
                'articleId' => $articleId,
                'timestamp' => time()
            ]
        ]));

        //事件触发(新增药丸)
        $event = new EventService();
        $event->params = ['user' => User::findOne($uid), 'type' => 'praise', 'article' => ArticleModel::findOne($articleId)];
        Event::trigger('\common\services\EventService', EventService::EVENT_AFTER_PRAISE, $event);

        return RetCode::response(RetCode::SUCCESS);
    }

    /**
     * @SWG\Post(
     *     path="/article/comment",
     *     tags={"内容管理"},
     *     summary="评论",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "articleId",description = "内容编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "content",description = "内容",required = true, type = "string"),
     *     @SWG\Parameter(in = "formData",name = "replayUid",description = "@用户编号",required = false, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionComment()
    {
        $articleId = (int)Yii::$app->request->post('articleId');
        $content = Yii::$app->request->post('content');
        $parentId = (int)Yii::$app->request->post('replayUid');
        if(!$articleId || !$content) {
            return RetCode::response(RetCode::ERROR);
        }

        FilterService::text($content);
        $uid = Yii::$app->user->id;
        $model = new ArticleCommentModel;
        $model->uid = $uid;
        $model->content = $content;
        $model->articleId = $articleId;
        $model->parentId = $parentId;
        if(!$model->save()) {
            Yii::error($model->errors, __CLASS__.'::'.__FUNCTION__);
            return RetCode::response(RetCode::DB_ERROR);
        }

        $data = ArticleCommentModel::findOne($model->commentId)->toArray();
        $data['replayUser'] = null;
        if($data['parentId']) {
            $data['replayUser'] = UserService::detail($data['parentId']);
        }

        $data['user'] = UserService::detail($model->uid);
        // 评论消息
        Yii::$app->queue->push(new NoticeJob([
            'type' => 'comment',
            'content' => [
                'uid' => $uid,
                'content' => $content,
                'articleId' => $articleId,
                'replayUid' => $parentId,
                'timestamp' => time()
            ]
        ]));

        //事件触发(新增药丸)
        $event = new EventService();
        $event->params = ['user' => User::findOne($uid), 'type' => 'comment', 'article' => ArticleModel::findOne($articleId)];
        Event::trigger('\common\services\EventService', EventService::EVENT_AFTER_COMMENT, $event);

        return RetCode::response(RetCode::SUCCESS, $data);
    }

    /**
     * @SWG\Get(
     *     path="/article/comment-list",
     *     tags={"内容管理"},
     *     summary="内容评论列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "articleId",description = "内容ID",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionCommentList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $articleId = Yii::$app->request->get('articleId');
        $list = ArticleCommentModel::find()->where(['articleId' => $articleId, 'deleteFlag' => 0])->orderBy('cTime desc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var ArticleCommentModel[] $commentModel */
        $commentModel = $list->offset($offset)->limit($pages->pageSize)->all();
        $ret = [];
        $ret['data'] = [];
        foreach ($commentModel as $k => $v) {
            $data = $v->toArray();
            $data['replayUser'] = null;
            if($data['parentId']) {
                $data['replayUser'] = UserService::detail($data['parentId']);
            }
            $data['user'] = UserService::detail($v->uid);
            $ret['data'][] = $data;
        }

        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$total}");
        header("x-offset: {$nexOffset}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(200, $ret);
    }

    /**
     * @SWG\Post(
     *     path="/article/forward",
     *     tags={"内容管理"},
     *     summary="转发",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "articleId",description = "内容编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionForward() {
        if (!Yii::$app->user->isGuest) {
            $articleId = Yii::$app->request->post('articleId');
            $uid = Yii::$app->user->id;

            $model = new ArticleForward();
            $model->uid = $uid;
            $model->articleId = $articleId;
            $model->cTime = time();
            $model->uTime = time();
            if (!$model->save()) {
                Yii::error($model->errors, __CLASS__ . '::' . __FUNCTION__);
                return RetCode::response(RetCode::DB_ERROR);
            }

            //事件触发(新增药丸)
            $event = new EventService();
            $event->params = ['user' => User::findOne($uid), 'type' => 'forward', 'article' => ArticleModel::findOne($articleId)];
            Event::trigger('\common\services\EventService', EventService::EVENT_AFTER_FORWARD, $event);
        }

        return RetCode::response(RetCode::SUCCESS);
    }
}