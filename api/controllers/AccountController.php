<?php

namespace api\controllers;

use common\services\RetCode;

/**
 * @SWG\Get(
 *     path="/account/decibels",
 *     tags={"分贝系统"},
 *     summary="获取用户分贝账户余额",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 *
 * @SWG\Post(
 *     path="/account/decibelsToPills",
 *     tags={"分贝系统"},
 *     summary="使用160分贝换取1药丸",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 */

class AccountController extends \yii\rest\ActiveController {

    public $modelClass = 'common\models\content\Account';

    public function actions()
    {
        $actions = parent::actions();
        $actions['decibels'] = 'api\actions\account\Decibels';
        $actions['decibelsToPills'] = 'api\actions\account\DecibelsToPills';
        return $actions;
    }

    public function afterAction($action, $result)
    {
        return RetCode::response(200, $result);
    }

}