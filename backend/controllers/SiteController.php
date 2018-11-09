<?php
namespace backend\controllers;

use yii\rest\Controller;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * @SWG\Get(
     *     path="/site/index",
     *     tags={"hello word"},
     *     summary="hello word",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function actionIndex()
    {
        Yii::error('aaaaa', __CLASS__.'::'.__FUNCTION__);
        return "这是后台api";
    }
}
