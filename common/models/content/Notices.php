<?php

namespace common\models\content;

use Yii;

/**
 * This is the model class for table "notices".
 *
 * @property int $id 消息Id
 * @property string $type 消息类型
 * @property array $content 消息内容
 * @property int $is_read 是否已读，0未读，1已读
 * @property int $is_delete 是否删除，0未删除，1已删除
 * @property int $create_at 创建时间
 * @property int $update_at 更新时间
 * @property int $user_id 通知的用户Id
 */
class Notices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notices';
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
            [['type', 'content', 'create_at', 'update_at'], 'required'],
            [['type'], 'string'],
            [['content'], 'safe'],
            [['is_read', 'is_delete', 'create_at', 'update_at', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'content' => 'Content',
            'is_read' => 'Is Read',
            'is_delete' => 'Is Delete',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'user_id' => 'User ID',
        ];
    }
}
