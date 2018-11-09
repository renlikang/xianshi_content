<?php
$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'X_kJP9dCToAIl_3Vhr_BugpYrCfJ43Iv',
        ],

        'log' => [
            'traceLevel' => 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'notamedia\sentry\SentryTarget',
                    'dsn' => 'https://408123322a93454db4b1245f070de933@sentry.heywoof.com/4',
                    'levels' => ['error', 'warning'],
                    'context' => true, // Write the context information. The default is true.
                    'prefix' => function () {
                        $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                        $uid = $user ? $user->getId(false) : '-';
                        $time = microtime(true);
                        $formatTime = date("Y-m-d H:i:s", $time) . "." . sprintf("%03d", ($time - floor($time)) * 1000);
                        $ip = \common\lib\Helper::getUserHostIp();
                        return "[$formatTime] [$uid] [$ip]";
                    },
                    'exportInterval' => 1,
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\web\HttpException:403',
                        'yii\web\HttpException:451',
                        'yii\web\HttpException:423',
                    ],
                ],
                [//默认业务日志, 通常用来定位问题，记录API调用等, Yii::info("api call", __CLASS__ . '::' . __FUNCTION__);
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['api\*', 'common\*'],
                    'logFile' => '/opt/logs/woof/api/app.log',
                    'logVars' => [],
                    'prefix' => function () {
                        $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                        $uid = $user ? $user->getId(false) : '-';
                        $time = microtime(true);
                        $formatTime = date("Y-m-d H:i:s", $time) . "." . sprintf("%03d", ($time - floor($time)) * 1000);
                        $ip = \common\lib\Helper::getUserHostIp();
                        return "[$formatTime] [$uid] [$ip]";
                    },
                    'exportInterval' => 1,
                    'enableRotation' => false,
                ],
            ],

        ],
    ],
];



return $config;