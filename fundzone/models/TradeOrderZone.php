<?php
namespace fundzone\models;

use frontend\models\TradeOrder;
use Yii;


/**
 * 交易记录公共模型类
 *
 * Class TradeOrder
 * @package
 */
class TradeOrderZone extends TradeOrder
{
    public $trade_type_map = [
            SELF::TRADE_TYPE_BUY => '申购',
            SELF::TRADE_TYPE_SALE => '赎回',
            SELF::TRADE_TYPE_WITHDRAW => '撤单',
            SELF::TRADE_TYPE_BONUS => '定投',
    ];

    public $Deduct_Money_map = [
        0 => '未校验',
        1 => '无效',
        2 => '有效',
        3 => '忆发送扣款指令'
    ];

    public $trade_status_map = [
        self::TS_CONFIRM_FAIL => '失败',
        self::TS_CONFIRM_SUCCESS => '成功',
        self::TS_PART_CONFIRM_SUCCESS => '部分确认成功',
        self::TS_RT_CONFIRM_SUCCESS => '实时确认成功',
        self::TS_WITHDRAW => '已撤单',
        self::TS_ACTION_SUCCESS => '行为确认',
        self::TS_NOHANDLE => '未处理',
    ];

    public function __construct(array $field, $bsNo)
    {
        parent::__construct($field, $bsNo);
    }

    /**
     * 获取用户申购中和赎回中的总金额
     * @param $uid
     * @return array
     */
    public function getUserTradeApplying($uid)
    {
        $db = Yii::$app->db_local;
        $data = ['buying'=>0.00, 'selling'=>0.00];
        $sql = "SELECT SUM(ApplyAmount) AS buying, SUM(ApplyShare) AS selling,TradeType FROM ".$this->tbName;
        $sql .= " WHERE TradeType IN (0,1) AND TradeStatus IN (9) AND DeductMoney=2 GROUP BY TradeType";
        $list = $db->createCommand($sql)->queryAll();
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


    /**
     * 企业用户交易记录
     * @param string $where
     * @param string $orderby
     * @param int $length
     * @param int $offset
     * @return mixed
     */
    public function getCompanyUserRecord($where="", $rs="all", $orderby="ApplyTime DESC", $limit="")
    {
        $instid = $this->bsNo;
        $db = Yii::$app->db_local;
        $sql = "SELECT o.*,f.FundName,f.PernetValue,f.FundTypeCode,f.FundType,f.ShareType FROM trade_order_{$instid} AS o LEFT JOIN fund_info AS f ON o.FundCode=f.FundCode WHERE ";
        $sql .= " {$where} ORDER BY {$orderby} {$limit}";

        $list = $db->createCommand($sql)->queryAll();
        $uid = !empty($list) ? $list[0]['Uid'] : 0;
        $company = $db->createCommand("SELECT * FROM `company_attach` WHERE Uid = {$uid}")->queryOne();
        foreach($list as $key=>$val) {
            $list[$key]['bankname'] = $company['BankName'];
            $list[$key]['bankacco'] = $company['BankAcco'];
            $list[$key]['trade_type_text'] = $this->trade_type_map[$val['TradeType']];
            $TradeStatus = $this->trade_status_map[$val['TradeStatus']];
            if ($val['TradeType'] == self::TRADE_TYPE_WITHDRAW && $val['TradeStatus'] == self::TS_CONFIRM_SUCCESS) {
                $TradeStatus = '撤单成功';
            }
            if ($val['TradeType'] == self::TRADE_TYPE_BUY && $val['TradeStatus'] == self::TS_NOHANDLE && $val['DeductMoney'] == 2) {
                $TradeStatus = '申购中';
            }
            if ($val['TradeType'] == self::TRADE_TYPE_SALE && $val['TradeStatus'] == self::TS_NOHANDLE && $val['DeductMoney'] == 2) {
                $TradeStatus = '赎回中';
            }
            $list[$key]['trade_status_text'] = $TradeStatus;
            $list[$key]['deduct_money_text'] = $val['DeductMoney'] == 2 ? '已汇款' : '未汇款';
        }
        return $rs == "all" ? $list : $list[0];
    }

    /**
     * 交易记录总数
     * @param $where
     * @return int
     */
    public function getUserFundCodeEveryDayProfitCount($where)
    {
        $instid = $this->bsNo;
        $sql = "SELECT COUNT(*) AS count FROM trade_order_{$instid} AS o LEFT JOIN fund_info AS f ON o.FundCode=f.FundCode WHERE ";
        $sql .= " {$where}";
        $db = Yii::$app->db_local;
        $r = $db->createCommand($sql)->queryOne();
        return !empty($r) ? $r['count'] : 0;
    }
}
