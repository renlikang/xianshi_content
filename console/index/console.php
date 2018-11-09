<?php
/**
 * @author rlk
 */
ini_set('date.timezone','Asia/Shanghai');

$config = merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../../console/config/main.php'),
    require(__DIR__ . '/../../console/config/main-local.php')
);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../../console/config/bootstrap.php');

$application = new yii\console\Application($config);
$exitCode = $application->run();

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

exit($exitCode);