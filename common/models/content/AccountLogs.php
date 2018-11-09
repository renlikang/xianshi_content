<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "account_logs".
 *
 * @property int $id 记录Id
 * @property int $accountId 账户Id
 * @property array $log 记录内容
 * @property string $cTime 创建时间
 * @property string $uTime 更新时间
 */
class AccountLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_logs';
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
            [['accountId', 'log'], 'required'],
            [['accountId'], 'integer'],
            [['log', 'cTime', 'uTime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'accountId' => 'Account ID',
            'log' => 'Log',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
        ];
    }
}
