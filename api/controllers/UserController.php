<?php

namespace api\controllers;

use common\models\User;
use common\services\RetCode;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

/**
 * @SWG\Post(
 *     path="/user/login",
 *     tags={"用户接口"},
 *     summary="用户登陆/获取Token",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "formData",name = "code",description = "微信小程序登陆code", required = true, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 *
 * @SWG\Post(
 *     path="/user/access-token",
 *     tags={"用户接口"},
 *     summary="获取Token/仅限测试环境使用",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "formData",name = "userId",description = "用户Id", required = false, type = "string"),
 *     @SWG\Parameter(in = "formData",name = "is_new",description = "是否是新用户", required = false, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 *
 * @SWG\Get(
 *     path="/users/{id}",
 *     tags={"用户接口"},
 *     summary="获取用户数据",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Parameter(in = "path",name = "id",description = "用户Id",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 *
 * @SWG\Put(
 *     path="/users/{id}",
 *     tags={"用户接口"},
 *     summary="更新用户数据",
 *     description="",
 *     produces={"application/json"},
 *     consumes = {"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
 *     @SWG\Parameter(in = "path",name = "id",description = "用户Id",required = true, type = "integer"),
 *     @SWG\Parameter(in = "formData",name = "nickName",description = "用户昵称", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "avatarUrl",description = "用户头像图片的 URL", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "gender",description = "性别（0 未知 1 男性 2 女性）", type = "integer"),
 *     @SWG\Parameter(in = "formData",name = "country",description = "用户所在国家", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "province",description = "用户所在省份", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "city",description = "用户所在城市", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "language",description = "语言（en 英文 zh_CN 简体中文 zh_TW 繁体中文）", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "birthday",description = "生日", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "signature",description = "用户签名", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "encryptedData",description = "包括敏感数据在内的完整用户信息的加密数据", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "iv",description = "加密算法的初始向量", type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 400,description = " bad request"),
 *     @SWG\Response(response = 500,description = " server error"),
 * )
 */
class UserController extends ActiveController
{

    public $modelClass = 'common\models\User';

    public $updateScenario = 'update';

    public function actions()
    {
        $actions = parent::actions();
        $actions['login'] = 'api\actions\user\Login';
        $actions['logout'] = 'api\actions\user\Logout';
        $actions['update']['class'] = 'api\actions\user\Update';
        $actions['access-token'] = 'api\actions\user\AccessToken';
        unset($actions['create'], $actions['delete']);
        return $actions;
    }


    public function afterAction($action, $result)
    {
        return RetCode::response(200, $result);
    }
}