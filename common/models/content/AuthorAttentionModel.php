<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "author_attention".
 *
 * @property int $authorId 作者ID
 * @property int $uid 用户ID
 * @property string $cTime 添加时间
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class AuthorAttentionModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author_attention';
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
            [['authorId', 'uid'], 'required'],
            [['authorId', 'uid', 'deleteFlag'], 'integer'],
            [['cTime'], 'safe'],
            [['authorId', 'uid'], 'unique', 'targetAttribute' => ['authorId', 'uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'authorId' => 'Author ID',
            'uid' => 'Uid',
            'cTime' => 'C Time',
            'deleteFlag' => 'Delete Flag',
        ];
    }
}