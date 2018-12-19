<?php
namespace App\Task;
use EasySwoole\EasySwoole\Swoole\Task\QuickTaskInterface;

class DisposeCorpusFile implements QuickTaskInterface
{
    static function run(\swoole_server $server, int $taskId, int $fromWorkerId)
    {
        echo "快速任务模板";

    }
}