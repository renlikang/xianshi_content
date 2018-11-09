<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/11/1
 * Time: 8:24 PM
 */

namespace api\actions\index;

use common\services\RetCode;
use yii\rest\Action;
use Yii;

class Index extends Action
{
    public $modelClass = false;

    /**
     * @SWG\Get(
     *     path="/index/banner",
     *     tags={"首页管理"},
     *     summary="banner",
     *     description="",
     *     produces={"application/json"},
     *     @SWG\Response(response = 200,description = " success"),
     * )
     *
     */
    public function run()
    {
        return RetCode::response(RetCode::SUCCESS, Yii::$app->params['banner']);
    }
}