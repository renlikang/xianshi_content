<?php

namespace api\actions\user;

use yii\rest\Action;

class Logout extends Action {

    public $modelClass = false;

    public function run() {
        $token = \Yii::$app->request->headers->get('Authorization');

        return \Yii::$app->sessionCache->del($token);
    }
}