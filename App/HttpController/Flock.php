<?php

namespace App\HttpController\User;

use App\Exception\AuthException;
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
        $mysqlStran = $flockObj->getDb()->startTransaction();
        $getFlockId = $flockObj->createId();

        $res = $flockObj->createFlockMysql($getFlockId,$uids);
        if(empty($res)){
            throw new AuthException('创建群失败');
        }

        $redisStatus = $flockObj->createFlockRedis($getFlockId,$uids);

        if(empty($redisStatus)){
            $mysqlStran->rollback();
            throw new AuthException('创建失败');
        }
        $mysqlStran->commit();
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
        $mysqlStran = $flockObj->getDb()->startTransaction();
        //mysql存根
        $mysqlStatus = $flockObj->mysqlReserveMsg($uid,$flockId,$msg);
        if(empty($mysqlStatus)){
            $mysqlStran->rollback();
            throw new AuthException('发送失败');
        }
        //redis推送
        $redisStatus = $flockObj->redisPublish($uid,$flockId,$msg);
        if(empty($redisStatus))
            throw new AuthException('失败');

        $mysqlStran->commit();//当前认为redis必须成功才算发消息成功
        $this->success(true,'发送成功');
    }
}