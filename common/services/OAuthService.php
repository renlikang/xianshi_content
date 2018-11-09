<?php
/**
 * @author rlk
 */

namespace common\services;

use common\models\OauthClients;
use yii\base\Component;

class OAuthService extends Component
{
    const AUTHORIZATION_CODE = 'authorization_code';
    const REFRESH_TOKEN = 'refresh_token';
    const RESPONSE_TYPE_CODE = 'code';
    const TAMP_CODE_EXPIRES = 300;

    public static $map = [
        'access_token' => 'redis',
        'authorization_code' => 'redis',
        'refresh_token' => 'redis',
    ];

    /**
     * 创建OAuth2客户端
     * @param $client_id
     * @param $redirect_uri
     * @param $client_secret
     * @param string $grant_types
     * @return bool
     */
    public static function createClient($client_id, $redirect_uri, $client_secret,
                                        $grant_types = self::AUTHORIZATION_CODE . ' ' . self::REFRESH_TOKEN)
    {
        if (!self::validateClient($client_id, $redirect_uri, $client_secret)) {
            return false;
        }

        $model = OauthClients::findOne($client_id);
        if (!$model) {
            $model = new OauthClients;
        }

        $model->client_id = $client_id;
        $model->redirect_uri = $redirect_uri;
        $model->client_secret = $client_secret;
        $model->grant_types = $grant_types;
        return $model->save();
    }

    /**
     * 获取client的list
     * @param array $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getClientList($params = [])
    {
        $model = OauthClients::find();
        foreach ($params as $k => $v)
        {
            $model->where([$k => $v]);
        }

        return $model->all();
    }

    public static function validateClient($client_id, $redirect_uri, $client_secret)
    {
        if ($client_id && $redirect_uri && $client_secret)
        {
            return true;
        }

        return false;
    }

    public static function formatUserByScope($user, $scope = null)
    {
        $scopeFields = ['uid', 'userName', 'realName', 'gender', 'email', 'mobile', 'workWeixinAccount']; //允许返回的字段
        foreach ($user as $key => $val)
        {
            if (!in_array($key, $scopeFields))
            {
                unset($user->$key);
            }
        }
        return $user;
    }

    /**
     * @param $clientId
     * @return OauthClients
     */
    public static function getClientById($clientId)
    {
        return OauthClients::findOne(['client_id' => $clientId]);
    }
}