<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', false);
defined('YII_ENV_PROD') or define('YII_ENV_PROD', true);

return [
    'components' => [
        'db_admin' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=prd-woof-community.ctrvsh6q54n2.rds.cn-north-1.amazonaws.com.cn;dbname=woof_admin',
            'username' => 'heywoofprod',
            'password' => 'zaq12wsxprod!',
            'charset' => 'utf8mb4',
        ],
        'db_content' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=prd-woof-community.ctrvsh6q54n2.rds.cn-north-1.amazonaws.com.cn;dbname=woof_content',
            'username' => 'heywoofprod',
            'password' => 'zaq12wsxprod!',
            'charset' => 'utf8mb4',
        ],

        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=prd-woof-community.ctrvsh6q54n2.rds.cn-north-1.amazonaws.com.cn;dbname=woof_oauth2',
            'username' => 'heywoofprod',
            'password' => 'zaq12wsxprod!',
            'charset' => 'utf8mb4',
        ],
        'cache' => [//主业务 cache
            'class' => 'common\components\Cache',
            'keyPrefix' => 'WOOF.DEV.',
            'redis' => [
                'hostname' => 'prd-woof-community.1f0vo6.0001.cnn1.cache.amazonaws.com.cn',
                'port' => 6379,
                'socketClientFlags' => STREAM_CLIENT_CONNECT
            ]
        ],
        'sessionCache' => [ //session cache
            'class' => 'common\components\Cache',
            'keyPrefix' => 'WOOF.DEV.SSID.',
            'redis' => [
                'hostname' => 'prd-woof-community.1f0vo6.0001.cnn1.cache.amazonaws.com.cn',
                'port' => 6379,
                'socketClientFlags' => STREAM_CLIENT_CONNECT
            ]
        ],
        'common_redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'prd-woof-community.1f0vo6.0001.cnn1.cache.amazonaws.com.cn',
            'port' => '6379',
            'database' => 5,
            'socketClientFlags' => STREAM_CLIENT_CONNECT
        ],
        'oauth2_redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'prd-woof-community.1f0vo6.0001.cnn1.cache.amazonaws.com.cn',
            'port' => '6379',
            'database' => 5,
            'socketClientFlags' => STREAM_CLIENT_CONNECT
        ],
        'queue' => [
            'class' => \yii\queue\amqp\Queue::class,
            'host' => '172.31.31.95',
            'port' => 5672,
            'user' => 'admin',
            'password' => '12345678_woof',
            'queueName' => 'queue'
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                //['http_address' => '127.0.0.1:9200'],
                ['http_address' => '172.31.31.95:9200'],
            ],
            'autodetectCluster' => false
        ],


        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
