<?php
namespace App\HttpController\User;

use App\Exceptions\GeneralException;
use App\HttpController\Base;
use App\Service\User\AccountService;
use App\Service\User\LoginService;
use App\Request\User\IndexRequest;
use App\Service\User\RegisterService;

class Index extends Base
{
    //注册api
    function register()
    {
        $postParam=IndexRequest::getInstance()->register($this->request()->getRequestParam());//验证参数
        $res = RegisterService::getInstance()->index($postParam);//业务逻辑
        if($res){
            $this->success();
        }else{
            $this->error();
        }
    }

    //登录
    public function login()
    {
        $postParam=IndexRequest::getInstance()->login($this->request()->getRequestParam());//验证参数
        $res = LoginService::getInstance()->index($postParam['username'],$postParam['password']);
        if($res){
            $this->success([],true);
        }else{
            $errorMsg = LoginService::getInstance()->errorMsg;
            $this->error($errorMsg);
        }
    }

    //修改密码
    public function editPsd()
    {
        $postParam=IndexRequest::getInstance()->editPsd($this->request()->getRequestParam());//验证参数
        list($uid,$oldPassword,$newPassword) =[$postParam['uid'],$postParam['oldPassword'],$postParam['newPassword']];
        $res = AccountService::getInstance()->editPsd($uid,$oldPassword,$newPassword);
        if($res){
            $this->success(true,'修改成功');
        }else{
            $errorMsg = AccountService::getInstance()->errorMsg;
            $this->error($errorMsg);
        }
    }

    //用户上报自己的fd
    public function appear()
    {
        $postParam=IndexRequest::getInstance()->appear($this->request()->getRequestParam());//验证参数
        $uid = $postParam['uid'];
        $fd = $postParam['fd'];
        $accountObj = AccountService::getInstance();
        $mysqlStran =  $accountObj->getDb()->startTransaction();
        //将用户上线的信息存入mysql
        $mysqlActiveStatus = $accountObj->userActiveMysql($uid,$fd);
        if($mysqlActiveStatus === false)
            throw new GeneralException('mysql同步失败');

        //同步redis
        $redisStatus = $accountObj->userActiveRedis($uid,['fd',$fd]);
        if($redisStatus){
            $mysqlStran->rollback();
            throw new GeneralException('redis同步失败');
        }
        $mysqlStran->commit();
        $this->success(true,'上报成功');
    }
}