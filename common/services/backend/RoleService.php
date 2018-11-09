<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/31
 * Time: 11:04 AM
 */

namespace common\services\backend;

use common\models\admin\RoleAdminModel;
use common\models\admin\RoleModel;
use Yii;
use yii\helpers\Json;

class RoleService
{
    const ADMIN = 'admin';
    const OPERATION = 'operation';

    /**
     * 创建角色
     * @param $roleName
     * @return bool
     */
    public function createRole($roleName)
    {
        if(RoleModel::findOne($roleName)) {
            return true;
        }
        $model = new RoleModel;
        $model->roleName = $roleName;
        if(!$model->save()) {
            Yii::error(Json::encode($model->errors), __CLASS__."::".__FUNCTION__ . "::".__LINE__);
            return false;
        }

        return true;
    }

    /**
     * 角色管理员映射表
     * @param $roleName
     * @param $adminId
     * @return bool
     */
    public function mapRoleAdmin($roleName, $adminId)
    {
        $this->createRole($roleName);
        if(RoleAdminModel::find()->where(['roleName' => $roleName, 'aid' => $adminId])->exists()) {
            return true;
        }

        $model = new RoleAdminModel;
        $model->roleName = $roleName;
        $model->aid = $adminId;
        if(!$model->save()) {
            Yii::error(Json::encode($model->errors), __CLASS__."::".__FUNCTION__ . "::".__LINE__);
            return false;
        }

        return true;
    }

    /**
     * 删除角色管理员映射表
     * @param $roleName
     * @param $adminId
     * @return bool
     */
    public function cancelMapRoleAdmin($roleName, $adminId)
    {
        RoleAdminModel::deleteAll(['roleName' => $roleName, 'aid' => $adminId]);
        return true;
    }

    /**
     * 是否操作员
     * @param $aid
     * @return bool
     */
    public static function isOperation($aid)
    {
        return RoleAdminModel::find()->where(['roleName' => self::OPERATION, 'aid' => $aid])->exists();
    }
}