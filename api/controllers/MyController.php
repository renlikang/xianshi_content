<?php
/**
 * @author rlk
 */

namespace api\controllers;

use common\models\content\ArticleCommentModel;
use common\models\content\ArticleModel;
use common\models\content\ArticlePraiseModel;
use common\models\content\AuthorAttentionModel;
use common\models\content\AuthorModel;
use common\services\content\ArticleService;
use common\services\content\AuthorService;
use common\services\RetCode;
use common\services\UserService;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;
use yii\web\ForbiddenHttpException;

class MyController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/my/info",
     *     tags={"我的"},
     *     summary="详情",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionInfo()
    {
        $data = UserService::detail(Yii::$app->user->id);
        return RetCode::response(RetCode::SUCCESS, $data);
    }

    /**
     * @SWG\Get(
     *     path="/my/comment-list",
     *     tags={"我的"},
     *     summary="我的评论列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
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
        $uid = (int)Yii::$app->user->id;
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
     *     path="/my/praise-list",
     *     tags={"我的"},
     *     summary="我的点赞列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
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
        $uid = (int)Yii::$app->user->id;
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
     *     path="/my/attention-list",
     *     tags={"我的"},
     *     summary="我的关注列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
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
        $uid = Yii::$app->user->id;
        $list = AuthorAttentionModel::find()->where(['uid' => $uid])->orderBy('cTime asc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        $pages->setPage($page - 1);
        /** @var AuthorAttentionModel[] $model */
        $model = $list->offset($pages->offset)->limit($pages->pageSize)->all();
        $user = UserService::detail($uid);
        $ret['attention'] = [];
        foreach ($model as $k => $v) {
            $ret['attention'][] = UserService::detail($v->authorId);
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

    /**
     * @SWG\Get(
     *     path="/my/fans-list",
     *     tags={"我的"},
     *     summary="我的粉丝列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
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
        $uid = Yii::$app->user->id;
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
            $ret['fans'][] = UserService::detail($v->uid);
            $ret['fans'][$k]['mutuallyFans'] = 0;
            if(AuthorAttentionModel::find()->where(['authorId' => $v->uid, 'uid' => $uid])->exists()) {
                $ret['fans'][$k]['mutuallyFans'] = 1;
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

    /**
     * @SWG\Get(
     *     path="/my/attention-ids",
     *     tags={"我的"},
     *     summary="我的关注的人Ids",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "string"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     */
    public function actionAttentionIds()
    {
        $uid = Yii::$app->user->id;
        $attentions = AuthorAttentionModel::find()->select('authorId')->where(['uid' => $uid])->orderBy('cTime asc');

        $ids = [];
        foreach ($attentions->all() as $attention) {
            $ids[] = $attention['authorId'];
        }

        return RetCode::response(RetCode::SUCCESS, $ids);
    }

    /**
     * @SWG\Get(
     *     path="/my/praise-ids",
     *     tags={"我的"},
     *     summary="我点赞的人Ids",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "string"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     */
    public function actionPraiseIds()
    {
        $uid = Yii::$app->user->id;
        $attentions = ArticlePraiseModel::find()->select('articleId')->where(['uid' => $uid])->orderBy('cTime asc')->all();
        if(!$attentions) {
            $ids = [];
        } else {
            $ids = ArrayHelper::getColumn($attentions, 'articleId');
        }

        return RetCode::response(RetCode::SUCCESS, $ids);
    }


}