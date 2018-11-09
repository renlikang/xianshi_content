<?php

namespace common\models\content;

use common\components\Helper;
use common\models\elasticsearch\ArticleElasticModel;
use Yii;

/**
 * This is the model class for table "paragraph_content".
 *
 * @property int $contentId 内容ID
 * @property int $articleId 文章ID
 * @property int $paragraphId 段落ID
 * @property string $type 内容类型
 * @property string $content 内容
 * @property int $orderId 内容顺序
 * @property string $cTime 添加时间
 * @property string $uTime 更新时间
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class ParagraphContentModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paragraph_content';
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
            [['articleId', 'paragraphId', 'cTime'], 'required'],
            [['articleId', 'paragraphId', 'orderId', 'deleteFlag'], 'integer'],
            [['content'], 'string'],
            [['cTime', 'uTime'], 'safe'],
            [['type'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'contentId' => 'Content ID',
            'articleId' => 'Article ID',
            'paragraphId' => 'Paragraph ID',
            'type' => 'Type',
            'content' => 'Content',
            'orderId' => 'Order ID',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
            'deleteFlag' => 'Delete Flag',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->type == 'img' && substr($this->content, 0, strlen("https://static.heywoof.com")) != "https://static.heywoof.com") {
            $this->content = Yii::$app->upYun->uploadContent(file_get_contents($this->content), md5($this->content) . '.jpg');
        }
        $this->content = Helper::encodeEmoji($this->content);
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->content = Helper::decodeEmoji($this->content);
        $this->cTime = strtotime($this->cTime);
        $this->uTime = strtotime($this->uTime);
    }

    public function afterDelete()
    {
        ArticleElasticModel::create($this->articleId);
        parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        ArticleElasticModel::create($this->articleId);
        parent::afterSave($insert, $changedAttributes);
    }
}