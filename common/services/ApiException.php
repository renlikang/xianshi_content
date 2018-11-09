<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 5:01 PM
 */

namespace common\services;
use Yii;

class ApiException extends \Exception
{
    public function __construct($code = RetCode::ERROR, $msg = '', $params = [])
    {
        if (isset(RetCode::$responseMsg[$code]) && !$msg)  {
            $msg = RetCode::getErrMsgByCode($code, $params);
        }

        header('Content-Type: application/json');
        //CORS
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        header("Access-Control-Allow-Origin: {$origin}");
        header("Access-Control-Allow-Headers: {$origin}");
        header("Access-Control-Allow-Credentials: true");
        echo json_encode(['retCode' => $code, 'retMsg' => $msg], JSON_FORCE_OBJECT);
        //添加日志
        $traceArr = $this->getTrace();
        if (!empty($traceArr[0]['class']) && !empty($traceArr[0]['function']))  {
            $logPrefix = $traceArr[0]['class'] . '::' . $traceArr[0]['function'];
        } else  {
            $logPrefix = 'backend\controller\\' . 'ApiException';
        }

        $log = "type[ApiException] code [{$code}] msg[{$msg}]";
        Yii::info($log, $logPrefix);
        exit(0);
    }
}