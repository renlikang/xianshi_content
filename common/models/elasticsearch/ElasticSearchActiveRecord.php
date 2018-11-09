<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/22
 * Time: 6:47 PM
 */

namespace common\models\elasticsearch;

use common\components\Helper;
use common\models\content\ArticleModel;
use yii\data\Pagination;
use yii\elasticsearch\ActiveRecord;
use yii\helpers\ArrayHelper;

class ElasticSearchActiveRecord extends ActiveRecord
{
    static $primaryKey;

    public function attributes()
    {
        $mapConfig = static::mapConfig();
        return array_keys($mapConfig['properties']);
    }

    public static function getDb()
    {
        return \Yii::$app->get('elasticsearch');
    }

    public static function mapping()
    {
        return [
            static::type() => static::mapConfig(),
        ];
    }

    public static function updateMapping()
    {
        $db = self::getDb();
        $command = $db->createCommand();
        if(!$command->indexExists(self::index())){
            $command->createIndex(self::index());
        }
        $command->setMapping(self::index(), self::type(), self::mapping());
    }

    public static function inquire($params)
    {
        $class = static::className();
        $model = $class::find();
        foreach ($params as $k => $v) {
            $model->$k($v);
        }
    }

    public static function list($offset, $page, $size, $params)
    {
        $class = static::className();
        $list = $class::find();
        foreach ($params as $k => $v) {
            foreach ($v as $kk => $vv) {
                if($vv[0] == 'like') {
                    $char = Helper::getCharArr($vv[2]);
                    if(isset($char['china'])) {
                        foreach ($char['china'] as $kkk => $vvv) {
                            $query['bool']['should'][0]['bool']['must'][]['term'][$vv[1]] = $vvv;
                        }
                    }

                    if(isset($char['english'])) {
                        foreach ($char['english'] as $kkk => $vvv) {
                            $query['bool']['should'][0]['bool']['must'][]['regexp'][$vv[1]] = '.*'.$vvv.'.*';
                        }
                    }

                    if(isset($query) && $query) {
                        $list->query($query);
                    }

                } else {
                    if($vv[0] == 'in' || $vv[0] == '=') {
                        $list->$kk([$vv[1] => $vv[2]]);
                    } else {
                        $list->$kk($vv);
                    }
                }
            }
        }

        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        $data = $list->offset($offset)->limit($pages->pageSize)->all();
        if($data) {
            $ret['data'] = ArrayHelper::getColumn($data, static::$primaryKey);
        } else {
            $ret['data'] = [];
        }

        $ret['es'] = $data;
        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        return $ret;
    }
}