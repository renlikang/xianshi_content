<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/16
 * Time: 7:38 PM
 */

namespace api\modules\v1\controllers;

use common\models\content\ArticleCommentModel;
use common\services\content\ArticleService;
use common\services\RetCode;
use common\services\UserService;
use yii\data\Pagination;
use yii\rest\Controller;
use Yii;
use yii\web\ForbiddenHttpException;

class ArticleController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/v1/article/list",
     *     tags={"内容管理"},
     *     summary="内容列表(v1版本)",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "tagName",description = "标签名称（全称）",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "type",description = "all 所有的 my 我关注的 myCreation 我的作品 ",required = true, default = "all", type = "string"),
     *     @SWG\Parameter(in = "query",name = "orderType",description = "排序方式 timeOrder按时间排序 praiseOrder 点赞数排序 commentOrder 评论数排序 默认按照权重排序",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionList()
    {
        $params = [];
        $tagName = Yii::$app->request->get('tagName') ?? '';
        if($tagName) {
            $params['tagName'] = urldecode($tagName);
        }
        
        $orderType = Yii::$app->request->get('orderType') ?? '';
        if($orderType == 'timeOrder') {
            $params['timeOrder'] = 2;
        } else if($orderType == 'praiseOrder') {
            $params['praiseOrder'] = 2;
        } else if($orderType == 'commentOrder') {
            $params['commentOrder'] = 2;
        }

        $page = (int)Yii::$app->request->get('page') ?? 0;
        $size = (int)Yii::$app->request->get('size');
        $type = Yii::$app->request->get('type');
        $offset = (int)Yii::$app->request->get('offset') ?? 0;
        if(!$page && !$offset) {
            $offset = 0;
        }

        if(($type == 'my' || $type == 'myCreation') && Yii::$app->user->isGuest == true) {
            throw new ForbiddenHttpException("必须登录用户才能访问");
        }

        $ret = ArticleService::articleListNew($offset, $page, $size, $type, $params);
        header("x-page: {$page}");
        header("x-size: {$size}");
        header("x-total: {$ret['total']}");
        header("x-offset: {$offset}");
        header("x-hasMore: {$ret['hasMore']}");
        return RetCode::response(200, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/v1/article/comment-list",
     *     tags={"内容管理"},
     *     summary="内容评论列表(v1)",
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
        $ret['list'] = [];
        foreach ($commentModel as $k => $v) {
            $data = $v->toArray();
            $data['replayUser'] = null;
            if($data['parentId']) {
                $data['replayUser'] = UserService::detail($data['parentId']);
            }
            $data['user'] = UserService::detail($v->uid);
            $ret['list'][] = $data;
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
}