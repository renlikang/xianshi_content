<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/24
 * Time: 5:20 PM
 */

namespace backend\controllers;

use common\services\content\TagService;
use common\services\RetCode;
use yii\rest\Controller;
use Yii;

class TagController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/tag/list",
     *     tags={"标签管理"},
     *     summary="标签列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "query",name = "tagName",description = "标签名称（全称）",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页个数",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionList()
    {
        $tagName = Yii::$app->request->get('tagName') ?? '';
        $page = (int)Yii::$app->request->get('page');
        $size = (int)Yii::$app->request->get('size');
        $offset = (int)Yii::$app->request->get('offset');
        $params = [];
        if($tagName) {
            $params['tagName'] = $tagName;
        }
        $ret = TagService::tagList($offset, $page, $size, $params);
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Get(
     *     path="/tag/detail",
     *     tags={"标签管理"},
     *     summary="标签详情",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "query",name = "tagName",description = "标签名称（全称）",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionDetail()
    {
        $tagName = Yii::$app->request->get('tagName');
        return RetCode::response(RetCode::SUCCESS, TagService::detail($tagName));
    }

    /**
     * @SWG\Post(
     *     path="/tag/create",
     *     tags={"标签管理"},
     *     summary="创建标签",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "tagName",description = "标签名称（全称）",required = true, type = "string"),
     *     @SWG\Parameter(in = "formData",name = "headImg",description = "头图",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionCreate()
    {
        $tagName = Yii::$app->request->post('tagName');
        $headImg = Yii::$app->request->post('headImg');
        return RetCode::response(RetCode::SUCCESS, TagService::create($tagName, $headImg));
    }

    /**
     * @SWG\Post(
     *     path="/tag/update",
     *     tags={"标签管理"},
     *     summary="更新标签",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "tagName",description = "标签名称（全称）",required = true, type = "string"),
     *     @SWG\Parameter(in = "formData",name = "headImg",description = "头图",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionUpdate()
    {
        $tagName = Yii::$app->request->post('tagName');
        $headImg = Yii::$app->request->post('headImg');
        return RetCode::response(RetCode::SUCCESS, TagService::update($tagName, $headImg));
    }
}