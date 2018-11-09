<?php

namespace common\components;

use common\services\api\PillServices;
use yii\base\BaseObject;
use yii\base\ErrorException;

class AddPillsJob extends BaseObject implements \yii\queue\RetryableJobInterface
{
    public $unionId;

    public $operationType;

    public $number;

    public function execute($queue)
    {
        if (PillServices::changeBalance($this->unionId, $this->operationType, $this->number) == false) {
            throw new ErrorException("尝试增加药丸失败");
        }
    }

    public function getTtr()
    {
        return 60;
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof ErrorException);
    }
}