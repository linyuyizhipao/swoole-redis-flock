<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Service\User;

use App\Exceptions\GeneralException;
use App\Model\UserModel;
use EasySwoole\Component\Singleton;

class AccountService extends UserModel
{
    use Singleton;
    public $errorMsg = '';

    //用户基本信息
    public function editPsd($uid,$oldPassword,$newPassword)
    {
        $db = $this->getDb();
        $oldPwd = $this->encryptionPsd($oldPassword);
        $newPsd = $this->encryptionPsd($newPassword);
        $res = $db->where('uid',$uid)->where('password',$oldPwd)->getOne($this->table);
        if(!empty($res)){
            //改密码
            $re =  $db->where('uid',$uid)->update($this->table,['password'=>$newPsd]);
            if($re !== false){
                return true;
            }
            $this->errorMsg = '修改失败';
            return false;
        }else{
            //密码不对
            $this->errorMsg = '密码错误';
            return false;
        }
    }

    //用户账号上线mysql同步
    public function userActiveMysql($uid,$fd)
    {
        $mysql = $this->getDb();
        return $mysql->where('uid',$uid)->update($this->table,['fd',$fd]);
    }
    //用户账号上线redis同步
    //return true;
    public function userActiveRedis($uid,array $data)
    {
        $reids = $this->getRedis();
        $arrData = [
            'fd'=>'',
        ];
        foreach ($arrData as $k=>$v){
            if(isset($data[$k])){
                $arrData[$k] = $data[$k];
            }else{
                throw new GeneralException('userActiveRedis 参数错误');
            }
        }
        return $reids->hmset($uid,$arrData);
    }
}