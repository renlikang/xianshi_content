<?php
/**
 * @author rlk
 */

namespace common\services;

use yii\base\Event;

class EventService extends Event
{
    const EVENT_AFTER_COMMENT = 'EVENT_AFTER_COMMENT';
    const EVENT_AFTER_PRAISE = 'EVENT_AFTER_PRAISE';
    const EVENT_AFTER_FORWARD = 'EVENT_AFTER_FORWARD';
    const EVENT_AFTER_PUBLISH = 'EVENT_AFTER_PUBLISH';
    public $params;
}