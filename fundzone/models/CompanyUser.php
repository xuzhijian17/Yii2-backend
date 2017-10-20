<?php
namespace fundzone\models;

use common\lib\CommFun;
use frontend\models\User;
use Yii;

/**
 * 企业用户模型
 * Class CompanyUser
 * @package fundzone\models
 */
class CompanyUser extends User
{
    public $instid = 1000;

    public function __construct($instid=1000, array $config=[])
    {
        $this->instid = $instid;
        parent::__construct($config);
    }


    /**
     * 获得企业账号扩展信息
     * @param $TradeAcco
     * @return mixed
     */
    public function getCompanyAttachByTradeacco($TradeAcco)
    {
        $db = Yii::$app->db_local;
        $sql = "SELECT * FROM company_attach WHERE TradeAcco='{$TradeAcco}'";
        $user_info = $db->createCommand($sql)->queryOne();
        return $user_info;
    }

    /**
     * 更新企业用户扩展信息
     * @param $uid
     * @param $data
     * @return mixed
     */
    public function updateCompanyAttachByUid($uid, $data)
    {
        $db = Yii::$app->db_local;
        $up_sql = CommFun::JoinUpdateStr($data);
        $sql = "update company_attach set {$up_sql} WHERE Uid='{$uid}'";
        $r = $db->createCommand($sql)->execute();
        return $r;
    }
}