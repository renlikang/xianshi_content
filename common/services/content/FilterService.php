<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/29
 * Time: 8:01 PM
 */

namespace common\services\content;

use common\services\RetCode;
use Yii;
use yii\web\HttpException;

class FilterService
{
    public static function text($content)
    {
        $code = Yii::$app->antispam_txt->filterText($content);
        if($code == 2) {
            throw new HttpException(RetCode::LEGAL, '由于法律原因，不能上传');
        }
    }

    public static function img($url)
    {
        $code = Yii::$app->antispam->filter($url);
        if($code == 2) {
            throw new HttpException(RetCode::LEGAL, '由于法律原因，不能上传');
        }
    }
}