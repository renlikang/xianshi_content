<?php

namespace common\models\content;

use common\components\Helper;
use common\models\elasticsearch\ArticleElasticModel;
use Yii;
use linslin\yii2\curl\Curl;

/**
 * This is the model class for table "article".
 *
 * @property int $articleId 文章ID
 * @property int $authorId 作者ID
 * @property int $type 文章类型，1 PGC 2 爬虫 3 UGC
 * @property string $source 文章来源
 * @property string $title 标题
 * @property string $subTitle 副标题
 * @property string $summary 内容摘要
 * @property string $headImg 头图
 * @property array $content 封面图片
 * @property int $orderId 权重
 * @property int $status 状态 1:启用 0:禁用
 * @property string $cTime 添加时间
 * @property string $uTime 更新时间
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class ArticleModel extends \yii\db\ActiveRecord
{
    public $tagNames;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
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
            [['authorId'], 'required'],
            [['authorId', 'type', 'orderId', 'status', 'deleteFlag'], 'integer'],
            [['summary'], 'string'],
            [['content', 'cTime', 'uTime'], 'safe'],
            [['source'], 'string', 'max' => 32],
            [['title', 'subTitle'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'articleId' => 'Article ID',
            'authorId' => 'Author ID',
            'type' => 'Type',
            'source' => 'Source',
            'title' => 'Title',
            'subTitle' => 'Sub Title',
            'summary' => 'Summary',
            'headImg' => 'Head Img',
            'coverType' => 'Cover Type',
            'covers' => 'Covers',
            'orderId' => 'Order ID',
            'status' => 'Status',
            'cTime' => 'C Time',
            'uTime' => 'U Time',
            'deleteFlag' => 'Delete Flag',
        ];
    }
}