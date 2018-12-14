<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/5
 * Time: 10:09 AM
 */

namespace App\Model;

class UserModel extends BaseModel
{
    const USER_REDIS_PREFIX = 'user:uid:';//用户在redis的hash的id
    const SECRET = 'swoole_shuang';
    protected $table = 'user';

    //密码加密
    protected function encryptionPsd(String $password)
    {
        $secret = str_split(self::SECRET);
        $password = str_split($password);
        $str = '';
        foreach ($password as $k=>$v){
            $pass = isset($secret[$k]) ? $secret[$k] : 'A';
            $str .= $v.$pass;
        }
        return sha1(md5($str));
    }

    //创建uid
    public function createUid()
    {
        return md5(uniqid(md5(microtime(true)),true));
    }
}
