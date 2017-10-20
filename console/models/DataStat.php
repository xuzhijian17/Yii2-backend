<?php
namespace console\models;

use Yii;
use yii\base\Model;
use common\lib\CommFun;

/**
 * 数据统计类：‘佣金’，‘保有量’，‘日申购中’，‘日赎回中’，‘注册数’
 *
 * @author Jason<anwen2218@gmail.com>
 */
class DataStat extends Model 
{
    public $db;
    public $cid; //channel id 商户id
    public $fund_position_x;//用户持仓表，x-商户id，分表标识
    public $statistics_x;//用户统计表，x-商户id，分表标识
    public $trade_order_x;//用户交易信息表，x-商户id，分表标识
    public $tb_fund_market = 'fund_market'; //基金表
    public $tb_user = 'user'; //用户表
    public $today;
    public $beginToday;
    public $endToday;
    //public $beginTodayM;
    //public $endTodayM;

    public function __construct($cid = 1) 
    {
        $this->cid   = $cid;
        $this->db    = Yii::$app->db;
        $this->today = date('Y-m-d');
        $this->beginToday      = date('Y-m-d').' 00:00:00';
        $this->endToday        = date('Y-m-d').' 23:59:59';
        //$this->beginTodayM     = date('Y-m-d',strtotime("-1 day")).' 15:00:00';
        //$this->endTodayM       = date('Y-m-d').' 15:00:00';

        $this->fund_position_x = 'fund_position_'.$cid;
        $this->statistics_x    = 'statistics_'.$cid;
        $this->trade_order_x   = 'trade_order_'.$cid;
    }

    /*
     * 按照channel商户统计注册人数
     * */
    public function statsRegByCid()
    {
        $rAmount = $this->getDayUserNum();
        $isExist = $this->isExistStat();
        if($isExist)
        {
            $execSql = "update {$this->statistics_x} ";
            $execSql .= "set RegNums={$rAmount},BindNums={$rAmount} ";
            $execSql .= "where Day = '{$this->today}' ";
        }else{
            $execSql = "insert into {$this->statistics_x} (`RegNums`,`BindNums`,`Day`) ";
            $execSql .= "values ({$rAmount},{$rAmount},'{$this->today}') ";
        }
        $rs = $this->db->createCommand($execSql)->execute();
        return $rs;
    }
    /*
     * 按照channel商户‘日累计申购额’，‘日累计赎回额’，‘申购中’，‘赎回中’
     * 
     * */
    public function buySellAmount()
    {
        $apply   = $this->getDayApply();
        $confirm = $this->getDayConfirm();
        $isExist = $this->isExistStat();
        if($isExist)
        {
            $execSql = "update {$this->statistics_x} ";
            $execSql .= "set InBuyAmount={$apply['InBuyAmount']},InSellAmount={$apply['InSellAmount']},TotalBuyAmount={$confirm['TotalBuyAmount']},TotalSellAmount={$confirm['TotalSellAmount']} ";
            $execSql .= "where Day = '{$this->today}' ";
        }else{
            $execSql = "insert into {$this->statistics_x} (`InBuyAmount`,`InSellAmount`,`TotalBuyAmount`,`TotalSellAmount`,`Day`) ";
            $execSql .= "values ({$apply['InBuyAmount']},{$apply['InSellAmount']},{$confirm['TotalBuyAmount']},{$confirm['TotalSellAmount']},'{$this->today}') ";
        }
        $rs = $this->db->createCommand($execSql)->execute();
        return $rs ;
    }
    /*
     * 按照channel商户统计佣金，保有量
     *
     * */
    public function commAssetByChannel() 
    {
        $fundList = $this->listFundAmmount();
        if(is_array($fundList) && !empty($fundList))
        {
            $distributeBi   = $this->getDistribueBi($this->cid);
            $TotalAsset = 0;
            $Commission = 0;
            //T-1日保有量 = 保有份额 * T-1日（即最新）净值
            foreach($fundList as $val)
            {
                $tmpAsset    = $val['CurrentRemainShare'] * $val['PernetValue'];
                $fundCode    = $val['FundCode'];
                $TotalAsset += $tmpAsset;
               
                // T-1日佣金 = 管理费*尾佣*(T-1日保有份额)*(T-1日基金净值)*分成比例
                $manageFare     = $this->getManageFare($fundCode); 
                $tailCommission = $this->getTailCommission($fundCode);
                $Commission += $manageFare * $tailCommission * $tmpAsset * $distributeBi;
            }
            $Commission = round($Commission,2);
            $TotalAsset = round($TotalAsset,2);
            $isExist = $this->isExistStat();
            if($isExist)
            {
                $execSql = "update {$this->statistics_x} ";
                $execSql .= "set TotalAsset={$TotalAsset},Commission={$Commission} ";
                $execSql .= "where Day = '{$this->today}' ";
            }else{
                $execSql = "insert into {$this->statistics_x} (`TotalAsset`,`Commission`,`Day`) ";
                $execSql .= "values ({$TotalAsset},{$Commission},'{$this->today}') ";
            }
            $rs = $this->db->createCommand($execSql)->execute();
            return $rs;
        }
    }
    /*
     * 今日统计数据是否存在
     * return mix false or []
     * */
    private function isExistStat()
    {
        $sql  = "select * from {$this->statistics_x} where Day = '{$this->today}' for update ";
        $info = $this->db->createCommand($sql)->queryOne();
        if( is_array($info) && !empty($info) )
            return $info;
        else
            return false;
    }

    /*
     * 得到商户持有基金数量
     * 此统计在查询同步份额之后 
     * return 
     [
        ['FundCode'=>'400001','PernetValue'=>1.02,'CurrentRemainShare'=>4000],
        ['FundCode'=>'400002','PernetValue'=>1.1,'CurrentRemainShare'=>5000]
     ]
     基金代码，单日单位净值，当前份额
     *
     * */
    private function listFundAmmount() 
    {
        $sql = "select fp.FundCode,f.PernetValue,sum(fp.CurrentRemainShare) as CurrentRemainShare from ";
        $sql .= "{$this->fund_position_x} as fp left join fund_info as f on fp.FundCode=f.FundCode where fp.CurrentRemainShare>0 group by fp.FundCode";
        $list = $this->db->createCommand($sql)->queryAll();
        return $list;
    }
    /*
     * 管理费
     * 固定值，依赖于基金
     * @param fundCode
     * */
    public function getManageFare( $fundCode='' ) 
    {
        $mf = 0;
        if(!empty($fundCode))
        {
            $info = CommFun::GetFundInfo($fundCode);
            if($info)
                $mf = $info['ManageFee'];
        }
        return $mf;
    }

    /*
     * 尾佣
     * 固定值，依赖于基金
     * @param fundCode
     *
     * */
    public function getTailCommission( $fundCode='' ) 
    {
        $cf = 0 ;
        if(!empty($fundCode))
        {
            $info = CommFun::GetFundInfo($fundCode);
            if($info)
                $cf = $info['CustodyFee'];
        }
        return $cf;
    }

    /*
     * 分成比例
     * 固定值，渠道
     * cid 渠道id (institute id)
     *
     * */
    public function getDistribueBi( $cid = -1 ) 
    {
        $bi = 0;
        if($cid >=0 )
        {
            $info = CommFun::getPartnerInfo($cid);
            if($info)
                $bi = $info['Divide'];
        }
        return $bi;
    }

    /*
     * 统计‘日申购中’、‘日赎回中’ by `trade_order_*`.`ApplyTime`
     * ？？自然日统计，还是工作日的3点到3点，周末
     * return ['InBuyAmount'=>,'InSellAmount'=>]
     * */
    private function getDayApply() 
    {
        $rArr = ['InBuyAmount' => 0, 'InSellAmount' => 0];
        //交易类型0:买入;1:卖出;2:撤单;3:定投;
        //??`TradeStatus`,`HandleStatus`
        $sql = "select TradeType,sum(ApplyAmount) as ApplyAmount from {$this->trade_order_x} where TradeType in (0,1,3) and TradeStatus='9' and ApplyTime BETWEEN '{$this->beginToday}' AND '{$this->endToday}' GROUP BY TradeType;";
        $list = $this->db->createCommand($sql)->queryAll();
        if(is_array($list) && !empty($list))
        {
            foreach($list as $val)
            {
                if(0 == $val['TradeType'])
                    $rArr['InBuyAmount']  += $val['ApplyAmount'];
                else if(1 == $val['TradeType'])
                    $rArr['InSellAmount'] += $val['ApplyAmount'];
                else if(3 == $val['TradeType'])
                    $rArr['InBuyAmount']  += $val['ApplyAmount'];
            }
        }
        return $rArr;
    }

    /*
     * 统计‘日累计申购额’、‘日累计赎回额’ by `trade_order_*`.`ConfirmTime`
     * ？？自然日统计，还是工作日的3点到3点，周末
     *
     * return ['buyConfirmNetValue'=>,'sellConfirmNetValue'=>]
     * */
    private function getDayConfirm() 
    {
        $rArr = ['TotalBuyAmount' => 0, 'TotalSellAmount' => 0] ;
        //交易类型0:买入;1:卖出;2:撤单;3:定投;
        //HandleStatus:1已经确认了的
        $sql = "select TradeType,sum(ConfirmAmount) as ConfirmAmount from {$this->trade_order_x} where TradeType in (0,1,3) and ConfirmTime BETWEEN '{$this->beginToday}' AND '{$this->endToday}' and TradeStatus in (1,2,3) GROUP BY TradeType;";
        $list = $this->db->createCommand($sql)->queryAll();
        if(is_array($list) && !empty($list))
        {
            foreach($list as $val)
            {
                if(0 == $val['TradeType'])
                    $rArr['TotalBuyAmount']  += $val['ConfirmAmount'];
                else if(1 == $val['TradeType'])
                    $rArr['TotalSellAmount'] += $val['ConfirmAmount'];
                if(3 == $val['TradeType'])
                    $rArr['TotalBuyAmount']  += $val['ConfirmAmount'];
            }
        }
        return $rArr;
    }
    /*
     * 统计日注册人数 or 绑卡人
     *
     * @param int $type 'reg:0','bind:1'
     * return
     *
     * */
    private function getDayUserNum ($type = 0)
    {
        $amount = 0;
        if(1 == $type)//bind
            $timeType = 'BindTime';
        else
            $timeType = 'SysTime';

        $sql = "select count(id) as amount from `{$this->tb_user}` where {$timeType} between '{$this->beginToday}' and '{$this->endToday}' and Instid = {$this->cid};";
        $info = $this->db->createCommand($sql)->queryOne();
        if($info)
            $amount = $info['amount'];
        return $amount;
    }

    /*
     * write DataStat log 
     * @msg string
     * @level 0-info，1-error
     * @fName file name
     *
     * */
    public function logDataStat($msg='', $level=0, $fName = '') 
    {   
        $fdir='/data/log/command/';
        $td = date('Y-m-d');
        if(1 == $level)
            $lv = '/error/';
        else if(0 == $level)
            $lv = '/info/';
        if(!$fName)
            $fName = 'dataStat_'.date('Y-m-d').'.log';
        $fdir  .= $lv;    
        if(!file_exists($fdir))  
            mkdir($fdir,0777,true);   
        $fName = $fdir.$fName;
        $msg .= "\n";
        file_put_contents($fName,$msg,FILE_APPEND);
    }
}
