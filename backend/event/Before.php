<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 12:00 PM
 */

namespace backend\event;

use common\models\admin\AdminModel;
use common\services\ApiException;
use common\services\OAuthService;
use common\services\RetCode;
use yii\base\Component;
use Yii;
use yii\base\Event;
use yii\web\ForbiddenHttpException;

class Before extends Component
{
    public static function index(Event $event)
    {
        self::checkRoute();
        return true;
    }

    /**
     * get access token from header
     * */
    public static function getBearerToken() {
        $headers = Yii::$app->request->headers->get('Authorization');
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public static function checkRoute()
    {
        $accessToken = self::getBearerToken() ?? Yii::$app->request->get('access_token');
        if($accessToken && Yii::$app->controller->id != 'oauth') {
            $redis = Yii::$app->oauth2_redis;
            $storage = new \OAuth2\Storage\Redis($redis);
            foreach (OAuthService::$map as $k => $v) {
                if ($v == 'redis') {
                    Yii::$container->set($k, $storage);
                }
            }

            $result = $storage->getAccessToken($accessToken);
            if (!isset($result['user_id'])) {
                throw new ApiException(RetCode::PARAM_ERROR);
            }

            $user = AdminModel::findOne($result['user_id']);
            Yii::$app->user->login($user);
            return true;
        }

        $controllerId = Yii::$app->controller->id;
        if($controllerId != 'files' && $controllerId != 'login' && $controllerId != 'oauth' && Yii::$app->getUser()->getIsGuest() == true) {
            throw new ForbiddenHttpException("必须登录用户才能访问");
        }
    }
}