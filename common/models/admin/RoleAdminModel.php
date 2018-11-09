<?php

namespace common\models\admin;

use Yii;

/**
 * This is the model class for table "role_admin".
 *
 * @property string $roleName 角色名称
 * @property int $aid 管理者ID
 */
class RoleAdminModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_admin';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_admin');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['roleName', 'aid'], 'required'],
            [['aid'], 'integer'],
            [['roleName'], 'string', 'max' => 255],
            [['roleName', 'aid'], 'unique', 'targetAttribute' => ['roleName', 'aid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'roleName' => 'Role Name',
            'aid' => 'Aid',
        ];
    }
}
