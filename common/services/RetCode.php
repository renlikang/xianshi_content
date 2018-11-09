<?php
/**
 * @author rlk
 */

namespace common\services;

class RetCode
{
    /** 基础错误 */
    const LEGAL = 451;
    const BANNED = 423;
    const SUCCESS = 200;
    const ERROR = 10001;
    const DELETE = 410;
    const FILE_UPLOAD_FAIL = 10002;
    const DB_ERROR = 10003;
    const PARAM_ERROR = 10004;
    /** 内容 */
    const PRAISE_REPEAT = 11001;
    const PRAISE_Attention = 11002;
    /** 药丸 和 分贝 */
    const DECIBELS_NOT_ENOUGH = 12001;
    const DECIBELS_TO_PILLS_FAILED = 12002;
    public static $responseMsg = [
        self::SUCCESS => '成功',
        self::ERROR => '失败',
        self::DELETE => '资源已被删除',
        self::LEGAL => '由于法律原因，不能上传该内容',
        self::BANNED => '根据《WOOF社区用户守则》，您的账号已被禁言',
        self::FILE_UPLOAD_FAIL => '文件上传失败',
        self::DB_ERROR => '保存失败',
        self::PRAISE_REPEAT => '不能重复点赞',
        self::PRAISE_Attention => '您已经关注过了',
        self::PARAM_ERROR => '参数错误',
        self::DECIBELS_NOT_ENOUGH => '分贝不足，无法兑换药丸',
        self::DECIBELS_TO_PILLS_FAILED => '分贝换取药丸失败，请重试',
    ];

    public static function response($code = RetCode::SUCCESS, $data = [], $params = [], $msg = '')
    {
        if (isset(RetCode::$responseMsg[$code]) && !$msg) {
            $msg = RetCode::getErrMsgByCode($code, $params);
        }

        return ['retCode' => $code, 'retMsg' => $msg, 'retData' => $data];
    }

    public static function getErrMsgByCode($code, $params = [])
    {
        $message = isset(self::$responseMsg [$code]) ? self::$responseMsg [$code] : '服务器忙，请稍后再试～';
        $patterns = array_map(function ($pattern) {
            return "/#$pattern#/";
        }, array_keys($params));
        $values = array_values($params);
        return preg_replace($patterns, $values, $message);
    }
}