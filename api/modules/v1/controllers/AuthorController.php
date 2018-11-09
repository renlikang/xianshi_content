<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/16
 * Time: 8:20 PM
 */

namespace api\modules\v1\controllers;

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
     * @SWG\Get(
     *     path="/v1/author/comment-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的评论列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
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
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $data = [];
        $ret = [];
        $list = ArticleCommentModel::find()->where(['uid' => $uid, 'parentId' => 0])->orderBy('cTime desc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var ArticleCommentModel[] $model */
        $model = $list->offset($offset)->limit($pages->pageSize)->all();
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

        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }

        $ret['list'] = $data;
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$total}");
        header("x-offset: {$ret['offset']}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/v1/author/praise-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的点赞列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
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
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $model = ArticlePraiseModel::find()->where(['uid' => $uid])->all();
        if($model) {
            $articleIdArr = ArrayHelper::getColumn($model, 'articleId');
        } else {
            $articleIdArr = [0];
        }

        $ret = ArticleService::articleListNew($offset, $page, $size, '', ['articleId' => $articleIdArr]);
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$ret['total']}");
        header("x-offset: {$ret['offset']}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/v1/author/attention-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的关注列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionAttentionList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $ret = [];
        $uid = Yii::$app->request->get('authorId');
        $list = AuthorAttentionModel::find()->where(['uid' => $uid])->orderBy('cTime asc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var AuthorAttentionModel[] $model */
        $model = $list->offset($offset)->limit($pages->pageSize)->all();
        $ret = [];
        foreach ($model as $k => $v) {
            $ret[] = UserService::detail($v->authorId);
        }

        $data = $ret;
        $ret = [];
        $ret['list'] = $data;
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
        header("x-offset: {$ret['offset']}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/v1/author/fans-list",
     *     tags={"媒体作者管理"},
     *     summary="媒体作者的粉丝列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
     *     @SWG\Parameter(in = "query",name = "authorId",description = "作者编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionFansList()
    {
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $ret = [];
        $uid = Yii::$app->request->get('authorId');
        $list = AuthorAttentionModel::find()->where(['authorId' => $uid])->orderBy('cTime asc');
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var AuthorAttentionModel[] $model */
        $model = $list->offset($offset)->limit($pages->pageSize)->all();
        $ret = [];
        foreach ($model as $k => $v) {
            $ret[$k] = UserService::detail($v->uid);
            $ret[$k]['mutuallyFans'] = 0;
            if(!Yii::$app->user->isGuest) {
                if(AuthorAttentionModel::find()->where(['authorId' => Yii::$app->user->id, 'uid' => $uid])->exists()) {
                    $ret[$k]['mutuallyFans'] = 1;
                }
            }
        }

        $data = $ret;
        $ret = [];
        $ret['list'] = $data;
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
        header("x-offset: {$ret['offset']}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }
}