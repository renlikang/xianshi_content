<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "article_static".
 *
 * @property int $id ID
 * @property int $articleId 内容ID
 * @property int $type 1：封面
 * @property string $url 静态资源url
 * @property string $cTime 添加时间
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class ArticleStaticModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_static';
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
            [['articleId'], 'required'],
            [['articleId', 'type', 'deleteFlag'], 'integer'],
            [['url'], 'string'],
            [['cTime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'articleId' => 'Article ID',
            'type' => 'Type',
            'url' => 'Url',
            'cTime' => 'C Time',
            'deleteFlag' => 'Delete Flag',
        ];
    }
}