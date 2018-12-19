<?php

namespace App\HttpController\User;

use App\HttpController\Base;
class Index extends Base
{
    public function getFile()
    {
        $filePaths = [];
        foreach ($filePaths as $k=>$v){
            swoole_async_read($v);
        }
    }
}