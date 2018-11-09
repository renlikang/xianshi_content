<?php
$config = [
    'components' => [
        'log' => [
            'traceLevel' => 0,
            'flushInterval' => 1,
            'targets' => [
                [//默认业务日志, 通常用来定位问题，记录API调用等, Yii::info("api call", __CLASS__ . '::' . __FUNCTION__);
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['common\*', 'console\*'],
                    'logFile' => '/opt/logs/woof/console/error.log',
                    'logVars' => [],
                    'exportInterval' => 1,
                    'enableRotation' => false,
                ],

                [//默认业务日志, 通常用来定位问题，记录API调用等, Yii::info("api call", __CLASS__ . '::' . __FUNCTION__);
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['common\*', 'console\*'],
                    'logFile' => '/opt/logs/woof/console/app.log',
                    'logVars' => [],
                    'exportInterval' => 1,
                    'enableRotation' => false,
                ],
            ],

        ],
    ],
];


return $config;