<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/18
 * Time: 8:21 PM
 */

namespace console\controllers;

use common\models\content\Account;
use common\models\User;
use common\services\api\DecibelsServices;
use common\services\ExcelService;
use common\services\UserService;
use yii\console\Controller;
use Yii;

class UserController extends Controller
{
    public function actionDecibelsRule()
    {
        /** @var Account[] $model */
        $model = Account::find()->where("decibels > 0")->all();
        foreach ($model as $k => $v) {
            $userId = $v->userId;
            $loginDays = UserService::getLoginDays($userId);
            //echo "userId: " . $userId . ", loginDays: " . $loginDays . "\n";
            if($loginDays >= 3) {
                DecibelsServices::add($userId, -1, '三日未登陆');
            }
        }
    }

    public function actionLoadingAccount($filePath, $imgPath)
    {
        $excelService = new ExcelService();
        $rowData = $excelService->read($filePath);
        unset($rowData[0]);
        foreach ($rowData as $k => $v) {
            if(file_exists($imgPath . '/' . $v[0] . '.jpg')) {
                $img = $imgPath . '/' . $v[0] . '.jpg';
                $fileName = md5(time() . mt_rand(10, 99)) . ".jpg";
                $url = Yii::$app->upYun->upload($img, $fileName);
            } else if($img = file_exists($imgPath . '/' . $v[0] . '.png')) {
                $img = $imgPath . '/' . $v[0] . '.png';
                $fileName = md5(time() . mt_rand(10, 99)) . ".png";
                $url = Yii::$app->upYun->upload($img, $fileName);
            } else {
                var_dump($v[0]);
                continue;
            }

            if(!$url) {
                continue;
            }

            $user = new User;
            $user->nickName = $v[1];
            $user->avatarUrl = $url;
            $user->type = 2;
            $user->signature = '';
            $user->created_at = time();
            $user->updated_at = time();
            $user->openid = '';
            $user->session_key = 'none';
            $user->openid = 'none';
            $user->unionid = '';
            if(!$user->save()) {
                Yii::info($user->errors, __CLASS__.'::'.__FUNCTION__);
                return false;
            }
            echo $user->id . "\n";
        }
    }
}