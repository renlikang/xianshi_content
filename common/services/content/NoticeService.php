<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/11/8
 * Time: 4:20 PM
 */

namespace common\services\content;

use common\models\elasticsearch\WxForm;
use common\models\User;

class NoticeService
{
    public function collectionFormId($uid, $formId)
    {
        $user = User::findOne($uid);
        if(!$user || !$user->openid) {
            return true;
        }

        if(WxForm::findOne($formId)) {
            return true;
        }
        $model = new WxForm;
        $model->primaryKey = $formId;
        $model->uid = $uid;
        $model->form_key = $formId;
        $model->open_id = $user->openid;
        $model->expiredTime = time() + 6 * 24 * 3600;
        $model->save();
        return true;
    }

    public function pushFormId($uid)
    {
        $model = WxForm::find()->where(['uid' => $uid]);
        $query['range']['expiredTime']['gt'] = time();
        $data = $model->query($query)->addOrderBy('cTime asc')->one();
        if(!$data) {
            return [];
        }

        return $data;
    }
}