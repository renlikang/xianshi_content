<?php

namespace backend\controllers;

use common\services\RetCode;

/**
 * @SWG\Post(
 *     path="/article-fake-praises",
 *     tags={"文章管理"},
 *     summary="伪造文章点赞数",
 *     description="",
 *     produces={"application/json"},
 *     @SWG\Parameter(in = "formData",name = "articleId",description = "文章Id",required = true, type = "integer"),
 *     @SWG\Parameter(in = "formData",name = "fakePraise",description = "伪造点赞数",required = true, type = "integer"),
 *     @SWG\Response(response = 200,description = " success"),
 * )
 *
 */
class ArticleFakePraiseController extends \yii\rest\ActiveController {

    public $modelClass = 'common\models\content\ArticleFakePraise';

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return RetCode::response(RetCode::SUCCESS, $result);
    }
}