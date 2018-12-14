<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Request\User;

use App\Exception\AuthException;
use EasySwoole\Component\Singleton;
use EasySwoole\Validate\Validate;

class InfoRequest extends Validate
{
    use Singleton;


    //获取用户信息
    public function index($data)
    {
        $this->addColumn('type','dsds')->numeric();
        $this->addColumn('uid','uid错误')->required('用户id');
     
        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }

}