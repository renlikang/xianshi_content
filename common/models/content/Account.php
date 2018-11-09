<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id 账户Id
 * @property int $userId 用户Id
 * @property int $decibels 分贝账户
 * @property int $superPills 特殊药丸账户
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_content');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId'], 'required'],
            [['userId', 'decibels', 'superPills'], 'integer'],
            [['userId'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'decibels' => 'Decibels',
            'superPills' => 'Super Pills',
        ];
    }
}
