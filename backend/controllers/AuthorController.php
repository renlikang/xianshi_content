<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 2:32 PM
 */

namespace backend\controllers;

use common\models\User;
use common\services\backend\AuthorService;
use common\services\RetCode;
use common\services\UserService;
use yii\base\Exception;
use yii\rest\Controller;
use Yii;

class AuthorController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/author/list",
     *     tags={"用户管理"},
     *     summary="用户列表",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "query",name = "nickName",description = "作者昵称",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "type",description = "作者类型 1:普通用户 2:媒体用户",required = false, type = "string"),
     *     @SWG\Parameter(in = "query",name = "offset",description = "偏移量",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "page",description = "页数",required = false, type = "integer"),
     *     @SWG\Parameter(in = "query",name = "size",description = "每页size",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = " success"),
     *     @SWG\Response(response = 100,description = "导入的文章信息失败"),
     * )
     *
     */
    public function actionList()
    {
        $offset = Yii::$app->request->get('offset');
        $page = Yii::$app->request->get('page');
        $size = Yii::$app->request->get('size');
        $nickName = Yii::$app->request->get('nickName') ?? '';
        $type = Yii::$app->request->get('type') ?? '';
        $params = [];
        if($nickName) {
            $params['nickName'] = $nickName;
        }

        if($type) {
            $params['type'] = $type;
        }
        $data = AuthorService::authorList($offset, $page, $size, $params);
        return RetCode::response(RetCode::SUCCESS, $data);
    }

    /**
     * @SWG\Get(
     *     path="/author/detail",
     *     tags={"用户管理"},
     *     summary="用户详情",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "query",name = "authorId",description = "媒体作者编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionDetail()
    {
        $authorId = Yii::$app->request->get('authorId');
        $data = UserService::detail($authorId);
        return RetCode::response(RetCode::SUCCESS, $data);
    }

    /**
     * @SWG\Post(
     *     path="/author/create",
     *     tags={"用户管理"},
     *     summary="新增用户",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "nickName",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "avatarUrl",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "gender",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "country",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "province",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "city",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "language",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "birthday",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "signature",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionCreate()
    {
        $params = [];
        $params['nickName'] = Yii::$app->request->post('nickName');
        $params['avatarUrl'] = Yii::$app->request->post('avatarUrl');
        $params['gender'] = Yii::$app->request->post('gender') ?? 0;
        $params['country'] = Yii::$app->request->post('country') ?? '';
        $params['province'] = Yii::$app->request->post('province') ?? '';
        $params['city'] = Yii::$app->request->post('city') ?? '';
        $params['language'] = Yii::$app->request->post('language') ?? '';
        $params['birthday'] = Yii::$app->request->post('birthday') ?? '';
        $params['signature'] = Yii::$app->request->post('signature') ?? '';
        $params['unionid'] = Yii::$app->request->post('unionid') ?? $params['nickName'];
        try {
            $uid = UserService::create($params);
        } catch (Exception $e) {
            return RetCode::response($e->getCode(), [], [], $e->getMessage());
        }

        if(!$uid) {
            return RetCode::response(RetCode::DB_ERROR);
        }

        $ret = User::findOne($uid);
        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Post(
     *     path="/author/update",
     *     tags={"用户管理"},
     *     summary="更新用户",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "id",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "nickName",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "avatarUrl",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "gender",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "country",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "province",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "city",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "language",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "birthday",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "signature",description = "用户编号",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $params['nickName'] = Yii::$app->request->post('nickName');
        $params['avatarUrl'] = Yii::$app->request->post('avatarUrl');
        $params['gender'] = Yii::$app->request->post('gender') ?? 0;
        $params['country'] = Yii::$app->request->post('country') ?? '';
        $params['province'] = Yii::$app->request->post('province') ?? '';
        $params['city'] = Yii::$app->request->post('city') ?? '';
        $params['language'] = Yii::$app->request->post('language') ?? '';
        $params['birthday'] = Yii::$app->request->post('birthday') ?? '';
        $params['signature'] = Yii::$app->request->post('signature') ?? '';
        $ret = UserService::update($id, $params);
        if(!$ret) {
            return RetCode::response(RetCode::DB_ERROR);
        }

        return RetCode::response(RetCode::SUCCESS, $ret);
    }

    /**
     * @SWG\Post(
     *     path="/author/banned",
     *     tags={"用户管理"},
     *     summary="禁言/取消禁言",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Parameter(in = "formData",name = "authorId",description = "前台用户编号",required = true, type = "integer"),
     *     @SWG\Parameter(in = "formData",name = "status",description = "前台用户状态 1：正常 2：禁言",required = true, type = "integer"),
     *     @SWG\Response(response = 200,description = "success"),
     * )
     *
     */
    public function actionBanned()
    {
        $authorId = Yii::$app->request->post('authorId');
        $status = Yii::$app->request->post('status');
        UserService::banned($authorId, $status);
        return RetCode::response(RetCode::SUCCESS);
    }


}