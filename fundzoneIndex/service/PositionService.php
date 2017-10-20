<?php
namespace fundzone\service;

use common\lib\CommFun;
use common\models\FundPosition;

use fundzone\models\TradeOrderZone;
use Yii;

class PositionService
{
    public $instid;
    public $uid;

    /**
     * PositionService constructor.
     * @param int $uid
     * @param int $instid
     */
    public function __construct($uid=0, $instid=1000)
    {
        $this->instid = $instid;
        $this->uid = $uid;
    }

    /**
     * 获取用户总持仓
     * @return array
     */
    public function userTotalPosition()
    {
        $fundPosition = new FundPosition([], $this->instid);
        $total_sum = $fundPosition->getUserTotalPositionByUid($this->uid);
        $tradeObj = new TradeOrderZone([], $this->instid);
        $tradeOrder = $tradeObj->getUserTradeApplying($this->uid);
        $data = [
            'totalsum' => !empty($total_sum['sum']) ? $total_sum['sum'] : 0.00,
            'unpaid' => !empty($total_sum['unpaid']) ? $total_sum['unpaid'] : 0.00,
            'sumdayprofitloss' => !empty($total_sum['sumdayprofitloss']) ? $total_sum['sumdayprofitloss'] : 0.00,
            'sumtotalprofitloss' => !empty($total_sum['sumtotalprofitloss']) ? $total_sum['sumtotalprofitloss'] : 0.00,
            'buying' => $tradeOrder['buying'],
            'selling' => $tradeOrder['selling']
        ];
        return $data;
    }

    /**
     * 获取用户持仓列表
     */
    public function userPositionList()
    {
        $fundPosition = new FundPosition([], $this->instid);
        $where1 = "uid = '{$this->uid}' AND (CurrentRemainShare>0 OR UnpaidIncome>0)";
        $where2 = "uid = '{$this->uid}' AND CurrentRemainShare<=0";
        $position_list = $fundPosition->query($where1, 'all');
        $unposition_list = $fundPosition->query($where2, 'all');
        $data = [
            'position_list' => $this->loopPositionList($position_list),
            'unposition_list'=> $this->loopPositionList($unposition_list)
        ];
        return $data;
    }


    public function loopPositionList($templist)
    {
        foreach ($templist AS $key=>$val ) {
            $val = array_change_key_case($val, CASE_LOWER);
            $fundInfo = CommFun::GetFundInfo($val['fundcode']);
            $val['fundname'] = !empty($fundInfo) ? $fundInfo['FundName'] : '';
            $val['pernetvalue'] = $fundInfo['PernetValue'];
            $val['totalsum'] = $val['currentremainshare'] * $fundInfo['PernetValue'];
            $templist[$key] = $val;
        }
        return $templist;
    }
}