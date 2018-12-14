<?php
namespace App\Exception;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

//应用抛出异常
class AuthException extends \Exception
{
    public static function handle( \Throwable $exception, Request $request, Response $response )
    {

    }
}