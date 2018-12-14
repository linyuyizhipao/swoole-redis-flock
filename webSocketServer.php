<?php
//配置对象
class Config{

    private static $instance;

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }

    protected $values = [
        'WEB_SOCKET_ALLOW_IP'=>'0.0.0.0',
        'WEB_SOCKET_PORT'=>9533,
        'REDIS_CHANNEL'=>'FLOCK_CHAT',//多个频道  FLOCK_CHAT FLOCK_CHAT_TWO FLOCK_CHAT_THREE 以空格分开
        'REDIS_IP'=>'127.0.0.1',
        'REDIS_PORT'=>6379,
    ];

    public static function get($key)
    {
        $values = self::getInstance()->values;
        return isset($values[$key]) ? $values[$key] : null;
    }


}

//频道处理逻辑
class ChannelProcessor
{
    private static $instance;

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
    //此方法具备 redis 当前推送消息频道信息  $channel
    //获取redis推送过来的消息主体  $msg
    //socket服务端对象，可给当前连接的任意fd发送信息的能力  $server
    public function run($channel,$msg,swoole_websocket_server $server)
    {
        switch ($channel){
            //如果是群聊频道,解析msg消息，获然后在此处写自己的业务逻辑，值得注意的是 $server 能够赋予你给fd在线的任何用户主动推送信息
            case 'FLOCK_CHAT':
                echo $msg;
                break;
        }
    }
}

//客户端建立web_socket的初始化行为
class ClientConnectInitialize
{
    private static $instance;

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }

    public function init($fd,$data = [])
    {

    }
    public function connect($fd,$data = [])
    {

    }
    public function close($fd,swoole_websocket_server $server)
    {

    }
}

defined('CHANNEL_STATUS_VAL') or define('CHANNEL_STATUS_VAL',1);//定义单例常量值,保证redis的订阅在多worker进程中只运行一次

//创建websocket服务器对象，监听0.0.0.0:9533端口
$server = new swoole_websocket_server(Config::get('WEB_SOCKET_ALLOW_IP'), Config::get('WEB_SOCKET_PORT'));

//监听WebSocket连接打开事件
$server->on('open', function (swoole_websocket_server $server, $request){
    //建立连接客户端初始化
    $channelStatusNum = $server->atomic->get();
    if($channelStatusNum == CHANNEL_STATUS_VAL){
        $server->atomic->add();
        $channel = Config::get('REDIS_CHANNEL'); //订阅redis的频道名称
        $redisIp = Config::get('REDIS_IP'); //redis的ip
        $port = Config::get('REDIS_PORT');//redis的端口

        $client = new swoole_redis();
        //$client 就是redis的客户端. $result redis返回的结果，结构为数组，共三元素，第一个 message  2频道名称，3.推送过来的消息
        $f = function (swoole_redis $client, $result) USE($server){
            if(isset($result[0]) && $result[0] == 'message'){
                $channel = $result[1];
                $msg = $result[2];

                ChannelProcessor::getInstance()->run($channel,$msg,$server);
            }
        };

        $client->on('message', $f);

        $client->connect($redisIp, $port, function (swoole_redis $client, $result) USE($channel) {
            $client->subscribe($channel);    //实现订阅order
        });
    }




    ClientConnectInitialize::getInstance()->connect($request->fd);
});

//监听WebSocket消息事件
$server->on('message', function (swoole_websocket_server $server, $frame) {
    $fd = $frame->fd;
    $data = $frame->data;
    //建立连接客户端，与操作用户的关联关系,或其他必要关联
    ClientConnectInitialize::getInstance()->init($fd,$data);
});

//监听WebSocket连接关闭事件
$server->on('close', function (swoole_websocket_server $server, $fd) {
    ClientConnectInitialize::getInstance()->close($fd,$server);
});

//**设计共享内存数据
$server->atomic  = new swoole_atomic(1);

$server->start();