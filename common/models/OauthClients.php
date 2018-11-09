<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oauth_clients".
 *
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 * @property string $grant_types
 * @property string $scope
 * @property int $access_token_expires
 * @property int $refresh_token_expires
 * @property int $user_id
 * @property string $logout_uri 登出回跳地址
 */
class OauthClients extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_clients';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'redirect_uri', 'grant_types'], 'required'],
            [['access_token_expires', 'refresh_token_expires', 'user_id'], 'integer'],
            [['client_id', 'client_secret'], 'string', 'max' => 32],
            [['redirect_uri', 'logout_uri'], 'string', 'max' => 1000],
            [['grant_types'], 'string', 'max' => 100],
            [['scope'], 'string', 'max' => 2000],
            [['client_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'redirect_uri' => 'Redirect Uri',
            'grant_types' => 'Grant Types',
            'scope' => 'Scope',
            'access_token_expires' => 'Access Token Expires',
            'refresh_token_expires' => 'Refresh Token Expires',
            'user_id' => 'User ID',
            'logout_uri' => 'Logout Uri',
        ];
    }
}
