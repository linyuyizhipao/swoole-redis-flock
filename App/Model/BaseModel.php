<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Model;

use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisObject;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Component\Singleton;
use EasySwoole\Spl\SplBean;

class BaseModel extends SplBean
{
    private static $db;
    private static $redis;
    use Singleton;

    function getDb(): MysqlObject
    {
        if (!self::$db instanceof MysqlObject) {
            self::$db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL.POOL_TIME_OUT'));
            if (!self::$db instanceof MysqlObject) {
                if(!self::$db instanceof MysqlObject){
                    //直接抛给异常处理，不往下
                    throw new \Exception(' error,Mysql Pool is Empty');
                }
            }
        }
        return self::$db;
    }

    function getRedis(): RedisObject
    {
        if(!self::$redis instanceof RedisObject){
            self::$redis = PoolManager::getInstance()->getPool(RedisPool::class)->getObj(\EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS.POOL_TIME_OUT'));
            if(!self::$redis instanceof RedisObject){
                //直接抛给异常处理，不往下
                throw new \Exception(' error,redis Pool is Empty');
            }
        }
        return self::$redis;
    }

    function gc()
    {
        if (self::$db instanceof MysqlObject) {
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj(self::$db);
        }
        if (self::$redis instanceof RedisObject) {
            PoolManager::getInstance()->getPool(RedisPool::class)->recycleObj(self::$redis);
        }
    }
}