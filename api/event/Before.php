<?php
/**
 * @author rlk
 */

namespace api\event;

use common\services\RetCode;
use common\services\UserService;
use yii\base\Component;
use yii\base\Event;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\UnauthorizedHttpException;

class Before extends Component
{
    public static function index(Event $event)
    {
        self::checkRoute();
        return true;
    }

    public static function checkRoute()
    {
        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        if (self::needLogin($controllerId, $actionId)) {
            if (Yii::$app->user->isGuest) {
                throw new ForbiddenHttpException("必须登录用户才能访问");
            }
            elseif ($controllerId != 'user' and self::needUpdateUserInfo()) {
                throw new UnauthorizedHttpException("需要补全用户信息，请重新授权");
            }
        }

        if ($uid = Yii::$app->getUser()->getId()) {
            UserService::record($uid);
            self::banned($uid);
        }
    }

    public static function banned($uid)
    {
        if(!UserService::isBanned($uid)) {
            return true;
        }

        $config = [
            'article' => [
                'create', 'praise', 'comment', 'forward',
            ],
            'author' => [
               'attention', 'cancel-attention',
            ],
            'files' => [
                '*'
            ],
        ];

        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        if(isset($config[$controllerId])) {
            $data = $config[$controllerId];
            if(in_array('*', $data) || in_array($actionId, $data)) {
                throw new HttpException(RetCode::BANNED, RetCode::$responseMsg[RetCode::BANNED]);
            }

            return true;
        }
    }

    public static function needLogin($controllerId, $actionId) {
        if(isset(Yii::$app->params['permissionRoute']['guest'][$controllerId])) {
            if (in_array('*', Yii::$app->params['permissionRoute']['guest'][$controllerId])) {
                return false;
            }
            elseif (in_array($actionId, Yii::$app->params['permissionRoute']['guest'][$controllerId])) {
                return false;
            }
        }

        return true;
    }

    public static function needUpdateUserInfo() {
        return empty(Yii::$app->user->identity->unionid) or empty(Yii::$app->user->identity->nickName);
    }
}