<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:40
 */

namespace App\HttpController;

use App\Model\BaseModel;
use App\Service\MysqlService;
use App\Service\RedisService;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class Base extends Controller
{
    function index()
    {
        $this->actionNotFound('index');
    }
    protected function success($data=[],$msg = 'ok')
    {
        $this->writeJson(Status::CODE_OK,$data,$msg);
    }
    protected function error($msg='error')
    {
        $this->writeJson(Status::CODE_BAD_REQUEST,[],$msg);
    }

    protected function gc(){
        parent::gc();
        BaseModel::getInstance()->gc();
    }
}