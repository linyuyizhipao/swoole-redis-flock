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

class RegisterService extends UserModel
{
    use Singleton;

    //用户注册action
    function index(array $data)
    {
        $data['password'] = $this->encryptionPsd($data['password']);
        $data['uid'] = $this->createUid();
        return $this->getDb()->insert($this->table,$data);
    }

}