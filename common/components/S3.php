<?php
/**
 * @author rlk
 */

namespace common\components;

use Aws\S3\S3Client;
use Aws\Sdk;
use fedemotta\awssdk\AwsSdk;
use yii\base\Component;
use Yii;


class S3 extends Component
{
    public $bucketName;
    public $domain;
    public function init()
    {
        if(YII_ENV == YII_ENV_PROD) {
            $this->bucketName = "contentWoof";
        } else {
            $this->bucketName = "testcontentwoof";
        }

        $this->domain = Yii::$app->id . "/" . date("Ym") . '/';
    }


    /**
     * 创建令牌桶
     * @param $bucketName
     * @return bool
     */
    public function createBucket($bucketName)
    {
        /** @var Sdk $aws */
        $aws = Yii::$app->awssdk->getAwsSdk();
        $client = $aws->createS3();
        $result = $client->createBucket(['Bucket' => $bucketName]);
        if(isset($result['@metadata']['statusCode']) && $result['@metadata']['statusCode'] == 200) {
            return true;
        } else {
            Yii::error('bucket没有创建成功', __CLASS__."::".__FUNCTION__);
            return false;
        }
    }

    /**
     * 简单文件上传（s3）
     * @param $filePath
     * @param $fileName
     * @return bool|mixed|null
     */
    public function upload($filePath, $fileName)
    {
        /** @var Sdk $aws */
        $aws = Yii::$app->awssdk->getAwsSdk();
        /** @var S3Client $client */
        $client = $aws->createS3();
        $result = $client->putObject([
            'Bucket' => $this->bucketName,
            'Key'    => $this->domain . $fileName,
            'Body'   => fopen($filePath, 'r'),
            'ACL'    => 'public-read',
        ]);

        return $result->get('ObjectURL') ?? false;
    }
}