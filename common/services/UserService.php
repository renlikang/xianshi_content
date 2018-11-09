<?php
/**
 * @author rlk
 */

namespace common\services;

use common\models\content\Account;
use common\models\content\ArticleCommentModel;
use common\models\content\ArticleFakePraise;
use common\models\content\ArticlePraiseModel;
use common\models\content\AuthorAttentionModel;
use common\models\User;
use yii\base\Exception;
use Yii;
use yii\helpers\Json;

class UserService
{
    const KEY = 'user.login.date.';
    public static function record($uid)
    {
        $cache = Yii::$app->cache;
        $key = self::KEY . $uid;
        if($date = $cache->get($key)) {
            if(date('Y-m-d') == $date) {
                return true;
            } else {
                $cache->set($key, date('Y-m-d'));
            }
        } else {
            $cache->set($key, date('Y-m-d'));
        }
    }

    /**
     * 距离上次登陆的天数
     * @param $uid
     * @return float|int
     */
    public static function getLoginDays($uid)
    {
        $cache = Yii::$app->cache;
        $key = self::KEY . $uid;
        if($date = $cache->get($key)) {
            if(date('Y-m-d') == $date) {
                return 0;
            } else {
                return ((time() - strtotime($date))/86400);
            }
        } else {
            $cache->set($key, date('Y-m-d'));
        }
    }

    public static function detail($uid)
    {
        $model = User::findOne($uid);
        if(!$model) {
            return null;
        }

        $ret = [];
        $ret['id'] = $model->id;
        $ret['uid'] = $model->id;
        $ret['userName'] = $model->nickName;
        $ret['nickName'] = $model->nickName;
        $ret['avatarUrl'] = $model->avatarUrl;
        $ret['gender'] = $model->gender;
        $ret['country'] = $model->country;
        $ret['province'] = $model->province;
        $ret['city'] = $model->city;
        $ret['language'] = $model->language;
        $ret['birthday'] = $model->birthday;
        $ret['signature'] = $model->signature;
        $ret['type'] = $model->type;
        $ret['status'] = $model->status;
        $ret['attentionTotal'] = (int)AuthorAttentionModel::find()->where(['uid' => $uid])->count();
        $ret['priseTotal'] = (int)ArticlePraiseModel::find()->where(['uid' => $uid])->count();
        $ret['commentTotal'] = (int)ArticleCommentModel::find()->where(['uid' => $uid])->count();
        $ret['fromPriseTotal'] = self::getPraiseTotal($uid);
        $ret['fromAttentionTotal'] = (int)AuthorAttentionModel::find()->where(['authorId' => $uid])->count();
        $ret['account'] = Account::findOne(["userId" => $uid]);
        return $ret;
    }

    public static function getPraiseTotal($userId) : int {
        $praiseTotal = (int)ArticlePraiseModel::find()->where('articleId in (select articleId from article where authorId = :authorId)', [
            ':authorId' => $userId
        ])->count();

        $fakePraise = (int)ArticleFakePraise::find()->where('articleId in (select articleId from article where authorId = :authorId)', [
            ':authorId' => $userId
        ])->sum('fakePraise');

        return $praiseTotal + $fakePraise;
    }

    public static function create($params)
    {
        if(isset($params['unionid']) && $params['unionid']) {
            if(User::findOne(['unionid' => $params['unionid']])) {
                throw new Exception("作者已经存在", RetCode::ERROR);
            }
        } else {
            throw new Exception("作者unionid不能为空", RetCode::ERROR);
        }
        $user = new User;
        if(isset($params['nickName']) && $params['nickName']) {
            $user->nickName = $params['nickName'];
        } else if(isset($params['name']) && $params['name']) {
            $user->nickName = $params['name'];
        }

        if(isset($params['avatarUrl']) && $params['avatarUrl']) {
            $user->avatarUrl = $params['avatarUrl'];
        } else if(isset($params['avatarImg']) && $params['avatarImg']) {
            $user->avatarUrl = $params['avatarImg'];
        }

        if(isset($params['gender'])) {
            $user->gender = (int)$params['gender'];
        }

        if(isset($params['country']) && $params['country']) {
            $user->country = $params['country'];
        }
        if(isset($params['province']) && $params['province']) {
            $user->province = $params['province'];
        }
        if(isset($params['city']) && $params['city']) {
            $user->city = $params['city'];
        }

        if(isset($params['language']) && $params['language']) {
            $user->language = $params['language'];
        }

        if(isset($params['birthday']) && $params['birthday']) {
            $user->birthday = $params['birthday'];
        }

        $user->type = 2;
        $user->signature = $params['signature'];
        $user->created_at = time();
        $user->updated_at = time();
        $user->openid = 'none';
        $user->session_key = 'none';
        $user->unionid = $params['unionid'] ?? '';
        if(!$user->save()) {
            Yii::error($user->errors, __CLASS__.'::'.__FUNCTION__);
            return false;
        }

        return $user->id;
    }

    public static function update($id, $params)
    {
        $model = User::findOne($id);
        foreach ($params as $k => $v) {
            $model->$k = $v;
        }

        $model->updated_at = time();
        if(!$model->save()) {
            Yii::error($model->errors, __CLASS__. '::' . __FUNCTION__);
            return false;
        }

        return self::detail($id);
    }

    /**
     * 设置是否禁言
     * @param $uid
     * @param $status
     * @return bool
     */
    public static function banned($uid, $status)
    {
        $model = User::findOne($uid);
        if($model) {
            $model->status = $status;
            if(!$model->save()) {
                Yii::error(Json::encode($model->errors), __CLASS__.'::'.__FUNCTION__.'::'.__LINE__);
                return false;
            }
        }

        return true;
    }

    /**
     * 获取该用户是否禁言
     * @param $uid
     * @return bool
     */
    public static function isBanned($uid)
    {
        return User::find()->where(['id' => $uid, 'status' => User::BANNED])->exists();
    }
}