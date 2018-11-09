<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/11/1
 * Time: 8:23 PM
 */

namespace api\controllers;

use yii\rest\Controller;

class IndexController extends Controller
{
    public function actions()
    {
        return [
           'banner' => 'api\actions\index\Index',
        ];
    }
}