<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/18
 * Time: 5:30 PM
 */

namespace backend\controllers;

use common\models\admin\AdminModel;
use common\services\RetCode;
use yii\rest\Controller;
use Yii;


/**
 * @SWG\Get(
 *     path="/admin/detail",
 *     tags={"后台用户管理"},
 *     summary="详情",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Response(response = 200,description = " success"),
 * )
 *
 */
class AdminController extends Controller
{
    public function actionDetail()
    {
        $uid = Yii::$app->user->id;
        $data =  AdminModel::findOne($uid);
        unset($data['password']);
        return RetCode::response(RetCode::SUCCESS, $data);
    }
}