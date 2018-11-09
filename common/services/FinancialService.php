<?php
/**
 * @author rlk
 */

namespace common\services;

use common\components\AddPillsJob;
use common\components\Cache;
use common\models\content\ArticleModel;
use common\models\User;
use common\services\api\DecibelsServices;
use common\services\api\PillServices;
use yii\base\Component;
use Yii;

class FinancialService extends Component
{
    private static $add = [
        'comment' => ['add' => 1, 'max' => 10],
        'praise'  => ['add' => 1, 'max' => 10],
        'forward' => ['add' => 1, 'max' => 10],
        'publish' => ['add' => 1, 'max' => 10],
    ];

    public static function setKey($type, $uid)
    {
        return md5(strtoupper($type) . '_ONE_DAY_PILL') . '-' . $uid;
    }

    public static function setDecibelsKey($type, $articleId)
    {
        return md5(strtoupper($type) . '_DECIBELS') . '-' . $articleId;
    }

    public static function add(EventService $event) {
        self::addPills($event);
        self::addDecibels($event);
    }

    /**
     * 新增药丸
     * @param EventService $event
     * @return bool
     */
    public static function addPills(EventService $event)
    {
        $params = $event->params;
        /** @var User $user */
        $user = $params['user'];
        /** @var Cache $cache */
        $cache = Yii::$app->cache;
        $config = self::$add[$params['type']];
        $key = self::setKey($params['type'], $user->id);
        if($cache->exists($key)) {
            $total = $cache->get($key);
        } else {
            $total = 0;
            $cache->set($key, 0);
            $expireAt = strtotime(date("Y-m-d",strtotime("+1 day")));
            $cache->expireat($key, $expireAt);
        }

        if($total >= $config['max']) {
            return true;
        }

        // 调用药丸接口
        $unionId = \Yii::$app->user->identity->unionid;
        $operationType = 'daily-' . $params['type'];
        if (PillServices::changeBalance($unionId, $operationType, 1) == false) {
            //如果增加药丸失败，重试 5 次
            Yii::$app->queue->push(new AddPillsJob([
                'unionId' => $unionId,
                'operationType' => $operationType,
                'number' => 1
            ]));
        }

        $cache->set($key, $total + $config['add']);
        return true;
    }

    /**
     * 新增分贝
     * @param EventService $event
     * @return bool
     */
    public static function addDecibels(EventService $event) {
        $params = $event->params;
        $type = $params['type'];
        /** @var User $user */
        $user = $params['user'];
        /** @var ArticleModel $article */
        $article = $params['article'];

        if ($type == 'comment' or $type == 'forward') {
            DecibelsServices::add($article->authorId, 1, ['type' => $type, 'userId' => $user->id, 'articleId' => $article->articleId]);
        } elseif ($type == 'praise') {
            $key = self::setDecibelsKey($params['type'], $article->articleId);

            if (Yii::$app->cache->exists($key)) {
                $total = Yii::$app->cache->get($key);
            } else {
                $total = 0;
            }

            $total++;

            if ($total >= 10) {
                DecibelsServices::add($article->authorId, 1, ['type' => $type, 'userId' => $user->id, 'articleId' => $article->articleId]);
                Yii::$app->cache->set($key, $total - 10);
            } else {
                Yii::$app->cache->set($key, $total);
            }
        }

        return true;
    }
}