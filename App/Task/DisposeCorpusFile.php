<?php
namespace App\Task;
use App\Model\BaseModel;
use EasySwoole\EasySwoole\Swoole\Task\QuickTaskInterface;

/*
 * 设置file-id的自增主键
 * set file-id 0
 * incr file-id
 *
 * 设置文件hash
 *
 * hset file-id:1 path ./a.xls offest 0 size 1024 valid-data 0 repetition-data 0  //文件位置，读取开始位置，文件总共大小
 *
 * lpush file-ids file-id:1
 *
 * */

class DisposeCorpusFile implements QuickTaskInterface
{

/*
 * 每一个用户上传自己需要翻译的稿件需要做到当前用户的稿件中的每一个句子，与这个用户在本应用上传的记录中的稿件句子
 * 做对比，如果当前句在历史中存在过，那么该句算该用户在该稿件中的重复句，
 * 反之则该句算该用户在该稿件中的非重复句，那么这个参数累加会用作计算该稿件的预估翻译费用
 *
 * 此处只做从队列中去文件id，然后处理该文件中的句子
 * 技术上  1.依次队列取文件来异步处理   低耦合，少等待
 *        2. 读取文件采用异步io，预防大文件采用按大小依次重复读取 占内存少
 *        3.一个用户对应一个句子集合，在判断该句是否重复上是根据往集合中插入的返回值判断，并没有循环对比的过程，可预防后期集合越来越大的时候循坏造成的吃内存
 *
 *
 * 后期优化：  此处目前采取的是一次出一个队列，处理一个文件。
 *            可一次出多个队列，实现多个协程处理多个文件
 *            甚至可以配置化代码，达到当出的文件id的文件大小达到多少时，为一个文件同时开启多个协程同时处理一个文件（此时需要考虑多协程间的通讯问题）
 *
 * */

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId)
    {
        go(function(){
             $fileIds = 'file-ids';
             $sizeOnce = 1024;

            $redis = BaseModel::getInstance()->getRedis();



            while(true){
                //从队列中取出一个文件id
                $fileId = $redis->brPop($fileIds,5);

                //通过文件id通过hash中取出文件的必要信息
                $path = $redis->hGet($fileId,'path');
                $func = function(string $filename, string $content) USE($fileId){
                    if(empty($content))
                        return true;

                    //已读取内容大小累加
                    $redis = BaseModel::getInstance()->getRedis();

                    $contentArr = explode('.',$content);
                    foreach ($contentArr as $k=>$v){
                        $res = $redis->sAdd('user-id-1',$v);
                        if($res !== false){
                            //插入进去了值,记录该文件不重复的句子数量加1
                            $redis->hIncrBy($fileId,'valid-data',1);
                        }else{
                            //没有插入进去值
                            $redis->hIncrBy($fileId,'repetition-data',1);
                        }
                    }
                    BaseModel::getInstance()->gc();
                    return true;
                };
                swoole_async_read($path,$func,$sizeOnce);

            }

            BaseModel::getInstance()->gc();

            //协程里面通过文件的基本必要信息，去按规定位置，及单次读取大小读取文件内容，并更新文件基本信息，再处理单次读取的内容

        });

    }
}