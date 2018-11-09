<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/11/8
 * Time: 5:34 PM
 */

namespace common\models\elasticsearch;

/**
 * This is the model class for table "article".
 *
 * @property int $uid 用户ID
 * @property int $form_key 表单KEY
 * @property int $open_id openId
 * @property string $expiredTime 添加时间
 */

class WxForm extends ElasticSearchActiveRecord
{
    public static $primaryKey = 'form_key';

    public static function index()
    {
        return 'woof_content';
    }

    public static function type()
    {
        return 'wx_form';
    }

    public static function mapConfig(){
        return [
            'properties' => [
                'uid'  => ['type' => 'long',  "index" => "not_analyzed"],
                'form_key' => ['type' => 'string', 'index' => 'not_analyzed'],
                'open_id' => ['type' => 'string', 'index' => 'not_analyzed'],
                'expiredTime' => ['type' => 'long', 'index' => 'not_analyzed'],
            ]
        ];
    }
}