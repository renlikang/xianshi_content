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
                    'dsn' => 'https://f6b7c9b56a9a4057b91ace7502dd099f@sentry.heywoof.com/3',
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
                    'except' => ['yii\web\HttpException:404', 'yii\web\ForbiddenHttpException:403'],

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


$config['bootstrap'][] = 'debug';
$config['modules']['debug'] = [
    'class' => 'yii\debug\Module',
    'allowedIPs'=>[
        '*',
    ]
];

$config['bootstrap'][] = 'gii';
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '::1', '*'],
];

return $config;