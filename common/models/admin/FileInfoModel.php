<?php

namespace common\models\admin;

use Yii;

/**
 * This is the model class for table "file_info".
 *
 * @property string $fileId 文件md5标识
 * @property string $path path地址
 * @property int $sourceType 资源服务器供应商， 1:又拍云
 * @property string $url url地址
 * @property int $fileSize 文件大小
 * @property string $cTime 添加时间
 */
class FileInfoModel extends \yii\db\ActiveRecord
{
    const Y_P_YUN = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_info';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_admin');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fileId', 'path'], 'required'],
            [['sourceType', 'fileSize'], 'integer'],
            [['url'], 'string'],
            [['cTime'], 'safe'],
            [['fileId', 'path'], 'string', 'max' => 255],
            [['fileId'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fileId' => 'File ID',
            'path' => 'Path',
            'sourceType' => 'Source Type',
            'url' => 'Url',
            'fileSize' => 'File Size',
            'cTime' => 'C Time',
        ];
    }
}
