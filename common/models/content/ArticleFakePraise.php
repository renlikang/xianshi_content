<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "article_fake_praise".
 *
 * @property int $id 伪造点赞Id
 * @property int $articleId 文章Id
 * @property int $fakePraise 伪造点赞数
 * @property string $cTime 创建时间
 * @property string $uTime 更新时间
 */
class ArticleFakePraise extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_fake_praise';
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
            [['articleId', 'fakePraise'], 'required'],
            [['articleId', 'fakePraise'], 'integer'],
            [['cTime', 'uTime'], 'safe'],
            [['articleId'], 'unique'],
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
            'fakePraise' => 'Fake Praise',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
        ];
    }
}
