<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
defined('YII_ENV_PROD') or define('YII_ENV_PROD', false);
return [
    'components' => [
        'db_admin' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=woof_admin',
            'username' => 'root',
            'password' => 'Woof123456!',
            'charset' => 'utf8mb4',
        ],
        'db_content' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=woof_content',
            'username' => 'root',
            'password' => 'Woof123456!',
            'charset' => 'utf8mb4',
        ],

        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=woof_oauth2',
            'username' => 'root',
            'password' => 'Woof123456!',
            'charset' => 'utf8mb4',
        ],
        'cache' => [//主业务 cache
            'class' => 'common\components\Cache',
            'keyPrefix' => 'WOOF.DEV.',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'socketClientFlags' => STREAM_CLIENT_CONNECT
            ]
        ],
        'sessionCache' => [ //session cache
            'class' => 'common\components\Cache',
            'keyPrefix' => 'WOOF.DEV.SSID.',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'socketClientFlags' => STREAM_CLIENT_CONNECT
            ]
        ],
        'oauth2_redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => '6379',
            'database' => 5,
            'socketClientFlags' => STREAM_CLIENT_CONNECT
        ],
        'queue' => [
            'class' => \yii\queue\amqp\Queue::class,
            'host' => '127.0.0.1',
            'port' => 5672,
            'user' => 'admin',
            'password' => '12345678_woof',
            'queueName' => 'queue'
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '127.0.0.1:9200'],
            ],
            'autodetectCluster' => false
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
