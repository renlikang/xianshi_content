<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/22
 * Time: 3:58 PM
 */

namespace api\controllers;

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
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
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
     *     @SWG\Parameter(in = "header",name = "Authorization",description = "用户Token",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "tagName",description = "标签名称（全称）",required = true, type = "string"),
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionDetail()
    {
        $tagName = Yii::$app->request->get('tagName');
        $tagName = urldecode($tagName);
        return RetCode::response(RetCode::SUCCESS, TagService::detail($tagName));
    }
}