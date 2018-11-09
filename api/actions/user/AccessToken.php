<?php

namespace api\actions\user;

use yii\rest\Action;
use yii\web\ForbiddenHttpException;

class AccessToken extends Action {

    public $modelClass = false;

    public function run() {
        if (YII_ENV_PROD) {
            throw new ForbiddenHttpException("正式环境不允许访问。");
        }

        $userId = \Yii::$app->request->post('userId') ?? 1;
        $is_new = \Yii::$app->request->post('is_new') ?? false;

        $token = \Yii::$app->security->generateRandomString();
        $userInfo = [
            'id' => $userId,
            'token' => $token,
            'is_new' => $is_new,
            'expire' => time() + 24*60*60
        ];

        foreach ($userInfo as $key => $value) {
            \Yii::$app->sessionCache->hset($token, $key, $value);
        }
        \Yii::$app->sessionCache->expire($token, 24*60*60);

        return $token;
    }
}