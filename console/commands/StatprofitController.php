<?php
namespace console\commands;

use Yii;
use common\lib\CommFun;
use Exception;
/**
 * 每天早上七点统计收益  00 8 * * * php /data/release/fund/yii Statprofit/index
 *
 */
class StatprofitController extends Controller
{
    /**
     * 查询最新用户的份额信息
	 * uid 查询用户编号
	 * fundcode 查询基金编号
	 * hzf 合作分配的平台号
     */
    public function actionIndex()
    {
		 return false;
    }
    /**
     * 每天早上七点统计收益  00 7 * * * php /data/release/fund/yii statprofit/dayprofit
     */
    public function actionDayprofit()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $db_juyuan = Yii::$app->db_juyuan;
//         $tradeDay = CommFun::getLastTradeDay();//上个交易日
        $logFile = 'Dayprofit_'.date('Ymd').'.log';
        $day = date('Y-m-d');
        //循环查找所有商户交易表
        $partners = $db_local->createCommand("SELECT * FROM `partner` where `Status` = 0")->queryAll();
        if (empty($partners))
        {
            parent::commandLog('商户信息为空',1,$logFile);
            return false;
        }
        $n = 0;//计数
        $t1 = time();
        foreach ($partners as $p)
        {
            $positionTable = 'fund_position_'.$p['Instid'];//持仓表
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '%{$positionTable}%'")->queryOne();
            //未建表跳过数据处理
            if (empty($existTable))
            {
                continue;
            }
            $resPosition = $db_local->createCommand("SELECT id,Uid,FundCode,CurrentRemainShare,UnpaidIncome,DayProfitLoss FROM `{$positionTable}` ")->queryAll();
            if (!empty($resPosition) && is_array($resPosition))
            {
                foreach ($resPosition as $valPosit)
                {
                    //份额为0的当日收益处理为0
                    if ($valPosit['CurrentRemainShare'] ==0){
                        if ($valPosit['DayProfitLoss'] !=0){
                            try {
                                $db_local->createCommand("UPDATE `{$positionTable}` SET DayProfitLoss=0 WHERE id = '{$valPosit['id']}'")->execute();
                            } catch (Exception $e) {
                                parent::commandLog($e->getMessage(),1,$logFile);
                            }
                        }
                        continue;
                    }
                    $fundInfo = CommFun::GetFundInfo($valPosit['FundCode']);
                    $fundType = 0;//0:货基/短期理财;1:股基/债基
                    $dailyProfit = 0;//万份收益
                    if (!empty($fundInfo))
                    {
                        if ($fundInfo['FundTypeCode'] =='1106' || $fundInfo['FundTypeCode'] =='1109')
                        {
                            $fundType = 0;
                            $dailyProfit = $fundInfo['DailyProfit'];
                        }else {
                            $fundType = 1;
                        }
                        $tradingDay = $fundInfo['TradingDay'];//收益所属日期
                        $innerCode = $fundInfo['InnerCode'];
                    }else {
                        //本地无数据记录日志从聚源取所需数据
                        parent::commandLog("fund_info表中无基金:{$valPosit['FundCode']}",1,$logFile);
                        $rsSecuMain = $db_juyuan->createCommand("SELECT InnerCode,SecuAbbr FROM SecuMain WHERE SecuCode = '{$valPosit['FundCode']}' AND (SecuCategory=8 OR SecuCategory = 13)")->queryOne();
                        if (empty($rsSecuMain['InnerCode'])){
                            continue;
                        }
                        $innerCode = $rsSecuMain['InnerCode'];
                        $rsArchivesAttach = $db_juyuan->createCommand("SELECT * FROM MF_FundArchivesAttach WHERE InnerCode= '{$innerCode}' AND TypeCode = '10'")->queryOne();
                        if ($rsArchivesAttach['DataCode'] =='1106' || $rsArchivesAttach['DataCode'] =='1109')
                        {
                            $fundType = 0;//货基/短期理财
                            //获取万份收益
                            //货币基金 MF_MMYieldPerformance
                            for($i=0;$i>-10;$i--)
                            {
                                $tDay = date('Ymd',strtotime("{$i} day"));
                                $rsMMYieldPerfor = $db_juyuan->createCommand("SELECT * FROM MF_MMYieldPerformance WHERE InnerCode = '{$innerCode}' AND TradingDay = '{$tDay}'")->queryOne();
                                if(!empty($rsMMYieldPerfor['DailyProfit'])){
                                    break;
                                }
                            }
                            if (empty($rsMMYieldPerfor))
                            {
                                parent::commandLog("聚源MF_MMYieldPerformance 表无法找到该基金信息:innercode:{$innerCode}",1,$logFile);
                                continue;
                            }
                            $dailyProfit = $rsMMYieldPerfor['DailyProfit'];//万份收益
                            $tradingDay = $rsMMYieldPerfor['TradingDay'];//收益所属日期
                        }else {
                            $fundType = 1;//股基/债基
                        }
                        
                    }
                    if (empty($fundType))
                    {
                        //货基/短期理财债券计算收益
                        $profitLoss = $dailyProfit*($valPosit['CurrentRemainShare'] + $valPosit['UnpaidIncome'])/10000;
                        $profitLossRate = $dailyProfit/100;//百分比
                        $positionValue = $valPosit['CurrentRemainShare'] + $valPosit['UnpaidIncome'];

                        $totalApplySum = $this->getUserTotalSum($p['Instid'], $valPosit['Uid'], $valPosit['FundCode']);
                        $TotalProfitLoss = $positionValue + $totalApplySum['sale'] - $totalApplySum['subscribe'];
                    }else {
                        //股基/债基计算收益
                        if (in_array($day,Yii::$app->params['holidays']) || date('N') >= 6)
                        {
                            continue;//节假日无收益
                        }
                        $rsNetValue = $db_juyuan->createCommand("SELECT InfoPublDate,UnitNV,EndDate FROM MF_NetValue WHERE InnerCode = '{$innerCode}' ORDER BY EndDate DESC LIMIT 2")->queryAll();
                        if (!empty($rsNetValue))
                        {
                            $yesterday = isset($rsNetValue[0]['UnitNV'])?$rsNetValue[0]['UnitNV']:0;
                            $before_yesterday = isset($rsNetValue[1]['UnitNV'])?$rsNetValue[1]['UnitNV']:0;
                            $exday = isset($rsNetValue[0]['EndDate'])?$rsNetValue[0]['EndDate']:'';
                            $tradingDay = $exday;//收益所属日期
                            if (empty($exday)){
                                parent::commandLog("MF_NetValue无法找到EndDate InnerCode={$innerCode}",1,$logFile);
                            }
                            $diviMoney = $this->Getdividend($db_local, $valPosit['FundCode'], $exday, $valPosit['CurrentRemainShare'], $p['Instid'], $valPosit['Uid'], $valPosit['id']);
                            $profitLoss = ($yesterday - $before_yesterday)*$valPosit['CurrentRemainShare']+$diviMoney;//加分红
                            $profitLossRate = empty($before_yesterday)?0:$profitLoss*100/($before_yesterday*$valPosit['CurrentRemainShare']);
                            $positionValue = $valPosit['CurrentRemainShare']*$yesterday;

                            $totalApplySum = $this->getUserTotalSum($p['Instid'], $valPosit['Uid'], $valPosit['FundCode']);
                            $TotalProfitLoss = $positionValue + $totalApplySum['sale'] - $totalApplySum['subscribe'];
                        }else {
                            parent::commandLog("聚源MF_NetValue表无法找到该基金信息:innercode:{$innerCode}",1,$logFile);
                            continue;
                        }
                    }
                    //查找是否今日已经统计该数据，存在跳出本循环继续
                    $exitProfit = $db_local->createCommand("SELECT * FROM position_profitloss_{$p['Instid']} WHERE Uid ='{$valPosit['Uid']}' AND FundCode ='{$valPosit['FundCode']}' AND TradeDay ='{$tradingDay}'")->queryOne();
                    if (!empty($exitProfit))
                    {
                        continue;
                    }
                    $inSql = "INSERT INTO `position_profitloss_{$p['Instid']}` (`PositionId`,`FundCode`,`Uid`,`ProfitLoss`,`ProfitLossRate`,`PositionValue`,`TradeDay`) VALUES ".
                    "('{$valPosit['id']}','{$valPosit['FundCode']}','{$valPosit['Uid']}','{$profitLoss}','{$profitLossRate}','{$positionValue}','{$tradingDay}')";
                    try {
                        $db_local->createCommand($inSql)->execute();
                        $db_local->createCommand("UPDATE {$positionTable} SET DayProfitLoss = '{$profitLoss}',TotalProfitLoss='{$TotalProfitLoss}',ProfitDay='{$tradingDay}' WHERE id ='{$valPosit['id']}' ")->execute();
                        parent::commandLog('基金盈亏统计sql:'.$inSql,0,$logFile);
                        $n++;
                    } catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }
                }
            }else {
                continue;
            }
        }
        parent::commandLog('共更新'.$n.'条数据 耗时'.(time()-$t1).'秒',0,$logFile);
    }

    public function getUserTotalSum($instid, $uid, $fundcode)
    {
        $db_local = Yii::$app->db_local;
        $sql = "select sum(ConfirmAmount) as confirmamount,TradeType from trade_order_{$instid} ";
        $sql .= "where Uid='{$uid}' and FundCode='{$fundcode}' AND TradeStatus in (1,2) group by TradeType";
        $one = $db_local->createCommand($sql)->queryAll();
        $rs = ['subscribe'=>0, 'sale'=>0];
        foreach ($one as $key=>$value) {
            if ($value['TradeType'] == 0 || $value['TradeType'] == 3) {
                $rs['subscribe'] += $value['confirmamount'];
            } else if ($value['TradeType'] == 1) {
                $rs['sale'] += $value['confirmamount'];
            }
        }
        return $rs;
    }
    /**
     * @param resource $db_local 数据库连接
     * @param string $fundcode 基金代码
     * @param string $exday 除息日 yyyy-mm-dd 00:00:00
     * @param string $totalshare 持有份数
     * @param string $instid 商户id
     * @param string $uid 用户id
     * @param string $positid 持仓id
     * @return float 分红金额/无分红 0
     */
    public function GetDividend($db_local,$fundcode,$exday,$totalshare,$instid,$uid,$positid)
    {
        $sql = "SELECT * FROM `dividend` WHERE FundCode = '{$fundcode}' AND ExRightDate = '{$exday}'";
        $divRes = $db_local->createCommand($sql)->queryOne();
        if (!empty($divRes))
        {
            $logFile = 'GetDividend_'.date('Ymd').'.log';
            $divMoney = sprintf('%01.2f',($totalshare/10)*$divRes['ActualRatioAfterTax']);
            //分红记录表数据start
            if ($divMoney > 0){
                $inSql = "INSERT INTO `dividend_history_{$instid}` (`PositionId`,`Uid`,`FundCode`,`ExRightDate`,`DivMoney`,`CurrentRemainShare`,`ActualRatioAfterTax`)".
                    " VALUES ('{$positid}','{$uid}','{$fundcode}','{$exday}','{$divMoney}','{$totalshare}','{$divRes['ActualRatioAfterTax']}')";
                try {
                    $db_local->createCommand($inSql)->execute();
                } catch (Exception $e) {
                    parent::commandLog($e->getMessage(),1,$logFile);
                }
            }
            //----end
            return $divMoney;
        }else {
            return 0;
        }
    }
}
