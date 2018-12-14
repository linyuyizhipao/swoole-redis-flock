<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Request;

use App\Exception\AuthException;
use EasySwoole\Component\Singleton;
use EasySwoole\Validate\Validate;

class FlockRequest extends Validate
{
    use Singleton;


    //获取用户信息
    public function create($data)
    {
        $this->addColumn('uids')->required('用户id集合不能为空');
     
        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }

    //用户在群里发消息
    public function uidSendMsg($data)
    {
        $this->addColumn('uid')->required('用户id不能为空');
        $this->addColumn('flock_id')->required('群id不能为空');
        $this->addColumn('msg')->required('消息内容不能为空');

        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }
}