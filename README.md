# swooleFlockRedis

* 保证 PHP 版本大于等于 7.1
* 保证 Swoole 拓展版本大于等于 4.2.6
* 需要 pcntl 拓展的任意版本  (这个存在创建子进程的系统函数)
* 使用 Linux / FreeBSD / MacOS 这三类操作系统
* 使用 Composer 作为依赖管理工具


基于swoole  挖掘swoole各种特性

聊天功能。登录，注册，加好友，好友聊天，创建群，群聊

实现方式  websocket  redis订阅  

需要考虑  业务解耦，异步处理加快api反应，服务器扛压，资源最大化。

composer install   根据composer.lock 装好第三方依赖

php webSocketServer.php  启动websocket服务端

./start.sh /App      热启动easyswoole项目

php easyswoole start  cli 直接启动服务器


/App/Task/DisposeCorpusFile.php  结合redis特性写了个异步io计算文件稿件句子重复率的算法解决方案的demo


