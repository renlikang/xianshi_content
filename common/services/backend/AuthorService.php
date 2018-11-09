<?php
/**
 * Created by PhpStorm.
 * User: renlikang
 * Date: 2018/10/17
 * Time: 2:34 PM
 */

namespace common\services\backend;

use common\models\User;
use Yii;
use yii\data\Pagination;

class AuthorService
{
    public static function authorList($offset, $page, $size, $params = [])
    {
        if(!$page && !$offset) {
            $offset = 0;
        }

        $list = User::find();
        if(isset($params['nickName']) && $params['nickName']) {
            $list->andFilterWhere(['like', 'nickName', $params['nickName']]);
        }

        if(isset($params['type']) && $params['type']) {
            $list->andWhere(['type' => $params['type']]);
        }

        $list->orderBy("created_at desc");
        $modelClone = clone $list;
        $total = (int)$modelClone->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => $size]);
        if(!$offset && $page) {
            $pages->setPage($page - 1);
            $offset = $pages->offset;
        }else {
            $page = intval($offset / $size) + 1;
        }

        /** @var User[] $commentModel */
        $data = $list->offset($offset)->limit($pages->pageSize)->all();
        $ret = [];
        $ret['list'] = $data;
        $ret['page'] = $page;
        $ret['size'] = $size;
        $ret['total'] = $total;
        $ret['offset'] = $nexOffset = $offset + $size;
        $ret['hasMore'] = 1;
        if($nexOffset >= $total) {
            $ret['hasMore'] = 0;
        }

        return $ret;
    }
}