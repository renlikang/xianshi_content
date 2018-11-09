<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/19
 * Time: 12:09 PM
 */

namespace common\services\content;

use common\models\content\ArticleFakePraise;
use common\models\content\ArticlePraiseModel;
use common\models\content\TagMapModel;
use common\models\content\TagModel;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class TagService
{
    public static function mapArticle($tagName, $articleId, $headImg = '')
    {
        if(!self::create($tagName, $headImg)) {
            return false;
        }

        $model = TagMapModel::findOne(['tagName' => $tagName, 'mapId' => $articleId, 'mapType' => TagMapModel::ARTICLE]);
        if(!$model) {
            $model = new TagMapModel();
            $model->tagName = $tagName;
            $model->mapId = $articleId;
            $model->mapType = TagMapModel::ARTICLE;
            if(!$model->save()) {
                Yii::error($model->errors, __CLASS__.'::'.__FUNCTION__);
                return false;
            }

            return true;
        }
    }


    public static function deleteMapArticle($articleId)
    {
        TagMapModel::deleteAll(['mapId' => $articleId, 'mapType' => TagMapModel::ARTICLE]);
        return true;
    }

    public static function create($tagName, $headImg = '')
    {
        $tag = TagModel::findOne(['tagName' => $tagName]);
        if(!$tag) {
            $tag = new TagModel();
            $tag->tagName = $tagName;
            $tag->headImg = $headImg;
            $tag->realTagName = $tagName;
            if(!$tag->save()) {
                Yii::error($tag->errors, __CLASS__.'::'.__FUNCTION__);
                return false;
            }
        }

        return TagModel::findOne(['tagName' => $tagName]);
    }

    public static function update($tagName, $headImg = '')
    {
        if ($tag = TagModel::findOne(['tagName' => $tagName])) {
            $tag->headImg = $headImg;
            if (!$tag->save()) {
                Yii::error($tag->errors, __CLASS__ . '::' . __FUNCTION__);
                return false;
            }
        } else {
            throw new NotFoundHttpException("标签不存在");
        }

        return TagModel::findOne(['tagName' => $tagName]);
    }

    public static function detail($tagName)
    {
        $model = TagModel::findOne($tagName) or $model = TagModel::findOne(['md5TagName' => $tagName]);
        if(!$model) {
            return [];
        }

        $model = $model->toArray();
        $model['articleCount'] = (int)TagMapModel::find()->where(['tagName' => $tagName, 'mapType' => TagMapModel::ARTICLE])->count();
        $model['praiseTotal'] = TagService::getPraiseTotal($tagName);
        return $model;
    }

    public static function getPraiseTotal($tagName) : int {
        $praiseTotal = (int)ArticlePraiseModel::find()->where('articleId in (select mapId from tag_map where mapType = 1 and tagName = :tagName)', [
            ':tagName' => $tagName
        ])->count();

        $fakePraise = (int)ArticleFakePraise::find()->where('articleId in (select mapId from tag_map where mapType = 1 and tagName = :tagName)', [
            ':tagName' => $tagName
        ])->sum('fakePraise');

        return $praiseTotal + $fakePraise;
    }

    public static function tagAll($articleId)
    {
        /** @var TagMapModel[] $model */
        $model = TagMapModel::find()->where(['mapId' => $articleId, 'mapType' => TagMapModel::ARTICLE])->all();
        $data = [];
        foreach ($model as $k => $v) {
            $tag = self::detail($v->tagName);
            if($tag) {
                $data[] = $tag;
            }
        }

        return $data;
    }

    public static function tagList($offset, $page, $size, $params= [])
    {
        $model = TagModel::find();
        if(isset($params['tagName']) && $params['tagName']) {
            $tagName = $params['tagName'];
            $model->andFilterWhere(['like', 'realTagName', $tagName])->orFilterWhere(['like', 'md5TagName', $tagName]);
        }

        $modelClone = clone $model;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var TagModel[] $model */
        $tags = $model->offset($offset)->limit($pages->pageSize)->all();
        $data = [];
        $ret = [];

        foreach ($tags as $k => $v)  {
            $data[] = self::detail($v->tagName);
        }
        
        $ret['list'] = $data;
        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        return $ret;
    }

    public static function getArticleIdArr($tagName)
    {
        $model = TagMapModel::find()->where("tagName in (select tagName from tag where tagName = :tagName or md5TagName = :tagName) and mapType = :mapType", [
            ":tagName" => $tagName,
            ":mapType" => TagMapModel::ARTICLE
        ])->all();
        if($model) {
            return ArrayHelper::getColumn($model, 'mapId');
        }

        return [];
    }
}