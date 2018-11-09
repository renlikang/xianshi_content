<?php
/**
 * @author rlk
 */

namespace console\controllers;

use common\components\NoticeJob;
use yii\console\Controller;
use Yii;

class NoticeController extends Controller
{
    public function actionSubscribe()
    {

    }

    public function actionPub()
    {
        Yii::$app->queue->push(new NoticeJob([
            'type' => 'notice',
            'content' => 'RabbitMQ Test',
            'userId' => 61
        ]));
    }
}