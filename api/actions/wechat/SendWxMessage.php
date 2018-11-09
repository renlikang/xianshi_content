<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/11/8
 * Time: 6:07 PM
 */

namespace api\actions\wechat;

use common\models\elasticsearch\WxForm;
use common\models\User;
use common\services\content\NoticeService;
use common\services\RetCode;
use GuzzleHttp\Client;
use linslin\yii2\curl\Curl;
use yii\helpers\Json;
use yii\rest\Action;
use Yii;

class SendWxMessage extends Action
{
    public $modelClass = false;

    /**
     * @SWG\Post(
     *     path="/wechat/send-wx-message",
     *     tags={"小程序高级功能"},
     *     summary="发送消息",
     *     description="",
     *     produces={"application/json"},
     *     consumes = {"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "uid",description = "用户Id",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 400,description = " bad request"),
     * )
     */
    public function run()
    {
        return true;
        $uid = Yii::$app->request->post('uid');
        /** @var WxForm $data */
        $data = (new NoticeService())->pushFormId($uid);
        if(!$data) {
            return RetCode::response(RetCode::ERROR);
        }

        $user = User::findOne($uid);
        $formId = $data->form_key;
        $openId = $data->open_id;
        $data = [];
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $this->controller->getAccessToken();
        $data['touser'] = $openId;
        $data['template_id'] = "tmlIDv2WB34xGAtozKDFd6UbcJmBPzJVhATpYOlqA3M";
        $data['form_id'] = $formId;
        $keyword['keyword1']['value'] = $user->nickName;
        $keyword['keyword2']['value'] = "测试";
        $keyword['keyword3']['value'] = "没有奖品";
        $data['data'] = $keyword;
        $curl = new Curl();
        $aaa = $curl->reset()->setHeaders(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'charset' => 'UTF-8',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
            ]
        )->setOption(
            CURLOPT_POSTFIELDS,
            json_encode($data)
        )->post($url);
        var_dump($curl->response);
        $response = Json::decode($curl->response, true);
        if($response['errcode'] == 0) {
            $model = WxForm::findOne($formId);
            if($model) {
                $model->delete();
            }
        }

        return true;
    }
}