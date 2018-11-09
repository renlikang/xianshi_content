<?php

namespace api\actions\user;

use common\models\User;
use common\services\RetCode;
use http\Exception\BadQueryStringException;
use yii\rest\Action;
use yii\web\BadRequestHttpException;

class Login extends Action {

    public $modelClass = false;

    public function run() {
        $code = \Yii::$app->request->post('code');

        if (empty($code)) {
            throw new BadRequestHttpException("code 不能为空");
        }

        $session = self::code2Session($code);
        $openid = $session['openid'];
        @$unionid = $session['unionid'];
        $session_key = $session['session_key'];

        if ($user = User::findOne(["openid" => $openid])) {
            $isNewUser = ($user->nickName and $user->unionid) ? false : true;
            if ($user->unionid == null) {
                $user->unionid = $unionid;
            }
            $user->session_key = $session_key;
            $user->save();

            if ($user->errors) {
                \Yii::error("用户更新失败" . json_encode($user->errors), __CLASS__.'::'.__FUNCTION__);
            }
        } else {
            $isNewUser = true;
            $user = new User();
            $user->created_at = time();
            $user->updated_at = time();
            $user->openid = $openid;
            $user->unionid = $unionid;
            $user->session_key = $session_key;
            $user->save();

            if ($user->errors) {
                \Yii::error("用户创建失败" . json_encode($user->errors), __CLASS__.'::'.__FUNCTION__);
            }
        }

        $token = $session_key;
        $userInfo = [
            'id' => $user->id,
            'token' => $token,
            'is_new' => $isNewUser,
            'expire' => time() + 24*60*60
        ];

        foreach ($userInfo as $key => $value) {
            \Yii::$app->sessionCache->hset($token, $key, $value);
        }
        \Yii::$app->sessionCache->expire($token, 24*60*60);

        $userInfo['token'] = $token;
        return $userInfo;
    }

    public static function code2Session($code) {
        $appid = \Yii::$app->params['appid'];
        $appsecret = \Yii::$app->params['appsecret'];
        $client = new \GuzzleHttp\Client();
        $res = $client->request("GET", "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appsecret}&js_code={$code}&grant_type=authorization_code");
        $content = json_decode($res->getBody()->getContents(), true);
        if (isset($content["errcode"])) {
            \Yii::error("微信 API 返回错误:" . json_encode($content), __CLASS__.'::'.__FUNCTION__);
            throw new \Exception("微信 API 返回错误:" . json_encode($content));
        } else {
            \Yii::info("微信 API 返回成功:" . json_encode($content), __CLASS__.'::'.__FUNCTION__);
            return $content;
        }
    }
}