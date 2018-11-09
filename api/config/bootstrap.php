<?php
\yii\base\Event::on(\yii\rest\Controller::className(), \yii\rest\Controller::EVENT_BEFORE_ACTION, ['api\event\Before', 'index']);

\yii\base\Event::on('\common\services\EventService',
    'EVENT_AFTER_COMMENT',
    ['common\services\FinancialService', 'add']
);

\yii\base\Event::on('\common\services\EventService',
    'EVENT_AFTER_PRAISE',
    ['common\services\FinancialService', 'add']
);

\yii\base\Event::on('\common\services\EventService',
    'EVENT_AFTER_FORWARD',
    ['common\services\FinancialService', 'add']
);

\yii\base\Event::on('\common\services\EventService',
    'EVENT_AFTER_PUBLISH',
    ['common\services\FinancialService', 'add']
);