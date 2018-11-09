<?php
if (YII_ENV == 'dev') {
    return [
        'appid' => 'wx2578a6d12c378998',
        'appsecret' => '8620fea389cd9a8864a11b33a7d65faf'
    ];
} elseif (YII_ENV == 'test') {
    return [
        'appid' => 'wx20db3ea15dffdbdb',
        'appsecret' => '502c5468c3b1bfdbcada23b815e10fc0'
    ];
} elseif (YII_ENV == 'prod') {
    return [
        'appid' => 'wx3bd2c87aa7eb526c',
        'appsecret' => '5122d4cfd3429921f242cc06e8b3a1f9'
    ];
}
