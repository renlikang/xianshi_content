<?php
/**
 *
 * @author  rlk
 */

namespace common\services;

use common\lib\RetCode;
use common\lib\UtilLib;
use common\models\admin\FileInfoModel;
use common\models\Attachment;
use common\models\AttachmentMap;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class FileServices extends Component
{
    public function upload($postFileName, $fileName = '')
    {
        $model = UploadedFile::getInstanceByName($postFileName);
        if ($fileName) {
            $fileName .= '_' . date('Ymd_His_') . mt_rand(10,99) . "." . $model->extension;
        } else {
            $fileName = md5(time() . mt_rand(10, 99)) . "." . $model->extension;
        }

        $tmpFilePath = '/tmp/' . $fileName;
        $model->saveAs($tmpFilePath);
        $fileMd5 = md5_file($tmpFilePath);
        if($fileMd5Model = FileInfoModel::findOne($fileMd5)) {
            return $fileMd5Model->url;
        }

        //var_dump(md5_file($tmpFilePath));exit;
        if(is_file($tmpFilePath)) {
            return Yii::$app->upYun->upload($tmpFilePath, $fileName);
            //return Yii::$app->s3->upload($tmpFilePath, $fileName);
        } else {
            Yii::error('file not save', __CLASS__."::".__FUNCTION__);
            return false;
        }
    }

    public function safeUploadImg($postFileName, $fileName = '')
    {
        $model = UploadedFile::getInstanceByName($postFileName);
        if ($fileName) {
            $fileName .= '_' . date('Ymd_His_') . mt_rand(10,99) . "." . $model->extension;
        } else {
            $fileName = md5(time() . mt_rand(10, 99)) . "." . $model->extension;
        }

        $tmpFilePath = '/tmp/' . $fileName;
        $model->saveAs($tmpFilePath);
        if(is_file($tmpFilePath)) {
            return Yii::$app->upYun->safeUploadImg($tmpFilePath, $fileName);
            //return Yii::$app->s3->upload($tmpFilePath, $fileName);
        } else {
            Yii::error('file not save', __CLASS__."::".__FUNCTION__);
            return false;
        }
    }

    public function policy($postFileName)
    {
        $file = pathinfo($postFileName);
        $fileName = md5(time() . mt_rand(10, 99)) . "." . $file['extension'];
        return Yii::$app->upYun->policy($fileName);

    }
}