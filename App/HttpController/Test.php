<?php
namespace App\HttpController;

use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Spl\SplString;
use EasySwoole\Curl\Request;

class Test extends Controller
{

    function index()
    {
        $result = TaskManager::async(\App\Task\DisposeCorpusFile::class);




    }


    function post()
    {

        $url = 'https://c.y.qq.com/lyric/fcgi-bin/fcg_query_lyric.fcg';
        $params = [
            'nobase64' => 1,
            'musicid' => 109332110,
            'inCharset' => 'utf8',
            'outCharset' => 'utf-8'
        ];
        $url = $url.'?'.http_build_query($params);
        $request = new Request($url);
        $request->setUserOpt([CURLOPT_REFERER => 'https://y.qq.com/n/yqq/song/001xiJdl0t4NgO.html']);
        $content = $request->exec()->getBody();
        $string = new SplString($content);
        $content = $string->regex('/\{.*\}/');
        $json = json_decode($content, true);
        $lyric = $json['lyric'];
        $this->response()->write(html_entity_decode($lyric));
    }


}