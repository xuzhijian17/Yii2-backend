<?php
namespace clientend\services;
use common\lib\HundSun;
use Yii;

class BaseService
{
    public $uid;
    public $instid;
    public $hs;
    
    /*
     * 构造方法
     * @param int $uid 用户id
     * @param int $instId 商户号
     */
    public function __construct ( $instId=0, $uid=0 )
    {
        $this->uid = $uid;
        $this->instId = $instId;
    }

    //获取恒生实例对象
    public function getHSInstance()
    {
        $this->hs = new HundSun($this->uid);
        return $this->hs;
    }


}