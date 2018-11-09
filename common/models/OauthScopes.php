<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oauth_scopes".
 *
 * @property string $scope
 * @property int $is_default
 */
class OauthScopes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_scopes';
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
            [['scope', 'is_default'], 'required'],
            [['is_default'], 'integer'],
            [['scope'], 'string', 'max' => 2000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scope' => 'Scope',
            'is_default' => 'Is Default',
        ];
    }
}
