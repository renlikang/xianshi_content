<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'awssdk' => [
            'class' => 'fedemotta\awssdk\AwsSdk',
            'credentials' => [ //you can use a different method to grant access
                'key' => 'AKIARNR6TIB6C2HPIJ2P',
                'secret' => '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',
            ],
            'region' => 'cn-north-1', //i.e.: 'us-east-1'
            'version' => 'latest', //i.e.: 'latest'
        ],

        's3' => [
            'class' => 'common\components\S3',
        ],

        'antispam' => [
            'class' => 'common\components\Antispam',
            'secretId' => '4f5e7581ee809d84f7865fb188ed723e',
            'secretKey' => 'e42803a702e0638e29abd9f3214c099e',
            'businessId' => '3aedc65277d98892db529230a4084388',
            'apiUrl' => 'https://as.dun.163yun.com/v3/image/check',
        ],
        'antispam_txt' => [
            'class' => 'common\components\Antispam',
            'secretId' => '4f5e7581ee809d84f7865fb188ed723e',
            'secretKey' => 'e42803a702e0638e29abd9f3214c099e',
            'businessId' => 'bcbc83bc8cd0c6f506f5946b488425ef',
            'apiUrl' => 'https://as.dun.163yun.com/v3/text/check',
        ],
    ],
];
