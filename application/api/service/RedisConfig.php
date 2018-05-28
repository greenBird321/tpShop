<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/16
 * Time: 下午3:02
 */

namespace app\api\service;

use think\Cache;
class RedisConfig
{
    /**
     * redis 连接 配置文件
     * @param string $passWord
     * @param string $host
     * @param int $port
     * @param int $expire
     */
    public static function redisConnect($passWord = '', $host = '127.0.0.1', $port = 6379, $expire = 0){
        $config = [
            'type' => 'redis',
            'password' => $passWord,
            'host' => $host,
            'expire' => $expire,
            'port' => $port
        ];
        // think 通过 缓存类 对 NoSql类型的数据库进行操作
        Cache::connect($config);
    }
}