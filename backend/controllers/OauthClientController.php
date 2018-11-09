<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 10:34 PM
 */

namespace backend\controllers;

use common\services\backend\OAuthSetService;
use common\services\RetCode;
use yii\rest\Controller;
use Yii;

/**
 * @SWG\Post(
 *     path="/oauth-client/create",
 *     tags={"OAuth2"},
 *     summary="申请 client_id 和 client_secret（需要管理员权限）",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "formData",name = "clientId",description = "Client Id", required = true, type = "string"),
 *     @SWG\Parameter(in = "formData",name = "redirect_uri",description = "跳转地址", default = "https://www.heywoof.com", required = true, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 * )
 */
class OauthClientController extends Controller
{
    public function actionCreate()
    {
        $clientId = Yii::$app->request->post('clientId');
        $redirect_uri = Yii::$app->request->post('redirect_uri');
        if($ret = OAuthSetService::create($clientId, $redirect_uri)) {
            return RetCode::response(RetCode::SUCCESS, $ret);
        }

        return RetCode::response(RetCode::ERROR);
    }
}