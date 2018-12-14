<?php
namespace App\Exception;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

//应用抛出异常
class GenerateException
{
    public static function handle( \Throwable $exception, Request $request, Response $response )
    {
        if($exception instanceof AuthException){
            $fmt = [
                'code'=>$exception->getCode(),
                'msg'=>$exception->getMessage().' :AuthException',
                'data'=>[],
            ];
        }else{
            $fmt = [
                'code'=>$exception->getCode(),
                'msg'=>$exception->getMessage(),
                'data'=>[],
            ];
        }
        $response->write(json_encode($fmt));
    }
}