<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property resource $tagName 标签名称
 * @property resource $realTagName 真实标签名称(用于搜索)
 * @property string $headImg 头图
 */
class TagModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_content');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tagName', 'realTagName'], 'required'],
            [['tagName', 'headImg', 'realTagName'], 'string', 'max' => 255],
            [['tagName'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tagName' => 'Tag Name',
            'headImg' => 'Head Img',
        ];
    }

    public function beforeSave($insert)
    {
        $this->md5TagName = md5($this->tagName);
        return parent::beforeSave($insert);
    }
}
