<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/11/8
 * Time: 4:09 PM
 */

namespace api\actions\notices;

use common\services\content\NoticeService;
use common\services\RetCode;
use yii\rest\Action;
use Yii;

class WxFormCollection extends Action
{
    public $modelClass = false;

    /**
     * @SWG\Post(
     *     path="/notices/wx-form-collection",
     *     tags={"消息接口"},
     *     summary="收集formId",
     *     description="",
     *     produces={"application/json"},
     *     consumes = {"application/json"},
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "formId",description = "表单ID",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 400,description = " bad request"),
     * )
     */
    public function run()
    {
        if(Yii::$app->user->isGuest) {
            return RetCode::response(RetCode::SUCCESS);
        }

        $formId = Yii::$app->request->post('formId');
        (new NoticeService())->collectionFormId(Yii::$app->user->id, $formId);
        return RetCode::response(RetCode::SUCCESS);
    }
}