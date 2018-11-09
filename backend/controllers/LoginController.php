<?php
/**
 * @author rlk
 */

namespace backend\controllers;

use common\models\admin\AdminModel;
use common\services\RetCode;
use Yii;
use yii\rest\Controller;

/**
 * @SWG\Post(
 *     path="/login/index",
 *     tags={"基础功能"},
 *     summary="登录接口",
 *     description="备用 operation 3beef887",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "formData",name = "username",description = "登录名",required = true,default="renlikang", type = "string"),
 *     @SWG\Parameter(in = "formData",name = "password",description = "密码",required = true, default="64124780", type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 *     @SWG\Response(response = 100,description = " 用户名不存在"),
 *     @SWG\Response(response = 101,description = " 用户名或者密码错误"),
 * )
 *
 */
class LoginController extends Controller
{
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest == false) {
            Yii::$app->user->logout();
        }

        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        $user = AdminModel::findByUsername($username);
        if(!$user) {
            return [
                'code' => 100,
                'message' => '用户名不存在',
            ];
        }

        if($user->validatePassword($password)) {
            Yii::$app->user->login($user);
            Yii::info("login ", __CLASS__ . '::' . __FUNCTION__);
            $data = [
                'aid' => $user->aid,
                'username' => $user->username,
            ];
            return RetCode::response(RetCode::SUCCESS, $data);
        }

        return [
            'code' => 101,
            'message' => '用户名或者密码错误',
        ];
    }


    /**
     * @SWG\Post(
     *     path="/login/out",
     *     tags={"基础功能"},
     *     summary="退出登录接口",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionOut()
    {
        Yii::$app->user->logout();
        return ['code' => 200, 'message' => 'success'];
    }
}