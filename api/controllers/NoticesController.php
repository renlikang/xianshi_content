<?php

namespace api\controllers;

use common\services\RetCode;
use yii\rest\ActiveController;

/**
 * @SWG\Get(
 *     path="/notices/count",
 *     tags={"消息接口"},
 *     summary="获取消息统计数据",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 * @SWG\Get(
 *     path="/notices/like",
 *     tags={"消息接口"},
 *     summary="获取点赞消息",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 * @SWG\Get(
 *     path="/notices/comment",
 *     tags={"消息接口"},
 *     summary="获取评论消息",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 * @SWG\Get(
 *     path="/notices/follow",
 *     tags={"消息接口"},
 *     summary="获取关注消息",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 * @SWG\Get(
 *     path="/notices/notice",
 *     tags={"消息接口"},
 *     summary="获取通知消息",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 * @SWG\Put(
 *     path="/notices/{id}",
 *     tags={"消息接口"},
 *     summary="设置消息已读接口",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Parameter(in = "path",name = "id",description = "消息Id",required = true, type = "integer"),
 *     @SWG\Parameter(in = "formData",name = "is_read",description = "是否已读", required = true, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 */
class NoticesController extends ActiveController
{

    public $modelClass = 'common\models\content\Notices';

    public function actions()
    {
        $actions = parent::actions();
        $actions['count'] = [
            'class' => 'api\actions\notices\Count',
        ];
        $actions['like'] = [
            'class' => 'api\actions\notices\Search',
            'type' => 'like',
            'setAsRead' => false
        ];
        $actions['comment'] = [
            'class' => 'api\actions\notices\Search',
            'type' => 'comment',
            'setAsRead' => false
        ];
        $actions['follow'] = [
            'class' => 'api\actions\notices\Search',
            'type' => 'follow',
            'setAsRead' => false
        ];
        $actions['notice'] = [
            'class' => 'api\actions\notices\Search',
            'type' => 'notice',
            'setAsRead' => false
        ];

        $actions['wx-form-collection'] = [
            'class' => 'api\actions\notices\WxFormCollection',
        ];


        return $actions;
    }

    public function afterAction($action, $result)
    {
        return RetCode::response(200, $result);
    }
}