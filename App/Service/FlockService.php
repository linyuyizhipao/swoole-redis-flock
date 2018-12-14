<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Service;

use App\Exceptions\GeneralException;
use App\Model\BaseModel;
use EasySwoole\Component\Singleton;

class FlockService extends BaseModel
{
    use Singleton;
    const FLOCK_TYPE_VAL = 2;
    protected $flockPrefix = 'CHAT:ID:';//群id前缀
    protected $publishName = 'FLOCK_CHAT';//群频道

    public function getPublishName()
    {
        return $this->publishName;
    }
    /*
    * 创建一个群，使用群id 为集合名称，
    * 集合里面的成员则为用户uid集合
    **/
    public function createFlockRedis($flockId,array $uids)
    {
        if(empty($uids) || !is_array($uids)){
            throw new GeneralException('uids非法');
        }

        $redis = $this->getRedis();
        $redis->sadd($flockId,...$uids);
    }

    public function createFlockMysql($flockId,array $uids)
    {
        if(empty($uids) || !is_array($uids)){
            throw new GeneralException('uids非法');
        }
        $data = [];
        foreach ($uids as $v){
            $data[] = ['flock_uid'=>$flockId,'uid'=>$v];
        }
        $db = $this->getDb();
        return $db->insertMulti('flock_uid',$data);
    }

    //生成群id
    public function createId()
    {
        return uniqid($this->flockPrefix);
    }

    //发送消息往mysql里面备份
    public function mysqlReserveMsg($uid,$flockId,$msg)
    {
        $db = $this->getDb();
        $arrData = [
            'uid'=>$uid,
            'flock_id'=>$flockId,
            'content'=>$msg,
            'create_at'=>date('Y-m-d H:i:s',time()),
            'types'=>self::FLOCK_TYPE_VAL,
        ];
        $db->insert('messages',$arrData);
    }

    //往群里面发一条信息,只需要往redis的群频道一推，redis订阅方  就会收到消息，然后根据消息频道对应的逻辑，解析消息主体，做对应的 将消息推送到client的行为
    public function redisPublish($uid,$flockId,$msg)
    {
        $fmtMessage = $flockId.'-'.$uid.'-'.$msg;
        $redis = $this->getRedis();
        return $redis->publish($this->publishName,$fmtMessage);
    }

}