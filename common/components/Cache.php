<?php

namespace common\components;

use yii;

class Cache extends yii\redis\Cache
{
    public function rPush($key, $value)
    {
        $key = $this->buildKey($key);
        $value = serialize($value);
        $flag = $this->redis->rpush($key, $value);
        return $flag;
    }

    public function buildKey($key)
    {
        if (is_array($key)) {
            $key = md5(json_encode($key));
        }
        return $this->keyPrefix . $key;
    }

    public function lPop($key)
    {
        $key = $this->buildKey($key);
        $ret = $this->redis->lpop($key);
        return ($ret !== false) ? unserialize($ret) : false;
    }


    public function hExists($token, $key)
    {
        $token = $this->buildKey($token);
        $ret = $this->redis->hexists($token, $key);
        return $ret;
    }

    public function hDel($token, $key)
    {
        $token = $this->buildKey($token);
        $ret = $this->redis->hdel($token, $key);
        return $ret;
    }

    public function hset($token, $key, $value)
    {
        $token = $this->buildKey($token);
        $value = serialize($value);
        $ret = $this->redis->hset($token, $key, $value);
        return $ret;
    }

    public function hget($token, $key)
    {
        $token = $this->buildKey($token);
        $ret = $this->redis->hget($token, $key);
        return ($ret !== false) ? unserialize($ret) : false;
    }

    public function getSet($key, $value)
    {
        $key = $this->buildKey($key);
        $value = serialize($value);
        $ret = $this->redis->getset($key, $value);
        return $ret === false ? $ret : unserialize($ret);
    }

    public function ttl($key)
    {
        $key = $this->buildKey($key);
        return $this->redis->ttl($key);
    }

    public function del($key)
    {
        $key = $this->buildKey($key);
        return $this->redis->del($key);
    }

    public function incr($key)
    {
        $key = $this->buildKey($key);
        return $this->redis->incr($key);
    }

    public function expire($key, $ttl)
    {
        $key = $this->buildKey($key);
        return $this->redis->expire($key, $ttl);
    }

    public function expireat($key, $ttl)
    {
        $key = $this->buildKey($key);
        return $this->redis->expireat($key, $ttl);
    }

    public function decr($key)
    {
        $key = $this->buildKey($key);
        return $this->redis->decr($key);
    }

    public function set($key, $value, $duration = null, $dependency = null)
    {
        $key = $this->buildKey($key);
        $value = serialize($value);
        if ($duration == 0) {
            return (bool)$this->redis->executeCommand('SET', [$key, $value]);
        } else {
            $expire = (int)($duration * 1000);
            return (bool)$this->redis->executeCommand('SET', [$key, $value, 'PX', $expire]);
        }
    }

    public function addValue($key, $value, $expire = 0)
    {
        $key = $this->buildKey($key);
        $value = serialize($value);
        if ($expire == 0) {
            return (bool)$this->redis->executeCommand('SET', [$key, $value, 'NX']);
        } else {
            $expire = (int)($expire * 1000);
            return (bool)$this->redis->executeCommand('SET', [$key, $value, 'PX', $expire, 'NX']);
        }
    }

    public function get($key, $serialize = true)
    {
        $key = $this->buildKey($key);
        $ret = $this->redis->executeCommand('GET', [$key]);
        if ($ret === false) {
            return $ret;
        } elseif ($serialize) {
            return unserialize($ret);
        } else {
            return $ret;
        }
    }
}