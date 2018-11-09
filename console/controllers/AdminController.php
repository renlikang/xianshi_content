<?php
/**
 * @author rlk
 */
namespace console\controllers;

use common\components\Helper;
use common\models\admin\AdminModel;
use common\models\content\ArticleModel;
use common\services\backend\RoleService;
use yii\console\Controller;
use yii\helpers\Json;

class AdminController extends Controller
{
    public function actionSetAdmin($username, $roleName)
    {
        $password = Helper::autoGeneratePassword($username);
        $model = AdminModel::findOne(['username' => $username]);
        if($model) {
            if($model->save()) {
                echo $password;
                return true;
            }
        }
        $model = new AdminModel;
        $model->username = $username;
        $model->setPassword($password);
        $model->status = AdminModel::STATUS_ACTIVE;
        if($model->save()) {
            (new RoleService())->mapRoleAdmin($roleName, $model->aid);
        }

        echo $password;
    }



    public function actionDataUpdate()
    {
        /** @var ArticleModel[] $model */
        $model = ArticleModel::find()->all();
        foreach ($model as $k => $v) {
            $data = [];
            if($v->covers) {
                foreach ($v->covers as $kk => $vv) {
                    $data[$kk]['url'] = $vv;
                    $data[$kk]['type'] = 'image';
                    $data[$kk]['previewImage'] = '';
                }

                $v->covers = $data;
                $v->save();
            }
        }
    }
}