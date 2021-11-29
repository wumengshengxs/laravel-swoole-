<?php


namespace App\Http\Swoole;

/**
 * redis实例类
 * Username 无梦生
 * data 2021/4/20
 * @package App\Helper
 */
class Redis
{

    protected static $redis = null;

    /**
     * 获取redis
     * @param int $db
     * Username 无梦生
     * data 2021/4/20
     * @return \Redis
     */
    public static function getRedis($db = 0) : \Redis
    {
        if (self::$redis == null){
            self::$redis = new \Redis();
            $conf  = config('database.redis');
            self::$redis->pconnect('127.0.0.1','6379',50);
        }
        self::$redis->select($db);

        return self::$redis;
    }







}
