<?php
/**
 * 自定义进程，用户redis订阅通讯
 * User: shuang
 * Date: 2018/10/18 0018
 * Time: 10:28
 */

namespace App\Process;

use swoole_websocket_server;
use swoole_redis;
use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use Swoole\Process;

class FlockChat extends AbstractProcess
{
    protected $channel = 'FLOCK_CHAT'; //订阅redis的频道名称
    protected $redisIp = '127.0.0.1'; //redis的ip
    protected $port = 6379;//redis的端口
    public function run(Process $process)
    {

    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
    }

}