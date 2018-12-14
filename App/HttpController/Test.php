<?php
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Spl\SplString;
use EasySwoole\Curl\Request;

class Test extends Controller
{

    function index()
    {
//        FlockService::getInstance()->createFlock([1,2,3,4,5,6,7]);
      //  FlockService::getInstance()->sendMeg('1','111','hello99!');



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