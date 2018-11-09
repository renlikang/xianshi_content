<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "article_forward".
 *
 * @property int $id 转发Id
 * @property int $articleId 文章Id
 * @property int $uid 用户Id
 * @property int $cTime 创建时间
 * @property int $uTime 更新时间
 * @property int $deleteFlg 删除标识：0正常，1删除
 */
class ArticleForward extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article_forward';
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
            [['articleId', 'uid', 'cTime', 'uTime'], 'required'],
            [['articleId', 'uid', 'cTime', 'uTime', 'deleteFlg'], 'integer'],
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
            'uid' => 'Uid',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
            'deleteFlg' => 'Delete Flg',
        ];
    }
}
