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

class InfoService extends UserModel
{
    use Singleton;
    public $errorMsg = '';

    //用户基本信息
    public function basicInfo($uid)
    {
        $db = $this->getDb();
        $res = $db->where('uid',$uid)->get($this->table);
        return $res;
    }
}