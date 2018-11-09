<?php
/**
 * @author rlk
 */

namespace common\components;

use yii\base\Component;

class Helper extends Component
{
    public static function autoGeneratePassword($username)
    {
        $password = md5($username.time());
        return substr($password, 0, 8);
    }


    public static function encodeEmoji($text)
    {
        return self::convertEmoji($text,"ENCODE");
    }

    public static function decodeEmoji($text) {
        return self::convertEmoji($text,"DECODE");
    }


    private static function convertEmoji($text,$op)
    {
        if($op=="ENCODE"){
            return preg_replace_callback('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{1F000}-\x{1FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F9FF}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F9FF}][\x{1F000}-\x{1FEFF}]?/u',array('self',"encodeE"),$text);
        }else{
            return preg_replace_callback('/(\\\u[0-9a-f]{4})+/',array('self',"decodeE"),$text);
        }
    }

    private static function encodeE($match)
    {
        return str_replace(array('[',']','"'),'',json_encode($match));
    }

    private static function decodeE($text)
    {
        if(!$text) return '';
        $text = $text[0];
        $decode = json_decode($text,true);
        if($decode) return $decode;
        $text = '["' . $text . '"]';
        $decode = json_decode($text);
        if(count($decode) == 1){
            return $decode[0];
        }
        return $text;
    }

    public static function getCharArr($str)
    {
        $data['english'] = [];
        $data['china'] = [];
        $is_china = 0;
        $array = self::ch2arr($str);
        $key = 0;
        foreach ($array as $v) {
            if(strlen($v) > 1) {
                $data['china'][] = $v;
                $is_china = 1;
            } else {
                if($is_china == 0) {
                    if(!isset($data['english'][$key])) {
                        $data['english'][$key] = '';
                    }
                    $data['english'][$key] = $data['english'][$key] . $v;
                } else {
                    if(isset($data['english'][$key])) {
                        $key = $key + 1;
                        $data['english'][$key] = $v;
                    } else {
                        $data['english'][$key] = $v;
                    }
                }
                $is_china = 0;
            }
        }

        return $data;
    }

    public static function ch2arr($str)
    {
        $length = mb_strlen($str, 'utf-8');
        $array = [];
        for ($i=0; $i<$length; $i++)
            $array[] = mb_substr($str, $i, 1, 'utf-8');
        return $array;
    }


}