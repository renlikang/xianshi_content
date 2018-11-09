<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/26
 * Time: 12:00 PM
 */

namespace console\controllers;

use common\models\content\ArticleModel;
use common\models\content\TagMapModel;
use common\models\elasticsearch\ArticleElasticModel;
use common\services\content\TagService;
use yii\console\Controller;
use yii\helpers\Json;

class ArticleController extends Controller
{
    public function actionSync()
    {
        /** @var ArticleModel[] $model */
        $model = ArticleModel::find()->all();
        foreach ($model as $k => $v) {
            ArticleElasticModel::create($v->articleId);
        }
    }

    public function actionTmpSync()
    {
        $data = "3438,3541,3596,3648,3649,3666,3667,3669,3681,3683,3684,3685,3686,3687,3688,3689,3690,3691,3693,3694,3695,3696,3705,3707,3709,3710,3711,3712,3713,3714";
        $data = explode(',', $data);
        foreach ($data as $k => $v ) {
            $model = ArticleModel::findOne($v);
            if($model) {
                ArticleElasticModel::create($v);
                echo $v . ',';
            }
        }
    }


    public function actionTagSync()
    {
        /** @var ArticleModel[] $articleModel */
        $articleModel = ArticleModel::find()->all();
        foreach ($articleModel as $k => $v) {
            $tagNames = [];
            /** @var TagMapModel[] $tagModel */
            $tagModel = TagMapModel::find()->where(['mapId' => $v->articleId, 'mapType' => TagMapModel::ARTICLE])->all();
            if(!$tagModel) {
                continue;
            }

            foreach ($tagModel as $kk => $vv) {
                $tagNames[] = TagService::detail($vv->tagName);
            }

            $tagNames = Json::encode($tagNames);
            $es = ArticleElasticModel::findOne($v->articleId);
            $es->tagNames = $tagNames;
            $es->save();
        }
    }


    public function actionLoading()
    {
        $a = ArticleElasticModel::findOne(['articleId' => 3666]);
        $b = ArticleElasticModel::findOne(['articleId' => 3667]);
        $c = ArticleElasticModel::findOne(['articleId' => 3669]);
        var_dump($a->cTime . ',' . $b->cTime . ',' . $c->cTime . "\n");
        $a = ArticleElasticModel::findOne(['articleId' => 3721]);
        $b = ArticleElasticModel::findOne(['articleId' => 3722]);
        $c = ArticleElasticModel::findOne(['articleId' => 3723]);
        var_dump($a->cTime . ',' . $b->cTime . ',' . $c->cTime . "\n");
    }

}