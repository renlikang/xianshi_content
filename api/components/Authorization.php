<?php

namespace api\components;

use yii\base\Component;
use Yii;
use yii\web\ForbiddenHttpException;

class Authorization extends Component
{
    public function init() {
        if ($token = \Yii::$app->request->headers['authorization']) {
            Yii::$app->user->loginByAccessToken($token);
        }
    }
}