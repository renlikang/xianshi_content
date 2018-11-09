<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/18
 * Time: 2:24 PM
 */

namespace backend\controllers;

use common\models\admin\OperationArticleModel;
use common\models\content\ArticleCommentModel;
use common\models\content\ArticleModel;
use common\models\content\ArticlePraiseModel;
use common\models\content\AuthorAttentionModel;
use common\models\content\ParagraphContentModel;
use common\models\content\ParagraphModel;
use common\models\User;
use common\services\backend\RoleService;
use common\services\content\ArticleService;
use common\services\content\TagService;
use common\services\RetCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\Controller;
use Yii;

class ArticleController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/article/list",
     *     tags={"内容管理"},
     *     summary="文章列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "query",name = "articleTitle",description = "文章标题",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "tagName",description = "标签名称",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "genre",description = "文章分类 1：长文 2：短文",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "type",description = "文章来源 1：微信 2：ins 3：UGC",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "startTime",description = "开始时间",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "endTime",description = "结束时间",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者ID",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "timeOrder",description = "时间顺序 1:正序 2：倒序",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "orderId",description = "时间顺序 1:权重正序 2：权重倒序",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionList()
    {
        $articleTitle = Yii::$app->request->get('articleTitle') ?? '';
        $genre = Yii::$app->request->get('genre') ?? 0;
        $type = Yii::$app->request->get('type') ?? 0;
        $startTime = Yii::$app->request->get('startTime') ?? '';
        $endTime = Yii::$app->request->get('endTime') ?? '';
        $authorId = Yii::$app->request->get('authorId') ?? '';
        $timeOrder = Yii::$app->request->get('timeOrder') ?? '';
        $orderId = Yii::$app->request->get('orderId') ?? '';
        $tagName = Yii::$app->request->get('tagName') ?? '';
        $params = [];
        if($tagName) {
            $params['tagName'] = urldecode($tagName);
        }
        if($genre) {
            $params['genre'] = $genre;
        }

        if($type) {
            $params['type'] = $type;
        }

        if($articleTitle) {
            $params['articleTitle'] = $articleTitle;
        }

        if($startTime) {
            $params['startTime'] = $startTime;
        }

        if($endTime) {
            $params['endTime'] = $endTime;
        }

        if($authorId) {
            $params['authorId'] = $authorId;
        }

        if($timeOrder) {
            $params['timeOrder'] = $timeOrder;
        }

        if($orderId) {
            $params['orderId'] = $orderId;
        }

        if(RoleService::isOperation(Yii::$app->user->id)) {
            $model = OperationArticleModel::find()->where(['aid' => Yii::$app->user->id])->all();
            if($model) {
                $params['articleIdArr'] = ArrayHelper::getColumn($model, 'articleId');
            } else {
                $params['articleIdArr'] = [];
            }
        }

        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $ret = ArticleService::articleListNew($offset, $page, $size, 'all', $params);
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/article/detail",
     *     tags={"内容管理"},
     *     summary="文章详情",
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
        $author = User::findOne($article->authorId);
        $paragraph = ParagraphModel::find()->where(['articleId' => $article->articleId])->orderBy('orderId asc')->all();
        foreach ($paragraph as $kk => $vv) {
            $data['paragraph'][] = ParagraphContentModel::find()->where(['paragraphId' => $vv->paragraphId])->orderBy('orderId asc')->all();
        }

        $data['author'] = $author->toArray();
        $data['author']['attentionStatus'] = self::getAttentionStatus($article);
        $data['article'] = $article->toArray();
        $data['article']['praiseStatus'] = self::getPraiseStatus($article);
        $data['article']['praiseTotal'] = ArticleService::getPraiseTotal($article);
        $data['article']['fakePraise'] = ArticleService::getFakePraise($article);
        $data['article']['tags'] = TagService::tagAll($article->articleId);
        return RetCode::response(RetCode::SUCCESS, $data);
    }

    /**
     * @SWG\Post(
     *     path="/article/create",
     *     tags={"内容管理"},
     *     summary="创建文章",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "authorId",description = "作者ID",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "article",description = "导入的文章信息",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 100,description = "导入的文章信息失败"),
     * )
     *
     */
    public function actionCreate()
    {
        try {
            $authorId = Yii::$app->request->post('authorId');
            $article = Yii::$app->request->post('article');
            $params = [];
            $params['article']['tagNameArr'] = $article['articleInfo']['tagNames'] ?? [];
            $params['article']['type'] = $article['articleInfo']['type'] ?? 1;
            $params['article']['title'] = $article['articleInfo']['title'] ?? '';
            $params['article']['subTitle'] = $article['articleInfo']['subTitle'] ?? '';
            $params['article']['summary'] = $article['articleInfo']['summary'] ?? '';
            $params['article']['orderId'] = $article['articleInfo']['orderId'] ?? 0;
            if(RoleService::isOperation(Yii::$app->user->id)) {
                $params['article']['orderId'] = 10;
            }
            
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
            $params['fakePraise'] = $article['fakePraise'] ?? 0;
            $params['authorId'] = $authorId;
            (new ArticleService)->createArticle($params, Yii::$app->user->id);
            return RetCode::response(RetCode::SUCCESS);
        } catch (\Exception $e) {
            Yii::error($e->getCode() . '--' .$e->getMessage(), __CLASS__.'::'.__FUNCTION__);
            return RetCode::response($e->getCode(), [], [], $e->getMessage());
        }
    }

    /**
     * @SWG\Post(
     *     path="/article/update",
     *     tags={"内容管理"},
     *     summary="更新文章",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "articleId",description = "文章ID",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "authorId",description = "作者ID",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "article",description = "导入的文章信息",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 100,description = "导入的文章信息失败"),
     * )
     *
     */
    public function actionUpdate()
    {

        try {
            $authorId = Yii::$app->request->post('authorId');
            $articleId = Yii::$app->request->post('articleId');
            $article = Yii::$app->request->post('article');
            $params = [];
            $params['article']['orderId'] = $article['articleInfo']['orderId'] ?? 0;
            if(RoleService::isOperation(Yii::$app->user->id)) {
                if(!OperationArticleModel::find()->where(['aid' => Yii::$app->user->id, 'articleId' => $articleId])->exists()) {
                    return RetCode::response(RetCode::ERROR);
                }

                unset($params['article']['orderId']);
            }

            $params['article']['tagNameArr'] = $article['articleInfo']['tagNames'] ?? [];
            //$params['article']['type'] = $article['articleInfo']['type'] ?? 1;
            $params['article']['title'] = $article['articleInfo']['title'] ?? '';
            $params['article']['subTitle'] = $article['articleInfo']['subTitle'] ?? '';
            $params['article']['summary'] = $article['articleInfo']['summary'] ?? '';
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
            $params['fakePraise'] = $article['fakePraise'] ?? 0;
            $params['authorId'] = $authorId;
            (new ArticleService)->updateArticle($articleId, $params);
            return RetCode::response(RetCode::SUCCESS);
        } catch (\Exception $e) {
            Yii::error($e->getCode() . '--' .$e->getMessage(), __CLASS__.'::'.__FUNCTION__);
            return RetCode::response($e->getCode(), [], [], $e->getMessage());
        }
    }

    /**
     * @SWG\Post(
     *     path="/article/update-detail",
     *     tags={"内容管理"},
     *     summary="批量更新",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "articleIds",description = "文章ID（多）",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "orderId",description = "权重",required = false, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionUpdateDetail()
    {
        $params = [];
        $articleIdArr = Yii::$app->request->post('articleIds');
        $orderId = Yii::$app->request->post('orderId');
        if($orderId) {
            $params['orderId'] = $orderId;
        }

        ArticleService::updateBatchArticleDetail($articleIdArr, $orderId);
        return RetCode::response(RetCode::SUCCESS);
    }

    /**
     * @SWG\Post(
     *     path="/article/delete",
     *     tags={"内容管理"},
     *     summary="删除文章",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "articleId",description = "文章ID",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionDelete()
    {
        $articleId = Yii::$app->request->post('articleId');
        $model = ArticleModel::findOne($articleId);
        $model->deleteFlag = 1;
        $model->save();
        return RetCode::response(RetCode::SUCCESS);
    }

    /**
     * @SWG\Get(
     *     path="/article/comment-list",
     *     tags={"内容管理"},
     *     summary="评论列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "query",name = "articleId",description = "文章Id",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionCommentList()
    {
        $params = [];
        $articleId = Yii::$app->request->get('articleId');
        if($articleId) {
            $params['articleId'] = $articleId;
        }

        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $ret = ArticleService::commentList($offset, $page, $size,  $params);
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Post(
     *     path="/article/comment-cancel",
     *     tags={"内容管理"},
     *     summary="删除评论",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "commentId",description = "评论ID",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionCommentCancel()
    {
        $commentId = Yii::$app->request->post('commentId');
        if(ArticleService::commentDelete($commentId)) {
            return RetCode::response(RetCode::SUCCESS);
        }

        return RetCode::response(RetCode::ERROR);
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
}