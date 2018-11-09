<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/16
 * Time: 8:36 PM
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

class MyController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/v1/my/comment-list",
     *     tags={"我的"},
     *     summary="我的评论列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
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
        $uid = (int)Yii::$app->user->id;
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
     *     path="/v1/my/praise-list",
     *     tags={"我的"},
     *     summary="我的点赞列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
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
        $uid = (int)Yii::$app->user->id;
        $offset = (int)Yii::$app->request->get('offset');
        if(!$page && !$offset) {
            $offset = 0;
        }

        $model = ArticlePraiseModel::find()->where(['uid' => $uid])->orderBy('cTime desc')->all();
        if($model) {
            $articleIdArr = ArrayHelper::getColumn($model, 'articleId');
        } else {
            $articleIdArr = [0];
        }

        $ret = ArticleService::articleList($offset, $page, $size, '', ['articleId' => $articleIdArr]);
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$ret['total']}");
        header("x-offset: {$ret['offset']}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/v1/my/attention-list",
     *     tags={"我的"},
     *     summary="我的关注列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
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
        $uid = (int)Yii::$app->user->id;
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
     *     path="/v1/my/fans-list",
     *     tags={"我的"},
     *     summary="我的粉丝列表(v1)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "string"),
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
        $uid = (int)Yii::$app->user->id;
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