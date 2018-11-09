<?php

namespace api\actions\notices;

use common\models\content\Notices;

class Count extends \yii\rest\Action {

    /**
     * @var bool
     */
    public $modelClass = false;

    public function run() {
        $types = ['like', 'comment', 'follow'];

        $notices = \Yii::$app->db_content->createCommand("select type, sum(is_read = 0 and is_delete = 0) as count from notices group by type")->queryAll();

        $data = [];
        foreach ($types as $type) {
            $data[$type] = Notices::find()->where("type = :type and is_read = 0 and is_delete = 0 and user_id = :userId", [
                ':type' => $type,
                ':userId' => \Yii::$app->user->id
            ])->count();
        }

        return $notices;
    }
}