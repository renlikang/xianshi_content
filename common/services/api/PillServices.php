<?php

namespace common\services\api;

use GuzzleHttp\Client;
use phpseclib\Crypt\RSA;
use yii\base\Component;

/**
 * Class PillServices
 * @package common\services\api
 */
class PillServices extends Component
{
    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $appKey;
    /**
     * @var string
     */
    protected $baseUrl;

    public function init()
    {
        if(YII_ENV_PROD == true) {
            $this->appId = '1810150443434d24c6';
            $this->appKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCCjBOTeYFALytP+n/LzGKPJ1mLRfOPmkddaYsK8T9MBr4MYRHPGAvAQRsn+PqYCQ4eBW4F1pWYU4p7b6Mqbww4ClLPpvBELceG7olJT6gUeQBZZu7H1ZX1v5vRx+sozXACPyUlW5TiSmne/A9alvH9XYiedos8GQIfRz+rtY7+lv9HY8tm4IZnU691d1koHt1uFljO5/l7Fi6TEQVfgm4QwPGOz9JLNjswIjLAeQmyvCVS/I+IXVU+qcPqBeQA8gygTNGMboyePuarOWMIgYccVwp1acvphl6eZnhOr1ptn2LNrRqjKkBz46qHso3yCr1KJTjSPpSA9Azkkd/Fn0bzAgMBAAECggEABISMeOIPkwUO3qmwmoDsOEAIXUWCdEnGhnkMkshehGcHVK5XS29tmK2oHu4C3hgSIP/XGuSeMLNTa4olf2ZwadARpZYFYpai/QxsO9lB29OEK5PgV5IQqnyDC/N7M+JIKgu9YVmWAW7J0ymt7lii7z25aKJ5lw7Wf/abQXn7BNxwLwXQqqqzxDvDmU+e3DWzDyS0itmglZGT7Jwyj6aot8gn3+lvwkZVJNEEmp4YvSf4X2DX5dlSmSRf2+7uC+0mduQL8opSShSixxIZPM7cDcPzO952VZWizeaYxsU6iZVDBdoD+/XhBPOYJBXthZ4RQCZMFeYAkx8Wuh6PGJ0sYQKBgQDGesDSejSFxXR3puhVrDOWUvQ3D+RfSaEVBEU5EfACM9hvRxCuQ2WUNz8xklU9WlifkgMpknfEAuybVVawSiJTVu0WGvUQf/vR4tqJ82fu+Tld76135+R7PQzWnVQz2osH/jmLNLpHf0Oa87E0JJ2ehwZ2jkAXXNtdMirYNJoK3wKBgQCoYWfcbPgH+feeLQKT+6FQfgnJTXFhZSLCK+du3Buiq5ih4Kgmxaj8Zs76cuoUN80ALwC0N62H7NevF8F9cARHgROPWxhRocUEHEAceq0jINukanRKhQWgKMEsmaba21ePSMmW4kMp2XdNeDr2Eo1N61rIHO30qVOfwg9NMNIabQKBgQC35COi3vaqujceZNX+Cn4BWPpLyb2dS2qjPkGkjqoAYGi3MPCThH4oxMcX9qxjG8Hglje33Kx6PvtZL8gRrZ3/+RnQ0Ukc8HcwDy7LgWlaUC6Gifn+jitywy2R6lKJeII39aL7Bu9QLulEN2SHGyWuKwwh68oV20KjJkfr6VCPoQKBgQCVpUQOJhZl2GWWgYAqDte2Vt3Rt28N1TmIAkcEQsCY2RKkw6oOE1t1PtWyxlB9OX6LLDre8CDcjcS+i0ledyCGgBPnM1mwqyoS1hywcuLPNpOeUzIZTJHMXowYXFJrbccqw9DNtTdlySz7f+A+9Av7dTFQak2dRKPadzjsLHh+HQKBgCZ2vYQw9KktqLizEPaGiZ6/82Qnc5uMnoyO0JDxCUDHqa0tUhJ8FJgjV0/s2S4mEYZ/Y3+0Kh+sSMCjNOf7nmueGkbMNpIDd8yRQoadYIOdaZNOkmKD/rRkNwf3j0Q+9uSKZIce4G/+ZYkSF19at2GYXdWYbrqDUzw0YIV0/b98';
           $this->baseUrl = 'http://yx1.fashionipo.com/ipo-actgateway/common/v1/';
        } else {
            $this->appId = '1809270823458827b1';
            $this->baseUrl = 'http://yx1-dev.fashionipo.com/ipo-actgateway/common/v1/';
            $this->appKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCCjBOTeYFALytP+n/LzGKPJ1mLRfOPmkddaYsK8T9MBr4MYRHPGAvAQRsn+PqYCQ4eBW4F1pWYU4p7b6Mqbww4ClLPpvBELceG7olJT6gUeQBZZu7H1ZX1v5vRx+sozXACPyUlW5TiSmne/A9alvH9XYiedos8GQIfRz+rtY7+lv9HY8tm4IZnU691d1koHt1uFljO5/l7Fi6TEQVfgm4QwPGOz9JLNjswIjLAeQmyvCVS/I+IXVU+qcPqBeQA8gygTNGMboyePuarOWMIgYccVwp1acvphl6eZnhOr1ptn2LNrRqjKkBz46qHso3yCr1KJTjSPpSA9Azkkd/Fn0bzAgMBAAECggEABISMeOIPkwUO3qmwmoDsOEAIXUWCdEnGhnkMkshehGcHVK5XS29tmK2oHu4C3hgSIP/XGuSeMLNTa4olf2ZwadARpZYFYpai/QxsO9lB29OEK5PgV5IQqnyDC/N7M+JIKgu9YVmWAW7J0ymt7lii7z25aKJ5lw7Wf/abQXn7BNxwLwXQqqqzxDvDmU+e3DWzDyS0itmglZGT7Jwyj6aot8gn3+lvwkZVJNEEmp4YvSf4X2DX5dlSmSRf2+7uC+0mduQL8opSShSixxIZPM7cDcPzO952VZWizeaYxsU6iZVDBdoD+/XhBPOYJBXthZ4RQCZMFeYAkx8Wuh6PGJ0sYQKBgQDGesDSejSFxXR3puhVrDOWUvQ3D+RfSaEVBEU5EfACM9hvRxCuQ2WUNz8xklU9WlifkgMpknfEAuybVVawSiJTVu0WGvUQf/vR4tqJ82fu+Tld76135+R7PQzWnVQz2osH/jmLNLpHf0Oa87E0JJ2ehwZ2jkAXXNtdMirYNJoK3wKBgQCoYWfcbPgH+feeLQKT+6FQfgnJTXFhZSLCK+du3Buiq5ih4Kgmxaj8Zs76cuoUN80ALwC0N62H7NevF8F9cARHgROPWxhRocUEHEAceq0jINukanRKhQWgKMEsmaba21ePSMmW4kMp2XdNeDr2Eo1N61rIHO30qVOfwg9NMNIabQKBgQC35COi3vaqujceZNX+Cn4BWPpLyb2dS2qjPkGkjqoAYGi3MPCThH4oxMcX9qxjG8Hglje33Kx6PvtZL8gRrZ3/+RnQ0Ukc8HcwDy7LgWlaUC6Gifn+jitywy2R6lKJeII39aL7Bu9QLulEN2SHGyWuKwwh68oV20KjJkfr6VCPoQKBgQCVpUQOJhZl2GWWgYAqDte2Vt3Rt28N1TmIAkcEQsCY2RKkw6oOE1t1PtWyxlB9OX6LLDre8CDcjcS+i0ledyCGgBPnM1mwqyoS1hywcuLPNpOeUzIZTJHMXowYXFJrbccqw9DNtTdlySz7f+A+9Av7dTFQak2dRKPadzjsLHh+HQKBgCZ2vYQw9KktqLizEPaGiZ6/82Qnc5uMnoyO0JDxCUDHqa0tUhJ8FJgjV0/s2S4mEYZ/Y3+0Kh+sSMCjNOf7nmueGkbMNpIDd8yRQoadYIOdaZNOkmKD/rRkNwf3j0Q+9uSKZIce4G/+ZYkSF19at2GYXdWYbrqDUzw0YIV0/b98';
        }
    }


    /**
     * @param $plainText
     * @return string
     */
    public static function sign($plainText) {
        $rsa = new RSA();
        $rsa->loadKey((new self)->appKey);
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $rsa->setHash("sha256");
        return base64_encode($rsa->sign($plainText));
    }

    /**
     * @param $unionId
     * @return mixed
     */
    public static function getBalance($unionId) {
        try {
            if (empty($unionId)) {
                return [
                    'authorized' => false,
                    'balance' => 0
                ];
            }

            $params = [
                'appid' => (new self)->appId,
                'nonce' => \Yii::$app->security->generateRandomString(),
                'timestamp' => time() * 1000,
                'unionId' => $unionId
            ];

            $signStr = urldecode(http_build_query($params));
            $params['sign'] = self::sign($signStr);

            $client = new Client([
                'base_uri' => (new self)->baseUrl
            ]);
            $response = $client->get("pill/leftover?" . http_build_query($params));
            $result = \GuzzleHttp\json_decode($response->getBody()->getContents());
            \Yii::info("获取药丸成功" . $response->getBody()->getContents(), __CLASS__ . "::" . __FUNCTION__ . "::" . __LINE__);
            return [
                'authorized' => $result->message->authorized,
                'balance' => $result->message->left
            ];
        } catch (\Exception $exception) {
            \Yii::error("获取药丸失败" . $exception->getMessage(), __CLASS__ . "::" . __FUNCTION__ . "::" . __LINE__);
            return $exception->getMessage();
        }
    }

    /**
     * @param $unionId
     * @param $operationType
     * @param $number
     * @return mixed
     */
    public static function changeBalance($unionId, $operationType, $number) {
        try {
            if (empty($unionId)) {
                return false;
            }

            $params = [
                'appid' => (new self)->appId,
                'nonce' => \Yii::$app->security->generateRandomString(),
                'number' => $number,
                'operationType' => $operationType,
                'timestamp' => time() * 1000,
                'unionId' => $unionId
            ];

            $signStr = urldecode(http_build_query($params));
            $params['sign'] = self::sign($signStr);

            $client = new Client([
                'base_uri' => (new self)->baseUrl
            ]);

            $response = $client->get("pill/change?" . http_build_query($params));
            $result = \GuzzleHttp\json_decode($response->getBody()->getContents());
            \Yii::info("增加药丸成功" . json_encode($result), __CLASS__ . "::" . __FUNCTION__ . "::" . __LINE__);
            return $result->success;
        } catch (\Exception $exception) {
            \Yii::error("增加药丸失败" . $exception->getMessage(), __CLASS__ . "::" . __FUNCTION__ . "::" . __LINE__);
            return false;
        }
    }

}