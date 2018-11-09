<?php

namespace api\actions\account;

use common\models\content\Account;

class Decibels extends \yii\rest\Action {

    public $modelClass = false;

    public function run() {
        return ['decibels' => self::getCurrentUserDecibels()];
    }

    public static function getCurrentUserDecibels() {
        $userId = \Yii::$app->user->id;
        $account = Account::find()->where(['userId' => $userId])->one();
        return $account['decibels'] ?? 0;
    }
}