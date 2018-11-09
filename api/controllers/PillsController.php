<?php

namespace api\controllers;

use common\models\User;
use common\services\api\PillServices;
use common\services\RetCode;
use yii\rest\Controller;

class PillsController extends Controller {
    /**
     * @SWG\Get(
     *     path="/pills/balance",
     *     tags={"药丸接口"},
     *     summary="获取药丸账户余额",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "userId",description = "用户Id（不填则默认获取自己的药丸账户余额）",required = false, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     */
    public function actionBalance() {
        if ($userId = \Yii::$app->request->get('userId')) {
            $unionId = User::findOne(['id' => $userId])->unionid;
        } else {
            $unionId = \Yii::$app->user->identity->unionid;
        }

        return PillServices::getBalance($unionId);
    }

    /**
     * @SWG\Put(
     *     path="/pills/change-balance",
     *     tags={"药丸接口"},
     *     summary="增加药丸账户余额",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "operationType",description = "daily-praise 日常点赞奖励 daily-comment 日常评论奖励 daily-forward 日常转发奖励",required = true, type = "string"),
     *     @SWG\Parameter(in = "formData",name = "number",description = "数量",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     */
    public function actionChangeBalance() {
        $unionId = \Yii::$app->user->identity->unionid;
        $operationType = \Yii::$app->request->post('operationType');
        $number = \Yii::$app->request->post('number');

        return PillServices::changeBalance($unionId, $operationType, $number);
    }

    public function afterAction($action, $result)
    {
        return RetCode::response(200, $result);
    }
}