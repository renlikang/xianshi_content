<?php
ini_set('date.timezone','Asia/Shanghai');
$config = merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Headers: content-type, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET");
    return true;
} else {
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    header("Access-Control-Allow-Origin: {$origin}");
    header("Access-Control-Allow-Headers: content-type, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, GET");
}

(new yii\web\Application($config))->run();
function merge($a, $b)
{
    $args = func_get_args();
    $res = array_shift($args);
    while (!empty($args)) {
        $next = array_shift($args);
        foreach ($next as $k => $v) {
            if (is_int($k)) {
                if (array_key_exists($k, $res)) {
                    $res[] = $v;
                } else {
                    $res[$k] = $v;
                }
            } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                $res[$k] = merge($res[$k], $v);
            } else {
                $res[$k] = $v;
            }
        }
    }

    return $res;
}
