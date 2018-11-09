<?php
/**
 * @author rlk
 */

namespace backend\controllers;

use common\services\FileServices;
use common\services\RetCode;
use yii\rest\Controller;
use Yii;
use yii\web\ForbiddenHttpException;

class FilesController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/files/upload",
     *     tags={"基础功能"},
     *     summary="文件上传",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "file",description = "上传文件(文档不支持上传，请使用postman)",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 100,description = " 文件上传失败"),
     * )
     *
     */
    public function actionUpload()
    {
        $url = (new FileServices)->upload('file');
        if($url) {
            return RetCode::response(RetCode::SUCCESS, ['url' => $url]);
        }

        return RetCode::response(RetCode::FILE_UPLOAD_FAIL);
    }
}