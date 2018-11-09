<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 4:37 PM
 */

namespace backend\controllers;


use common\models\OauthClients;
use common\services\OAuthService;
use common\services\ApiException;
use common\services\RetCode;
use yii\rest\Controller;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * @SWG\Post(
 *     path="/oauth/authorization-code",
 *     tags={"OAuth2"},
 *     summary="获取授权码",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "formData",name = "client_id",description = "Client Id",required = true, type = "string"),
 *     @SWG\Parameter(in = "formData",name = "client_secret",description = "Client Secret",required = true, type = "string"),
 *     @SWG\Parameter(in = "formData",name = "state",description = "自定义字符串",required = true, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 * )
 *
 * @SWG\Post(
 *     path="/oauth/access-token",
 *     tags={"OAuth2"},
 *     summary="获取AccessToken",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "Basic 认证", default = "Basic {base64_encode(client_id:client_secret)}", required = true, type = "integer"),
 *     @SWG\Parameter(in = "formData",name = "code",description = "授权码",required = true, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 * )
 *
 * @SWG\Post(
 *     path="/oauth/refresh-access-token",
 *     tags={"OAuth2"},
 *     summary="刷新AccessToken",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "header",name = "Authorization",description = "Basic 认证", default = "Basic {base64_encode(client_id:client_secret)}", required = true, type = "integer"),
 *     @SWG\Parameter(in = "formData",name = "refresh_token",description = "授权码",required = true, type = "string"),
 *     @SWG\Response(response = 200,description = " success"),
 * )
 */
class OauthController extends Controller
{
    /**
     * @var $oauthModule \filsh\yii2\oauth2server\Module
     */
    private $oauthModule;
    /**
     * @var $storage \OAuth2\Storage\Redis
     */
    private $storage;


    public function init()
    {
        $redis = Yii::$app->oauth2_redis;
        $this->storage = new \OAuth2\Storage\Redis($redis);
        foreach (OAuthService::$map as $k => $v)
        {
            if ($v == 'redis')
            {
                Yii::$container->set($k, $this->storage);
            }
        }

        $this->oauthModule = Yii::$app->getModule('oauth2');
    }

    /**
     * 获取授权码
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionAuthorizationCode()
    {
        $_POST['grant_type'] = OAuthService::AUTHORIZATION_CODE;
        $_POST['response_type'] = OAuthService::RESPONSE_TYPE_CODE;
        $client_id     = Yii::$app->request->post('client_id');
        if ($client = OauthClients::findOne(['client_id' => $client_id])) {
            $user_id = $client->user_id;
            $redirect_uri = $client->redirect_uri;
        } else {
            throw new NotFoundHttpException("Client Id 不存在");
        }
        $scope         = Yii::$app->request->post('scope') ?? null;
        $server        = $this->oauthModule->getServer();
        $request       = \OAuth2\Request::createFromGlobals();
        $response      = new \OAuth2\Response();
        $server->handleAuthorizeRequest($request, $response, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
        $this->storage->setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, 300 + time(), $scope);
        return RetCode::response(RetCode::SUCCESS, ['code' => $code]);
    }

    /**
     * 通过Code换取accessToken
     * @return array
     * @throws ApiException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAccessToken()
    {
        $code = Yii::$app->request->post('code');
        $info = $this->storage->getAuthorizationCode($code);
        $_POST['redirect_uri'] = $info['redirect_uri'];
        $_POST['grant_type'] = OAuthService::AUTHORIZATION_CODE;
        $response = $this->oauthModule->getServer()->handleTokenRequest();
        $result = $response->getParameters();
        if (isset($result['access_token'])) {
            $client = OAuthService::getClientById($info['client_id']);
            if (!$client) {
                throw new ApiException(RetCode::PARAM_ERROR);
            }

            $this->storage->setAccessToken($result['access_token'], $info['client_id'], $info['user_id'],
                $client->access_token_expires + time(), $info['scope']);
            $this->storage->setRefreshToken($result['refresh_token'], $info['client_id'], $info['user_id'],
                $client->refresh_token_expires + time(), $info['scope']);
        }
        else
        {
            return RetCode::response(RetCode::ERROR, $result);

        }

        return RetCode::response(RetCode::SUCCESS, $result);
    }

    /**
     * 通过refreshToken换取accessToken
     * @return array
     * @throws ApiException
     */
    public function actionRefreshAccessToken()
    {
        $post = Yii::$app->request->post();
        $post['grant_type'] = OAuthService::REFRESH_TOKEN;
        Yii::$app->request->setBodyParams($post);
        $_POST['grant_type'] = OAuthService::REFRESH_TOKEN;
        $refresh_token = Yii::$app->request->post('refresh_token');
        $info = $this->storage->getRefreshToken($refresh_token);
        try  {
            $response = $this->oauthModule->getServer()->handleTokenRequest();
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __CLASS__.'::'.__FUNCTION__);
            return RetCode::response(RetCode::ERROR, [], [], $e->getMessage());
        }
        $result = $response->getParameters();
        if (isset($result['access_token']))
        {
            $client = OAuthService::getClientById($info['client_id']);
            if (!$client)
            {
                Yii::error($info, __CLASS__.'::'.__FUNCTION__);
                throw new ApiException(RetCode::PARAM_ERROR);
            }
            $this->storage->setAccessToken($result['access_token'], $info['client_id'], $info['user_id'],
                $client->access_token_expires + time(), $info['scope']);
            $this->storage->setRefreshToken($result['refresh_token'], $info['client_id'], $info['user_id'],
                $client->refresh_token_expires + time(), $info['scope']);
        } else {
            Yii::error($result, __CLASS__.'::'.__FUNCTION__);
            return RetCode::response(RetCode::ERROR, $result);

        }

        return RetCode::response(RetCode::SUCCESS, $result);
    }

    public function actionOrganization()
    {
        $accessToken = Yii::$app->request->post('access_token');
        if (!$accessToken) {
            throw new ApiException(RetCode::PARAM_ERROR);
        }

        $result = $this->storage->getAccessToken($accessToken);
        if (!isset($result['user_id'])) {
            throw new ApiException(RetCode::PARAM_ERROR);
        }
        $result = DepartmentService::getDeptTreeAndUser();
        return $this->response(RetCode::SUCCESS, $result);
    }


}