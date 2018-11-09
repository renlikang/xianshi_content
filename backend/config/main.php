<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'backend\controllers',
    'modules' => [
        'apidoc' => [
            'class' => 'tanghengzhi\apidoc\Module',
        ],
        //other modules .....
        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'grantTypes' => [
                'authorization_code' => [
                    'class' => 'OAuth2\GrantType\AuthorizationCode',
                ],

                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ]
            ]
        ]
    ],

    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'common\models\admin\AdminModel',
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'authTimeout' => 2592000,
            'enableSession' => true,
        ],

        'session' => [
            'class' => 'yii\web\CacheSession',
            'timeout' => 2592000,
            'cookieParams' => [
                'httponly' => true,
            ],
            'cache' => 'sessionCache',
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'article-fake-praise'],
            ],
        ],

        'upYun' => [
            'class' => 'common\components\upYunComponent',
            'serviceName' => 'woof-community',
            'operatorName' => 'woofcontentadmin',
            'operatorPwd' => '1234qwer',
        ],

    ],
    'params' => $params,
];
