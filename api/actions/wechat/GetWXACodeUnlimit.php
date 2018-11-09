<?php

namespace api\actions\wechat;

use common\services\RetCode;
use GuzzleHttp\Client;
use yii\base\Action;
use yii\web\ServerErrorHttpException;

class GetWXACodeUnlimit extends Action {

    public $modelClass = false;

    public function run() {
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $this->controller->getAccessToken();
        $client = new Client();
        $response = $client->post($url, [
            'json' => \Yii::$app->request->get()
        ]);
        $result = $response->getBody()->getContents();

        if ($response->getHeader('Content-Type') == ['application/json; charset=UTF-8']) {
            \Yii::error("生成二维码失败", __CLASS__ . "::" . __FUNCTION__ . "::" . __LINE__);
            throw new ServerErrorHttpException("生成二维码失败");
        } else {
            header("Content-Type: image/png");
            echo $result;
            exit();
        }
    }


}