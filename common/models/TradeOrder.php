<?php
namespace common\models;

use frontend\modules\api\models\BaseModel;
use Yii;


/**
 * 组合交易记录模型类
 *
 * Class PortfolioTrade
 * @package frontend\modules\api\models
 */
class TradeOrder extends BaseModel
{
    //TradeStatus
    const TS_APPLYING = 0; //申请中
    const TS_PART_CONFIRM = 1; //部分确认
    const TS_ALL_CONFIRM = 2; //全部确认

    //HandleStatus
    const HS_UNPROCESS = 0; //未处理
    const HS_PROCESS = 1; //已处理

    //TradeType
    const TT_PURCHASE = 0; //买入
    const TT_SELL = 1; //卖出
    const TT_WITHDRAW = 2; //撤单

    public function __construct($bsNo = 0, $field=[])
    {
        $this->tbName = "trade_order_{$bsNo}";
        parent::__construct($bsNo, $field);
    }

    /**
     * 获取用户申购中和赎回中的总金额
     * @param $uid
     * @return array
     */
    public function getUserTradeApplying($uid)
    {
        $data = ['buying'=>0.00, 'selling'=>0.00];
        $sql = "SELECT SUM(ApplyAmount) AS buying, SUM(ApplyShare) AS selling,TradeType FROM ".$this->tbName;
        $sql .= " WHERE TradeType IN (0,1) AND TradeStatus IN (9) AND DeductMoney=2 GROUP BY TradeType";
        $list = $this->db->createCommand($sql)->queryAll();
        if (!empty($list)) {
            foreach ($list as $k=>$val) {
                if ($val['TradeType'] == 0) {
                    $data['buying'] = $val['buying'];
                }
                if ($val['TradeType'] == 1) {
                    $data['selling'] = $val['selling'];
                }
            }
        }
        return $data;
    }
}
