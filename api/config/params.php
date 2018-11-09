<?php
return [
    'adminEmail' => 'admin@example.com',
    'apidoc'=>[
        'scan_dir'=>[
            'api/controllers',
            'api/modules/v1/controllers',
            'api/actions',
        ]
    ],

    'permissionRoute' => [
        'guest' => [
            'article' => ['list', 'detail', 'comment-list', 'forward'],
            'files' => ['*'],
            'site' => ['*'],
            'user' => ['login', 'access-token'],
            'author' => ['detail', 'comment-list', 'praise-list', 'attention-list', 'fans-list'],
            'my' => ['attention-ids', 'praise-ids'],
            'tag' => ['*'],
            'index' => ['*'],
            'wechat' => ['getWXACodeUnlimit', 'send-wx-message'],
            'notices' => ['wx-form-collection'],
        ],
    ],

    'banner' => [
        'imgUrl' => 'https://static.heywoof.com/6b87d1ca/9ffef5c8ef4e702397d6122c2f5d87fa.png',
        'jump' => [
            'type' => 'miniApp',
            'appId' => 'string',
            'link' => 'pages/topic/index?tagName=我的滑板鞋',
        ],

        'orderId' => 2,
    ],
];