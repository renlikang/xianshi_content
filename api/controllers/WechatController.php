<?php

namespace api\controllers;

use GuzzleHttp\Client;
use yii\helpers\Json;

/**
 * @SWG\Post(
 *     path="/wechat/getWXACodeUnlimit",
 *     tags={"小程序高级功能"},
 *     summary="生成二维码",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "formData",name = "scene",description = "最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）",required = true, type = "string"),
 *     @SWG\Parameter(in = "formData",name = "page",description = "必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index, 根路径前不要填加 /,不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 * )
 */
class WechatController extends \yii\rest\Controller {
    public function actions()
    {
        return [
            'getWXACodeUnlimit' => 'api\actions\wechat\GetWXACodeUnlimit',
            'send-wx-message' => 'api\actions\wechat\SendWxMessage',
        ];
    }


    public  function getAccessToken()
    {
        $appId = \Yii::$app->params['appid'];
        $appSecret = \Yii::$app->params['appsecret'];

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appId}&secret={$appSecret}";

        $client = new Client();
        $response = $client->get($url);
        $result = Json::decode($response->getBody()->getContents(), true);
        return $result['access_token'];
    }
}