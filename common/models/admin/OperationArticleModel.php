<?php

namespace common\models\admin;

use Yii;

/**
 * This is the model class for table "operation_article".
 *
 * @property int $aid 管理者ID
 * @property int $articleId 文章ID
 * @property string $cTime 添加时间
 * @property string $uTime 更新时间
 */
class OperationArticleModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operation_article';
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
            [['aid', 'articleId'], 'required'],
            [['aid', 'articleId'], 'integer'],
            [['cTime', 'uTime'], 'safe'],
            [['aid', 'articleId'], 'unique', 'targetAttribute' => ['aid', 'articleId']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'aid' => 'Aid',
            'articleId' => 'Article ID',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
        ];
    }
}
