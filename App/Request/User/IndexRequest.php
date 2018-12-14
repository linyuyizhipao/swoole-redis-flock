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

class IndexRequest extends Validate
{
    use Singleton;

    public function login(array $data)
    {
        $this->addColumn('username')->required('账号不能为空')->lengthMin(2,'最小长度不能小于2位');
        $this->addColumn('password')->required('密码不能为空')->lengthMin(6,'最小长度不能小于6位');
        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }

    public function register(array $data)
    {
        $this->addColumn('nickname')->required('用户昵称不能为空')->lengthMin(5,'最小长度不能小于10位');
        $this->addColumn('username')->required('账号不能为空')->lengthMin(2,'最小长度不能小于10位');
        $this->addColumn('email')->email('email格式不对')->lengthMin(5,'最小长度不能小于5位');
        $this->addColumn('password')->required('用户昵称不能为空')->lengthMin(6,'最小长度不能小于6位');
        $this->addColumn('phone')->required('手机号码不能为空')->lengthMin(5,'最小长度不能小于11位');
        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }
    public function editPsd(array $data)
    {
        $this->addColumn('oldPassword')->required('旧密码不能为空');
        $this->addColumn('newPassword')->required('新密码不能为空');
        $this->addColumn('newPasswordAgain')->required('再次输入新密码不能为空');
        $this->addColumn('uid')->required('用户uid不能为空');

        if($data['newPassword'] != $data['newPasswordAgain']){
            throw new AuthException('2次密码不一致');
        }

        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }

    public function appear(array $data)
    {
        $this->addColumn('uid')->required('用户uid不能为空');
        $this->addColumn('fd')->required('用户fd为空非法');

        if(!$this->validate($data)){
            throw new AuthException($this->getError());
        }
        return $data;
    }
}