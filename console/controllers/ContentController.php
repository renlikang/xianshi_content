<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/15
 * Time: 6:50 PM
 */

namespace console\controllers;

use common\models\content\ArticleModel;
use common\models\User;
use common\services\ExcelService;
use yii\console\Controller;
use yii\helpers\Json;

class ContentController extends Controller
{
    public function actionOrder()
    {
        Json::
        /** @var ArticleModel[] $model */
        $model = ArticleModel::find()->where(['type' => 2, 'deleteFlag' => 0])->orderBy('orderId desc, cTime desc')->all();
        foreach ($model as $k => $v) {
            $v->orderId = rand(0, 20);
            $v->save();
        }
    }
    public function actionLoading($filePath)
    {
        $excelService = new ExcelService();
        $rowData = $excelService->read($filePath);
        unset($rowData[0]);
        foreach ($rowData as $k => $v) {
            $oldAuthor = User::findOne(['nickName' => $v[0]]);
            if(!$oldAuthor) {
                echo $k . "行：被替换作者没有\n";
                continue;
            }

            $author = User::findOne(['nickName' => $v[1]]);
            $author->avatarUrl = $v[2];
            $author->session_key = 'none';
            $author->openid = 'none';
            if(!$author->save()) {
                echo $k . Json::encode($author->errors) . "\n";
            }
            if(!$author) {
                echo $k . "行：作者没有\n";
                continue;
            }

            try {
                $a = ArticleModel::findOne(['summary' => $v[3]]);
            } catch (\Exception $e) {
                echo $k . "行：文章摘要没有\n";
                continue;
            }

            if(!$a) {
                echo $k . "行：文章摘要没有\n";
                continue;
            }

            $a->authorId = $author->id;
            $a->summary = $v[4];
            $a->deleteFlag = 0;
            if(!$a->save()) {
                echo $k . Json::encode($a->errors) . "\n";
            }

        }
    }
}