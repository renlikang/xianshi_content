<?php

namespace common\models\elasticsearch;

use common\models\content\ArticleModel;
use common\models\content\ParagraphModel;
use common\services\content\ArticleService;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "article".
 *
 * @property int $articleId 文章ID
 * @property int $authorId 作者ID
 * @property int $type 文章类型，1 微信文章 2 Ins
 * @property string $title 标题
 * @property string $tagNames 标签
 * @property string $subTitle 副标题
 * @property string $summary 内容摘要
 * @property string $headImg 头图
 * @property string $coverType 封面类型
 * @property array $covers 封面图片
 * @property int genre 文章分类， 1 长文 2 短文
 * @property array $priseTotal 点赞总数
 * @property array $commentTotal 评论总数
 * @property int $orderId 权重
 * @property int $status 1:启用 0:禁用
 * @property string $cTime 添加时间
 * @property string $uTime 更新时间
 * @property int $deleteFlag 删除标识:0正常，1删除
 */
class ArticleElasticModel extends ElasticSearchActiveRecord
{
    public static $primaryKey = 'articleId';

    public static function index()
    {
        return 'woof_content';
    }

    public static function type()
    {
        return 'article';
    }

    public static function mapConfig()
    {
        return [
            'properties' => [
                'articleId'  => ['type' => 'long',  "index" => "not_analyzed"],
                'authorId' => ['type' => 'long',  "index" => "not_analyzed"],
                'type' => ['type' => 'long',  "index" => "not_analyzed"],
                'title'    => ['type' => 'string',"index" => "not_analyzed"],
                'tagNames'    => ['type' => 'string',"index" => "not_analyzed"],
                'subTitle'    => ['type' => 'string',"index" => "not_analyzed"],
                'summary'    => ['type' => 'string',"index" => "not_analyzed"],
                'headImg'    => ['type' => 'string',"index" => "not_analyzed"],
                'coverType'    => ['type' => 'string',"index" => "not_analyzed"],
                'covers'    => ['type' => 'string',"index" => "not_analyzed"],
                'priseTotal' => ['type' => 'long',"index" => "not_analyzed"],
                'commentTotal' => ['type' => 'long',"index" => "not_analyzed"],
                'genre' => ['type' => 'long',"index" => "not_analyzed"],
                'orderId'    => ['type' => 'long',"index" => "not_analyzed"],
                'status'    => ['type' => 'long',"index" => "not_analyzed"],
                'cTime'    => ['type' => 'long',"index" => "not_analyzed"],
                'uTime'    => ['type' => 'long',"index" => "not_analyzed"],
                'deleteFlag' => ['type' => 'long',"index" => "not_analyzed"],
            ]
        ];
    }

    public static function create($articleId, $tagNames = '')
    {
        $article = ArticleModel::findOne($articleId);
        if(!$article) {
            return false;
        }

        $model = ArticleElasticModel::findOne($articleId);
        if(!$model) {
            $model = new ArticleElasticModel;
            $model->articleId = $articleId;
            $model->primaryKey = $articleId;
        }

        if(ParagraphModel::findOne(['articleId' => $article->articleId])) {
            $model->genre = 1;
        } else {
            $model->genre = 2;
        }


        $model->authorId = $article->authorId;
        $model->tagNames = $tagNames;
        $model->type = $article->type;
        $model->title = $article->title;
        $model->subTitle = $article->subTitle;
        $model->summary = $article->summary;
        $model->headImg = $article->headImg;
        $model->coverType = $article->coverType;
        $model->covers = $article->covers;
        $model->priseTotal = ArticleService::getPraiseTotal($article);
        $model->commentTotal = ArticleService::getCommentTotal($article);
        $model->orderId = $article->orderId;
        $model->status = $article->status;
        $model->cTime = $article->cTime;
        $model->uTime = $article->uTime;
        $model->deleteFlag = $article->deleteFlag;
        if(!$model->save()) {
            Yii::error(Json::encode($model->errors), __CLASS__.'::'.__FUNCTION__);
        }

        return true;
    }
}