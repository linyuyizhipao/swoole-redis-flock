<?php

namespace App\HttpController\User;

use App\Exceptions\GeneralException;
use App\HttpController\Base;
use App\Request\FlockRequest;
use App\Service\FlockService;
use App\Service\User\InfoService;

class Flock extends Base
{

    //创建一个群
    public function create()
    {
        $param = FlockRequest::getInstance()->create($this->request()->getRequestParam());
        $uids = $param['uids'];
        $result = [];
        $flockObj = FlockService::getInstance();
        $getFlockId = $flockObj->createId();

        $res = $flockObj->createFlockMysql($getFlockId,$uids);

        if(empty($res)){
            throw new GeneralException('创建群失败');
        }

        $redisStatus = $flockObj->createFlockRedis($getFlockId,$uids);

        if(empty($redisStatus)){
            throw new GeneralException('创建失败');
        }

        $this->success($result);
    }

    //用户在群里面发送一条消息
    public function uidSendMsg()
    {
        $param = FlockRequest::getInstance()->uidSendMsg($this->request()->getRequestParam());
        $uid = $param['uid'];
        $flockId = $param['flock_id'];
        $msg = $param['msg'];

        $flockObj = FlockService::getInstance();
        //mysql存根
        $mysqlStatus = $flockObj->mysqlReserveMsg($uid,$flockId,$msg);
        if(empty($mysqlStatus))
            throw new GeneralException('发送失败');
        //redis推送
        $redisStatus = $flockObj->redisPublish();
        if(empty($redisStatus))
            throw new GeneralException('失败');

        $this->success(true,'发送成功');
    }
}