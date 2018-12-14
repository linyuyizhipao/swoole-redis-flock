<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Service\User;

use App\Model\UserModel;
use EasySwoole\Component\Singleton;

class LoginService extends UserModel
{
    use Singleton;
    public $errorMsg = '';
    //用户注册action
    function index($userName,$passWord) :bool
    {
        //根据用户名取用户记录
        $userInfo = $this->getDb()->where('username',$userName)->getOne('user');
        if(empty($userInfo)){
            $this->errorMsg = '用户名不存在';
            return false;
        }
        if(!isset($userInfo['username'],$userInfo['password'])){
            $this->errorMsg = '用户信息不完整';
            return false;
        }
        if($passWord != $userInfo['password']){
            $this->errorMsg = '密码错误';
            return false;
        }
        return true;
    }

}