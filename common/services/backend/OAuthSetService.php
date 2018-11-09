<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 6:15 PM
 */

namespace common\services\backend;


use common\components\Helper;
use common\models\admin\AdminModel;
use common\models\OauthClients;

class OAuthSetService
{
    public static function create($client_id, $redirect_uri, $access_token_expires = 28800, $refresh_token_expires = 86400)
    {
        if(OauthClients::findOne($client_id)) {
            return false;
        }

        $user = new AdminModel;
        $user->username = $client_id;
        $user->password = $password = Helper::autoGeneratePassword($client_id);
        $user->status = AdminModel::STATUS_ACTIVE;
        $user->type = 2;
        if(!$user->save()) {
            return false;
        }

        $model = new OauthClients;
        $model->client_id = $client_id;
        $model->client_secret = md5($client_id);
        $model->redirect_uri = $redirect_uri;
        $model->grant_types = "client_credentials authorization_code password implicit refresh_token";
        $model->user_id = $user->aid;
        $model->access_token_expires = $access_token_expires;
        $model->refresh_token_expires = $refresh_token_expires;
        if(!$model->save()) {
            return false;
        }

        return OauthClients::findOne($client_id);
    }
}