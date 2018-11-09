<?php
/**
 * @author rlk
 */

namespace api\controllers;

use common\components\NoticeJob;
use common\models\content\ArticleCommentModel;
use common\models\content\ArticleModel;
use common\models\content\ArticlePraiseModel;
use common\models\content\AuthorAttentionModel;
use common\services\content\ArticleService;
use common\services\RetCode;
use common\services\UserService;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;

class AuthorController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/author/attention",
     *     tags={"媒体作者管理"},
     *     summary="关注",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionAttention()
    {
        $authorId = Yii::$app->request->post('authorId');
        $uid = Yii::$app->user->id;
        if(AuthorAttentionModel::find()->where(['authorId' => $authorId, 'uid' => $uid])->exists()) {
            return RetCode::response(RetCode::PRAISE_Attention);
        }

        $model = new AuthorAttentionModel;
        $model->authorId = $authorId;
        $model->uid = $uid;
        if(!$model->save()) {
            Yii::error($model->errors, __CLASS__.'::'.__FUNCTION__);
            return RetCode::response(RetCode::DB_ERROR);
        }

        // 关注消息
        Yii::$app->queue->push(new NoticeJob([
            'type' => 'follow',
            'content' => [
                'uid' => $uid,
                'authorId' => $authorId
            ]
        ]));

        return RetCode::response(RetCode::SUCCESS);
    }

    /**
     * @SWG\Post(
     *     path="/author/cancel-attention",
     *     tags={"媒体作者管理"},
     *     summary="取消关注",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionCancelAttention()
    {
        $authorId = Yii::$app->request->post('authorId');
        $uid = Yii::$app->user->id;
        if(AuthorAttentionModel::find()->where(['authorId' => $authorId, 'uid' => $uid])->exists()) {
            AuthorAttentionModel::deleteAll(['authorId' => $authorId, 'uid' => $uid]);
        }

        return RetCode::response(RetCode::SUCCESS);
    }

    /**
     * @SWG\Get(
     *     path="/author/detail",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者详情",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "媒体作者编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionDetail()
    {
        $authorId = Yii::$app->request->get('authorId');
        $data = UserService::detail($authorId);
        return RetCode::response(RetCode::SUCCESS, $data);
    }


    /**
     * @SWG\Get(
     *     path="/author/comment-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的评论列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页条数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionCommentList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $uid = (int)Yii::$app->request->get('authorId');
        $data = [];
        $ret = [];
        $list = ArticleCommentModel::find()->where(['uid' => $uid, 'parentId' => 0])->orderBy('cTime desc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        $pages->setPage($page - 1);
        /** @var ArticleCommentModel[] $model */
        $model = $list->offset($pages->offset)->limit($pages->pageSize)->all();
        foreach ($model as $k => $v) {
            $data[$k] = $v->toArray();
            $article = ArticleModel::findOne($v->articleId);
            if($article) {
                $data[$k]['article'] = $article->toArray();
                $data[$k]['article']['author'] = UserService::detail($article->authorId);
            } else {
                $data[$k]['article'] = [];
            }
        }

        $ret['data'] = $data;
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$total}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/author/praise-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的点赞列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页条数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionPraiseList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $uid = (int)Yii::$app->request->get('authorId');
        $model = ArticlePraiseModel::find()->where(['uid' => $uid])->all();
        if($model) {
            $articleIdArr = ArrayHelper::getColumn($model, 'articleId');
        } else {
            $articleIdArr = [0];
        }

        $ret = ArticleService::articleOldList($page, $size, '', ['articleId' => $articleIdArr]);
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$ret['total']}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/author/attention-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的关注列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionAttentionList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $ret = [];
        $uid = Yii::$app->request->get('authorId');
        $list = AuthorAttentionModel::find()->where(['uid' => $uid])->orderBy('cTime asc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        $pages->setPage($page - 1);
        /** @var AuthorAttentionModel[] $model */
        $model = $list->offset($pages->offset)->limit($pages->pageSize)->all();
        $user = UserService::detail($uid);
        $ret = [];
        foreach ($model as $k => $v) {
            $ret[] = UserService::detail($v->authorId);
        }

        $data = $ret;
        $ret = [];
        $ret['data'] = $data;
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$total}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/author/fans-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的粉丝列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionFansList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $ret = [];
        $uid = Yii::$app->request->get('authorId');
        $list = AuthorAttentionModel::find()->where(['authorId' => $uid])->orderBy('cTime asc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        $pages->setPage($page - 1);
        /** @var AuthorAttentionModel[] $model */
        $model = $list->offset($pages->offset)->limit($pages->pageSize)->all();
        $user = UserService::detail($uid);
        $ret['fans'] = [];
        foreach ($model as $k => $v) {
            $ret['fans'][$k] = UserService::detail($v->uid);
            $ret['fans'][$k]['mutuallyFans'] = 0;
            if(!Yii::$app->user->isGuest) {
                if(AuthorAttentionModel::find()->where(['authorId' => Yii::$app->user->id, 'uid' => $uid])->exists()) {
                    $ret['fans'][$k]['mutuallyFans'] = 1;
                }
            }
        }

        $ret['user'] = $user;
        $data = $ret;
        $ret = [];
        $ret['data'] = $data;
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$total}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

}