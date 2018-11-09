<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
defined('YII_ENV_DEV') or define('YII_ENV_DEV', false);
defined('YII_ENV_PROD') or define('YII_ENV_PROD', true);

return [
    'components' => [
        'db_admin' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;port=3307;dbname=woof_admin',
            'username' => 'heywoofprod',
            'password' => 'zaq12wsxprod!',
            'charset' => 'utf8mb4',
        ],
        'db_content' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;port=3307;dbname=woof_content',
            'username' => 'heywoofprod',
            'password' => 'zaq12wsxprod!',
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
        'common_redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => '6379',
            'database' => 5,
            'socketClientFlags' => STREAM_CLIENT_CONNECT
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
