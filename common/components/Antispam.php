<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/25
 * Time: 9:11 PM
 */

namespace common\components;

use yii\base\Component;
use yii\helpers\Json;
use Yii;

class Antispam extends Component
{
    public $secretId;
    public $secretKey;
    public $businessId;
    public $apiUrl;

    const VERSION = 'v3.2';
    const API_TIMEOUT = 10;
    const INTERNAL_STRING_CHARSET = "auto";
    /**
     * 计算参数签名
     * $params 请求参数
     * $secretKey secretKey
     */
    private function gen_signature($secretKey, $params)
    {
        ksort($params);
        $buff="";
        foreach($params as $key=>$value){
            if($value !== null) {
                $buff .=$key;
                $buff .=$value;
            }
        }
        $buff .= $secretKey;
        return md5($buff);
    }
    /**
     * 将输入数据的编码统一转换成utf8
     * @params 输入的参数
     */
    public function toUtf8($params)
    {
        $utf8s = array();
        foreach ($params as $key => $value) {
            $utf8s[$key] = is_string($value) ? mb_convert_encoding($value, "utf8", self::INTERNAL_STRING_CHARSET) : $value;
        }
        return $utf8s;
    }
    /**
     * 反垃圾请求接口简单封装
     * $params 请求参数
     */
    private function check($params)
    {
        $params["secretId"] = $this->secretId;
        $params["businessId"] = $this->businessId;
        $params["version"] = self::VERSION;
        $params["timestamp"] = sprintf("%d", round(microtime(true)*1000));// time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int
        $params = $this->toUtf8($params);
        $params["signature"] = $this->gen_signature($this->secretKey, $params);
        $options = array(
            "http" => array(
                "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
                "method"  => "POST",
                "timeout" => self::API_TIMEOUT, // read timeout in seconds
                "content" => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($this->apiUrl, false, $context);
        if($result === FALSE){
            return array("code"=>500, "msg"=>"file_get_contents failed.");
        }else{
            return Json::decode($result, true);
        }
    }


    public function filter($url)
    {
        //echo "mb_internal_encoding=".mb_internal_encoding()."\n";
        $images = array();
        array_push($images, array(// type=1表示传图片url检查
            "name" => $url,
            "type" => 1,
            "data" => $url,
        ));

        if(Yii::$app->user->isGuest) {
            $account = 'guest';
        } else {
            $account = Yii::$app->user->id;
        }

        $params = [
            "images"=>Json::encode($images),
            "account"=> $account,
            "ip"=> Yii::$app->request->userIP
        ];

        $ret = $this->check($params);
        if ($ret["code"] == 200) {
            $result = $ret["result"];
            foreach($result as $index => $image_ret){
//                $name = $image_ret["name"];
//                $taskId = $image_ret["taskId"];
//                $status = $image_ret["status"];
//                $labelArray = $image_ret["labels"];
//                echo "taskId={$taskId}，status={$status}，name={$name}，labels:\n";
                $maxLevel=-1;
                foreach($image_ret["labels"] as $index=>$label){
                    $maxLevel=$label["level"]>$maxLevel?$label["level"]:$maxLevel;
                }

                return $maxLevel;
//                if($maxLevel==0){
//                    echo "#图片机器检测结果：最高等级为：正常\n";
//                }else if($maxLevel==1){
//                    echo "#图片机器检测结果：最高等级为：嫌疑\n";
//                }else if($maxLevel==2){
//                    echo "#图片机器检测结果：最高等级为：确定\n";
//                }
            }
        }else{
            Yii::error(Json::encode($ret), __CLASS__.'::'.__FUNCTION__);
        }
    }

    public function filterText($txt)
    {
        if(Yii::$app->user->isGuest) {
            $account = 'guest';
        } else {
            $account = Yii::$app->user->id;
        }

        $dataId = time() . rand(10, 99);
        $params = array(
            "dataId"=> $dataId,
            "content"=> $txt,
            "dataType"=>"1",
            "ip"=> Yii::$app->request->userIP,
            "account"=> $account,
            "publishTime"=>round(microtime(true)*1000)
        );

        $ret = $this->check($params);
        if ($ret["code"] == 200) {
            $action = $ret["result"]["action"];
            return $action;
//            $taskId = $ret["result"]["taskId"];
//            $labelArray = $ret["result"]["labels"];
//            if ($action == 0) {
//                echo "taskId={$taskId}，文本机器检测结果：通过\n";
//            } else if ($action == 1) {
//                echo "taskId={$taskId}，文本机器检测结果：嫌疑，需人工复审，分类信息如下：".Json::encode($labelArray)."\n";
//            } else if ($action == 2) {
//                echo "taskId={$taskId}，文本机器检测结果：不通过，分类信息如下：".Json::encode($labelArray)."\n";
//            }
        }else{
            Yii::error(Json::encode($ret), __CLASS__.'::'.__FUNCTION__);
        }
    }
}