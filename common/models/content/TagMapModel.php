<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "tag_map".
 *
 * @property resource $tagName 标签名称
 * @property int $mapId 映射ID
 * @property int $mapType 类型 1：文章 2：用户的标签 3：用户喜欢的话题
 */
class TagMapModel extends \yii\db\ActiveRecord
{
    const ARTICLE = 1;
    const USER = 2;
    const I_LIKE = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag_map';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     * @throws \yii\base\InvalidConfigException
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
            [['tagName', 'mapId', 'mapType'], 'required'],
            [['mapId', 'mapType'], 'integer'],
            [['tagName'], 'string', 'max' => 255],
            [['tagName', 'mapId', 'mapType'], 'unique', 'targetAttribute' => ['tagName', 'mapId', 'mapType']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tagName' => 'Tag Name',
            'mapId' => 'Map ID',
            'mapType' => 'Map Type',
        ];
    }
}
