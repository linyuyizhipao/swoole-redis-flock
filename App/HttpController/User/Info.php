<?php

namespace App\HttpController\User;

use App\HttpController\Base;
use App\Request\User\InfoRequest;
use App\Service\User\InfoService;

class Info extends Base
{
    const USER_BASIC_TYPE = 1;//去用户基本信息

    //展示用户信息,这个api要做到足够灵活，根据不同的参数获取用户的不同信息
    public function index()
    {
        $param = InfoRequest::getInstance()->index($this->request()->getRequestParam());
        $type = $param['type'] ?? 1;
        $uid = $param['uid'];
        $result = [];
        switch ($type) {
            case self::USER_BASIC_TYPE :
                $result = InfoService::getInstance()->basicInfo($uid);
                break;
        }
        $this->success($result);
    }

}