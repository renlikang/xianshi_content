<?php

namespace api\actions\notices;

use common\models\content\ArticleModel;
use common\models\content\Notices;
use common\models\User;
use Yii;

class Search extends \yii\rest\Action {

    /**
     * @var bool
     */
    public $modelClass = false;

    /**
     * @var 消息类型，目前支持 like，comment，follow
     */
    public $type;

    /**
     * @var bool 是否自动设置消息为已读。
     */
    public $setAsRead = false;

    public function run() {
        $currentTime = time();
        $subQuery = Notices::find()->select(['type', 'content'])->where([
            'type' => $this->type,
            'is_read' => 0,
            'is_delete' => 0,
            'user_id' => \Yii::$app->user->id
        ])->orderBy('create_at desc');
        $notices = Notices::find()->select(['type', 'content'])->from(['tmpA' => $subQuery])->addGroupBy('content')->all();
//        $notices = Notices::find()->select(['type', 'content'])->where([
//            'type' => $this->type,
//            'is_read' => 0,
//            'is_delete' => 0,
//            'user_id' => \Yii::$app->user->id
//        ])->orderBy('create_at desc')->addGroupBy('content')->all();

        $output = [];
        foreach ($notices as $notice) {
            $data['notice'] = $notice;

            if ($notice['type'] == 'like' || $notice['type'] == 'comment') {
                $data['user'] = User::findIdentity($notice['content']['uid']);
                $data['article'] = ArticleModel::findOne(['articleId' => $notice['content']['articleId']]);
            } elseif ($notice['type'] == 'follow') {
                $data['user'] = User::findIdentity($notice['content']['uid']);
            }

            $output[] = $data;
        }

        if ($this->setAsRead === true) {
            Notices::updateAll(['is_read' => 1],"type = :type and user_id = :user_id and is_read = :is_read and is_delete = :is_delete and create_at < :currentTime", [
                ':type' => $this->type,
                ':user_id' => \Yii::$app->user->id,
                ':is_read' => 0,
                ':is_delete' => 0,
                ':currentTime' => $currentTime
            ]);
        }

        return $output;
    }
}