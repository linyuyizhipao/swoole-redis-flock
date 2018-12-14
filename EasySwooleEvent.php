<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Exception\GenerateException;
use App\Process\FlockChat;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Di;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        // 注册mysql数据库连接池
        PoolManager::getInstance()->register(RedisPool::class, Config::getInstance()->getConf('REDIS.POOL_MAX_NUM'));
        PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));

        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[GenerateException::class,'handle']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        ServerManager::getInstance()->getSwooleServer()->addProcess((new FlockChat('flock98'))->getProcess());
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        $response->withHeader('Content-type','application/json;charset=utf-8');
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data):void
    {

    }

}