<?php

namespace common\services\api;

use common\models\content\Account;
use common\models\content\AccountLogs;
use yii\base\Component;

/**
 * Class DecibelsServices
 * @package common\services\api
 */
class DecibelsServices extends Component
{
    public static function add(int $userId, int $decibels, $log) :bool {
        if (self::duplicationCheck($log)) {
            return true;
        }

        $transaction = Account::getDb()->beginTransaction();

        if ($account = Account::findOne(['userId' => $userId])) {
            $account->decibels += $decibels;
        } else {
            $account = new Account();
            $account->userId = $userId;
            $account->decibels = $decibels;
        }
        $account->save();

        if ($account->errors) {
            \Yii::error("账户保存失败");
            $transaction->rollBack();
            return false;
        }

        $accountLogs = new AccountLogs();
        $accountLogs->accountId = $account->id;
        $accountLogs->log = $log;
        $accountLogs->save();

        if ($accountLogs->errors) {
            \Yii::error("账户记录保存失败");
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        return true;
    }

    /**
     * 重复评论和转发检查
     * 单个用户给同一篇文章评论和转发只发放一次分贝
     * @param $log
     * @return bool
     */
    public static function duplicationCheck($log) {
        if ($log['type'] == 'comment' or $log['type'] == 'forward') {
            return AccountLogs::find()->where('log->"$.type" = :type and log->"$.userId" = :userId and log->"$.articleId" = :articleId', [
                ":type" => $log['type'],
                ':userId' => $log['userId'],
                ':articleId' => $log['articleId']
            ])->exists();
        }
    }
}