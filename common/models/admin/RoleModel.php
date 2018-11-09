<?php

namespace common\models\admin;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property string $roleName 角色名称
 * @property string $cTime 添加时间
 * @property string $uTime 更新时间
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class RoleModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role';
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
            [['roleName'], 'required'],
            [['cTime', 'uTime'], 'safe'],
            [['deleteFlag'], 'integer'],
            [['roleName'], 'string', 'max' => 255],
            [['roleName'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'roleName' => 'Role Name',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
            'deleteFlag' => 'Delete Flag',
        ];
    }
}
