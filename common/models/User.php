<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use linslin\yii2\curl\Curl;

/**
 * This is the model class for table "user".
 *
 * @property int $id 用户Id
 * @property resource $nickName 用户昵称
 * @property int $type 用户类型 1:普通用户 2:媒体用户
 * @property string $avatarUrl 用户头像图片的 URL
 * @property int $gender 性别（0 未知 1 男性 2 女性）
 * @property string $country 用户所在国家
 * @property string $province 用户所在城市
 * @property string $city 用户所在城市
 * @property string $language 语言（en 英文 zh_CN 简体中文 zh_TW 繁体中文）
 * @property string $birthday 生日
 * @property resource $signature 用户签名
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $session_key
 * @property string $openid
 * @property string $unionid
 * @property int $status 用户状态:1正常，2禁言
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class User extends ActiveRecord implements IdentityInterface
{
    //const STATUS_DELETED = 0;
    //const STATUS_ACTIVE = 10;
    const BANNED = 2;
    const NORMAL = 1;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDb()
    {
        return Yii::$app->get('db_content');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'gender', 'created_at', 'updated_at', 'status', 'deleteFlag'], 'integer'],
            [['birthday'], 'safe'],
            [['created_at', 'updated_at', 'session_key', 'openid'], 'required'],
            [['nickName', 'avatarUrl', 'signature'], 'string', 'max' => 255],
            [['country', 'province', 'city', 'language', 'session_key', 'openid', 'unionid'], 'string', 'max' => 32],
            [['nickName', 'avatarUrl', 'gender', 'country', 'province', 'city', 'language', 'birthday', 'signature'], 'safe', 'on' => 'update']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $id = Yii::$app->sessionCache->hget($token, 'id');
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function beforeSave($insert)
    {
        if(!$this->openid) {
            $this->openid = 'none';
        }

        if (!empty($this->avatarUrl) && substr($this->avatarUrl, 0, strlen("https://static.heywoof.com")) != "https://static.heywoof.com") {
            if(strstr($this->avatarUrl, 'https')) {
                $curl = new Curl;
                $curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
                $curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
                $this->avatarUrl = Yii::$app->upYun->uploadContent($curl->get($this->avatarUrl), md5($this->avatarUrl) . '.jpg');
            } else {
                $this->avatarUrl = Yii::$app->upYun->uploadContent(file_get_contents($this->avatarUrl), md5($this->avatarUrl) . '.jpg');
            }
        }

        return parent::beforeSave($insert);
    }
}
