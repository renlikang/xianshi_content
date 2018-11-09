<?php

namespace api\actions\account;

use common\models\content\Account;
use common\services\RetCode;

class DecibelsToPills extends \yii\rest\Action {

    public $modelClass = false;

    public function run() {
        $decibels = Decibels::getCurrentUserDecibels();

        if ($decibels < 160) {
            header('Content-Type: application/json');
            echo json_encode(RetCode::response(RetCode::DECIBELS_NOT_ENOUGH, ['decibels' => $decibels]));
            exit();
        }

        $account = Account::findOne(['userId' => \Yii::$app->user->id]);
        $account->superPills += 1;
        $account->decibels = 0;
        $account->save();

        if ($account->errors) {
            \Yii::error(json_encode($account->errors), __CLASS__ . "::" . __FUNCTION__ . "::". __LINE__);
            header('Content-Type: application/json');
            echo json_encode(RetCode::response(RetCode::DECIBELS_TO_PILLS_FAILED, ['decibels' => $decibels]));
            exit();
        }

        return ['decibels' => 0, 'superPills' => $account->superPills];
    }

}