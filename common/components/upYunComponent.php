<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/10
 * Time: 9:40 PM
 */

namespace common\components;

use common\models\admin\FileInfoModel;
use GuzzleHttp\Client;
use linslin\yii2\curl\Curl;
use Upyun\Signature;
use Upyun\Util;
use yii\base\Component;
use Upyun\Upyun;
use Upyun\Config;
use Yii;
use yii\helpers\Json;

class upYunComponent extends Component
{
    public $serviceName;
    public $operatorName;
    public $operatorPwd;
    /**
     * @var Upyun $client
     */
    private $client;
    /** @var Config $serviceConfig */
    private $serviceConfig;
    public $domain;

    public function init()
    {
        $this->serviceConfig = new Config($this->serviceName, $this->operatorName, $this->operatorPwd);
        $this->domain =  substr(md5(YII_ENV . Yii::$app->id . date("Ym")), 0, 8) . '/';
    }

    public function upload($path, $fileName)
    {
        $md5 = md5_file($path);
        if($url = $this->check($md5)) {
            return $url;
        }

        $this->client = new Upyun($this->serviceConfig);
        $file = fopen($path, 'r');
        $result = $this->client->write($this->domain . $fileName, $file);
        if(isset($result['x-upyun-content-length']) || isset($result['x-upyun-multi-uuid'])) {
            $this->filter($this->domain . $fileName);
            return "https://static.heywoof.com/" . $this->domain . $fileName;
        }

        Yii::error(Json::encode($result), __CLASS__.'::'.__FUNCTION__);
        return false;
    }

    public function safeUploadImg($path, $fileName)
    {
        $this->client = new Upyun($this->serviceConfig);
        $url = $this->upload($path, $fileName);
        $a = Yii::$app->antispam->filter($url);
    }

    public function uploadContent($content, $fileName, $rule='!tjpg')
    {
        $url = $this->check(md5($content));
        if($url) {
            return $url;
        }
        $this->client = new Upyun($this->serviceConfig);
        $result = $this->client->write($this->domain . $fileName, $content);
        if(isset($result['x-upyun-content-length'])) {
            $this->filter($this->domain . $fileName, "https://static.heywoof.com/" . $this->domain . $fileName . $rule);
            return "https://static.heywoof.com/" . $this->domain . $fileName . $rule;
        }

        return false;
    }

    public function policy($fileName)
    {
        $sign = substr(md5(rand(100, 999) . time()), 0, 28);
        $this->serviceConfig->setFormApiKey($sign);
        $data['save-key'] = "/" . $this->domain . $fileName;
        $data['expiration'] = time() + 120;
        $data['bucket'] = $this->serviceName;
        $policy = Util::base64Json($data);
        $method = 'POST';
        $uri = '/' . $this->domain . $fileName;
        $signature = Signature::getBodySignature($this->serviceConfig, $method, $uri, null, $policy);
        return Json::encode([
            'policy' => $policy,
            'authorization' => $signature,
        ]);
    }

    function sign($key, $secret, $method, $uri)
    {
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $md = md5($secret);
        // 上传，处理，内容识别有存储
        $sign = $this->signBase($key, $md, $method, $uri, $date);
        return [
            'Authorization' => $sign,
            'Date' => $date,
            ];
    }
    public function signBase($key, $secret, $method, $uri, $date, $policy=null, $md5=null)
    {
        $elems = [];
        foreach ([$method, $uri, $date, $policy, $md5] as $v) {
            if ($v) {
                $elems[] = $v;
            }
        }
        $value = implode('&', $elems);
        $sign = base64_encode(hash_hmac('sha1', $value, $secret, true));
        return 'UPYUN ' . $key . ':' . $sign;
    }

    public function check($md5)
    {
        $model = FileInfoModel::findOne($md5);
        if($model) {
            return $model->url;
        }

        return false;
    }

    public function filter($path, $url = '')
    {
        $fileInfo = $this->client->info($path, ['content-md5']);
        if(isset($fileInfo['content-md5'])) {
            $fileModel = FileInfoModel::findOne([$fileInfo['content-md5']]);
            if($fileModel) {
                return true;
            }
        }
        $model = new FileInfoModel;
        if(!$url) {
            $model->url = "https://static.heywoof.com/" . $path;
        } else {
            $model->url = $url;
        }

        $model->fileSize = $fileInfo['x-upyun-file-size'];
        $model->fileId = $fileInfo['content-md5'];
        $model->path = $path;
        if(!$model->save()) {
            Yii::error(Json::encode($model->errors), __CLASS__.'::'.__FUNCTION__);
        }

        return true;
    }

}