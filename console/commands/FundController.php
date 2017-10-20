<?php
namespace console\commands;

use Yii;
use common\lib\HundSun;
use common\lib\CommFun;
use Exception;

/**
 *涉及基金信息相关任务
 */
class FundController extends Controller
{
    /**
     * 55 9 * * 1-5 (周一到周五9：45执行)
     * 任务描述：更新fund_info数据及缓存
     */
    public function actionIndex()
    {
        $t1 = time();
        set_time_limit(0);
        ini_set('memory_limit','512M');
        $logFile = 'fundinfo_'.date('Ymd').'.log';
        $db_local = Yii::$app->db_local;
        $db_juyuan = Yii::$app->db_juyuan;
        $redis = Yii::$app->redis;
        $objHs = new HundSun(0,null,1);
        $resT001 = $objHs->apiRequest('T001');
        if ($resT001['code'] ==HundSun::SUCC_CODE && !empty($resT001['items']))
        {
            $n = 0;
            foreach ($resT001['items'] as $val)
            {
                //从聚源净值表现查询所需字段--start//
                $rsSecuMain = $db_juyuan->createCommand("SELECT InnerCode,SecuAbbr,ChiSpelling,ChiNameAbbr FROM SecuMain WHERE SecuCode = '{$val['fundcode']}' AND (SecuCategory=8 OR SecuCategory = 13)")->queryOne();
                if (empty($rsSecuMain)){
                    //后端代码略过
                    continue;
                }
                $innerCode = $rsSecuMain['InnerCode'];//内部代码
                $chiSpelling = $rsSecuMain['ChiSpelling'];//拼音简称
                $fundName = $rsSecuMain['ChiNameAbbr'];//基金名称
                //聚源MF_FundArchives 查询最低持有份额\基金类型
                $rsArchives = $db_juyuan->createCommand("SELECT LowestSumForHolding,FundTypeCode,FundType,FundNature,InvestAdvisorCode FROM MF_FundArchives WHERE InnerCode = '{$innerCode}'")->queryOne();
                //概况附表查询
                $rsArchivesAttach = $db_juyuan->createCommand("SELECT * FROM MF_FundArchivesAttach WHERE InnerCode= '{$innerCode}' AND TypeCode = '10' ORDER BY StartDate DESC LIMIT 1")->queryOne();
//                 $minHoldShare = isset($rsArchives['LowestSumForHolding'])?$rsArchives['LowestSumForHolding']:-404;//最小持有份额
                $fundTypeCode = isset($rsArchivesAttach['DataCode'])?$rsArchivesAttach['DataCode']:-404;//基金类别(代码)
                $fundType = isset($rsArchivesAttach['DataName'])?$rsArchivesAttach['DataName']:-404;//基金类别(字符串)
                $fundNature = isset($rsArchives['FundNature'])?$rsArchives['FundNature']:-404;//基金性质
                $rsAdvisor = $db_juyuan->createCommand("SELECT InvestAdvisorAbbrName FROM MF_InvestAdvisorOutline WHERE InvestAdvisorCode = '{$rsArchives['InvestAdvisorCode']}' ")->queryOne();
                $investAdvisorName = isset($rsAdvisor['InvestAdvisorAbbrName'])?$rsAdvisor['InvestAdvisorAbbrName']:-404;//基金管理人
                if ($fundTypeCode =='1109' || $fundTypeCode =='1106')
                {
                    //货币基金 MF_MMYieldPerformance
                    for($i=0;$i>-10;$i--)
                    {
                        $tDay = date('Ymd',strtotime("{$i} day"));
                        $rsMMYieldPerfor = $db_juyuan->createCommand("SELECT * FROM MF_MMYieldPerformance WHERE InnerCode = '{$innerCode}' AND TradingDay = '{$tDay}'")->queryOne();
                        if(!empty($rsMMYieldPerfor)){
                            break;
                        }
                    }
                    if (empty($rsMMYieldPerfor)){
                        parent::commandLog("聚源MF_MMYieldPerformance表无法找到该基金信息:innercode:{$innerCode}",1,$logFile);
                        $pernetValue=$tradingDay=$dailyProfit=$latestWeeklyYield=$rRInSelectedWeek=$rRInSingleWeek=$rRInSingleMonth=$rRInSelectedMonth=$rRInThreeMonth=$rRInSixMonth=$rRInSingleYear=$rRSinceThisYear=$rRSinceStart=-404;
                    }else {
                        $pernetValue= ($rsMMYieldPerfor['UnitNV']===null)?-404:$rsMMYieldPerfor['UnitNV'];
                        $tradingDay= ($rsMMYieldPerfor['TradingDay']===null)?-404:$rsMMYieldPerfor['TradingDay'];
                        $rRInSelectedWeek = ($rsMMYieldPerfor['RRInThisWeek']===null)?-404:$rsMMYieldPerfor['RRInThisWeek'];
                        $rRInSingleWeek = ($rsMMYieldPerfor['RRInSingleWeek']===null)?-404:$rsMMYieldPerfor['RRInSingleWeek'];
                        $rRInSingleMonth = ($rsMMYieldPerfor['RRInSingleMonth']===null)?-404:$rsMMYieldPerfor['RRInSingleMonth'];
                        $rRInSelectedMonth = ($rsMMYieldPerfor['RRInThisMonth']===null)?-404:$rsMMYieldPerfor['RRInThisMonth'];
                        $rRInThreeMonth = ($rsMMYieldPerfor['RRInThreeMonth']===null)?-404:$rsMMYieldPerfor['RRInThreeMonth'];
                        $rRInSixMonth = ($rsMMYieldPerfor['RRInSixMonth']===null)?-404:$rsMMYieldPerfor['RRInSixMonth'];
                        $rRInSingleYear = ($rsMMYieldPerfor['RRInSingleYear']===null)?-404:$rsMMYieldPerfor['RRInSingleYear'];
                        $rRSinceThisYear = ($rsMMYieldPerfor['RRSinceThisYear']===null)?-404:$rsMMYieldPerfor['RRSinceThisYear'];
                        $rRSinceStart = ($rsMMYieldPerfor['RRSinceStart']===null)?-404:$rsMMYieldPerfor['RRSinceStart'];
                        $dailyProfit = ($rsMMYieldPerfor['DailyProfit'] ===null)?-404:$rsMMYieldPerfor['DailyProfit'];//万份收益
                        $latestWeeklyYield = ($rsMMYieldPerfor['LatestWeeklyYield'] ===null)?-404:$rsMMYieldPerfor['LatestWeeklyYield'];//七日年化
                    }
                }else {
                    //非货币基金  MF_NetValuePerformance
                    $rsNetValuePerfor = $db_juyuan->createCommand("SELECT * FROM MF_NetValuePerformance WHERE InnerCode = '{$innerCode}'")->queryOne();
                    if (empty($rsNetValuePerfor))
                    {
                        parent::commandLog("聚源MF_NetValuePerformance表无法找到该基金信息:innercode:{$innerCode}",1,$logFile);
                        $pernetValue=$tradingDay=$nVDailyGrowthRate=$rRInSelectedWeek=$rRInSingleWeek=$rRInSingleMonth=$rRInSelectedMonth=$rRInThreeMonth=$rRInSixMonth=$rRInSingleYear=$rRSinceThisYear=$rRSinceStart=-404;
                    }else {
                        $pernetValue= ($rsNetValuePerfor['UnitNV']===null)?-404:$rsNetValuePerfor['UnitNV'];
                        $tradingDay= ($rsNetValuePerfor['TradingDay']===null)?-404:$rsNetValuePerfor['TradingDay'];
                        $nVDailyGrowthRate = ($rsNetValuePerfor['NVDailyGrowthRate']===null)?-404:$rsNetValuePerfor['NVDailyGrowthRate'];
                        $rRInSelectedWeek = ($rsNetValuePerfor['RRInSelectedWeek']===null)?-404:$rsNetValuePerfor['RRInSelectedWeek'];
                        $rRInSingleWeek = ($rsNetValuePerfor['RRInSingleWeek']===null)?-404:$rsNetValuePerfor['RRInSingleWeek'];
                        $rRInSingleMonth = ($rsNetValuePerfor['RRInSingleMonth']===null)?-404:$rsNetValuePerfor['RRInSingleMonth'];
                        $rRInSelectedMonth = ($rsNetValuePerfor['RRInSelectedMonth']===null)?-404:$rsNetValuePerfor['RRInSelectedMonth'];
                        $rRInThreeMonth = ($rsNetValuePerfor['RRInThreeMonth']===null)?-404:$rsNetValuePerfor['RRInThreeMonth'];
                        $rRInSixMonth = ($rsNetValuePerfor['RRInSixMonth']===null)?-404:$rsNetValuePerfor['RRInSixMonth'];
                        $rRInSingleYear = ($rsNetValuePerfor['RRInSingleYear']===null)?-404:$rsNetValuePerfor['RRInSingleYear'];
                        $rRSinceThisYear = ($rsNetValuePerfor['RRSinceThisYear']===null)?-404:$rsNetValuePerfor['RRSinceThisYear'];
                        $rRSinceStart = ($rsNetValuePerfor['RRSinceStart']===null)?-404:$rsNetValuePerfor['RRSinceStart'];
                    }
                }
                //从聚源净值表现查询所需字段--end//
                    //S022查询交易限制字段--start//
                    $businFlagArray = ['020','022','024','090'];
                    $minSubscribAmount=$minPurchaseAmount=$minAddPurchaseAmount=$minRedemeShare=$minValuagrAmount=$minAddValuagrAmount=0;
                    foreach ($businFlagArray as $flagVal)
                    {
                        $resS022 = $objHs->apiRequest('S022',['fundcode'=>$val['fundcode'],'businflag'=>$flagVal],true);
                        if ($resS022['code']==HundSun::SUCC_CODE)
                        {
                            if (!empty($resS022['limits']))
                            {
                                switch ($flagVal)
                                {
                                   case '020':
                                       $minSubscribAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       break;
                                   case '022':
                                       $minPurchaseAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       $minAddPurchaseAmount=isset($resS022['limits'][0]['sndMinValue'])?$resS022['limits'][0]['sndMinValue']:-404;
                                       break;
                                   case '024':
                                       $minRedemeShare=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       break;
                                   case '090':
                                       $minValuagrAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       $minAddValuagrAmount=isset($resS022['limits'][0]['sndMinValue'])?$resS022['limits'][0]['sndMinValue']:-404;
                                       break;
                                }
                            }
                        }else {
                            parent::commandLog("S022查询出错fundcode:{$val['fundcode']},result=".var_export($resS022),1,$logFile);
                        }
                    }
//                 //更改为聚源数据--start//
//                 $juyuan = $db_juyuan->createCommand("SELECT LowestSumSubLL,LowestSumPurLL,LowestSumRedemption,LowestSumForHolding FROM MF_FundArchives WHERE InnerCode = '{$innerCode}'")->queryOne();
//                 $minSubscribAmount = isset($juyuan['LowestSumSubLL'])?$juyuan['LowestSumSubLL']:0;//最低认购额
//                 $minPurchaseAmount = isset($juyuan['LowestSumPurLL'])?$juyuan['LowestSumPurLL']:0;//最低申购额
//                 $minAddPurchaseAmount = 0;//最低申购追加额
//                 $minRedemeShare = isset($juyuan['LowestSumRedemption'])?$juyuan['LowestSumRedemption']:0;//最低赎回额
//                 $minValuagrAmount = 0;//最低定投申购额
//                 $minAddValuagrAmount = 0;//最低定投追加
//                 //更改为聚源数据--end//
                //S010查询基金信息--start//
                $resS010 = $objHs->apiRequest('S010',['fundcode'=>$val['fundcode']],false);
                if ($resS010['code'] == HundSun::SUCC_CODE && !empty($resS010['returnlist'][0]))
                {
                    $subScribeState = $resS010['returnlist'][0]['subscribestate'];//认购状态
                    $declareState = $resS010['returnlist'][0]['subscribestate'];//申购状态
                    $valuagrState = $resS010['returnlist'][0]['valuagrstate'];//定投状态
                    $withDrawState = $resS010['returnlist'][0]['withdrawstate'];//赎回状态
                    $minHoldShare = $resS010['returnlist'][0]['minshare'];//最小持有份额
                }else {
                    parent::commandLog("S010查询出错fundcode:{$val['fundcode']}",1,$logFile);
                    $subScribeState=$declareState=$valuagrState=$withDrawState=$minHoldShare=-404;
                }
                //S010查询基金信息--end//
                //管理费、销售服务费记录
                $manageFee = $marketFee = -404;
                $feeSql = "SELECT ChargeRateType,MinChargeRate FROM MF_ChargeRateNew WHERE InnerCode = '{$innerCode}' ".
                    "AND IfExecuted =1 AND ClientType = 10 AND (ChargeRateType = '15000' OR ChargeRateType = '19000')";
                $feeRs = $db_juyuan->createCommand($feeSql)->queryAll();
                if (!empty($feeRs))
                {
                    foreach ($feeRs as $feeval)
                    {
                        if ($feeval['ChargeRateType']=='15000')
                        {
                            if (isset($feeval['MinChargeRate'])){
                                $manageFee = $feeval['MinChargeRate'];
                            }
                        }
                        if ($feeval['ChargeRateType']=='19000')
                        {
                            if (isset($feeval['MinChargeRate'])){
                                $marketFee = $feeval['MinChargeRate'];
                            }
                        }
                    }
                }
                //T001基本字段数据获取
                $fundCode = $val['fundcode'];//基金代码
                $fundState = $val['fundstate'];//基金状态
                $shareType = $val['sharetype'];//收费类型
                $fundRiskLevel = $val['risklevel'];//风险类别
                //fund_info表字段数组
                $fundInfoParam = ['FundCode'=>$fundCode,'InnerCode'=>$innerCode,'FundName'=>$fundName,'ChiSpelling'=>$chiSpelling,'PernetValue'=>$pernetValue,'TradingDay'=>$tradingDay,
                    'NVDailyGrowthRate'=>isset($nVDailyGrowthRate)?$nVDailyGrowthRate:-404,'RRInSingleWeek'=>$rRInSingleWeek,'RRInSelectedWeek'=>$rRInSelectedWeek,
                    'RRInSingleMonth'=>$rRInSingleMonth,'RRInSelectedMonth'=>$rRInSelectedMonth,'RRInThreeMonth'=>$rRInThreeMonth,
                    'RRInSixMonth'=>$rRInSixMonth,'RRInSingleYear'=>$rRInSingleYear,'RRSinceThisYear'=>$rRSinceThisYear,'RRSinceStart'=>$rRSinceStart,'DailyProfit'=>isset($dailyProfit)?$dailyProfit:-404,
                    'LatestWeeklyYield'=>isset($latestWeeklyYield)?$latestWeeklyYield:-404,'FundRiskLevel'=>$fundRiskLevel,'FundState'=>$fundState,'ShareType'=>$shareType,'DeclareState'=>$declareState,
                    'SubScribeState'=>$subScribeState,'ValuagrState'=>$valuagrState,'WithDrawState'=>$withDrawState,'MinHoldShare'=>$minHoldShare,'ManageFee'=>$manageFee,'MarketFee'=>$marketFee,
                    'MinRedemeShare'=>$minRedemeShare,'MinPurchaseAmount'=>$minPurchaseAmount,'MinSubscribAmount'=>$minSubscribAmount,'MinAddPurchaseAmount'=>$minAddPurchaseAmount,
                    'MinValuagrAmount'=>$minValuagrAmount,'MinAddValuagrAmount'=>$minAddValuagrAmount,'FundTypeCode'=>$fundTypeCode,'FundType'=>$fundType,'InvestAdvisorName'=>$investAdvisorName,
                    'FundNature'=>$fundNature,'SysTime'=>date('Y-m-d H:i:s')
                ];
                $rsFundInfo = $db_local->createCommand("SELECT * FROM fund_info WHERE FundCode = '{$val['fundcode']}'")->queryOne();
                if (empty($rsFundInfo))
                {
                    //为空插入新数据
                    $fundInfoField = CommFun::JoinInsertStr($fundInfoParam,1);
                    $FundInfoSql = "INSERT INTO `fund_info` ({$fundInfoField['fields']}) VALUES ({$fundInfoField['values']})";
                }else {
                    //修改原数据
                    $fundInfoField = CommFun::JoinUpdateStr($fundInfoParam,1);
                    $FundInfoSql = "UPDATE `fund_info` SET {$fundInfoField} WHERE FundCode = '{$val['fundcode']}'";
                }
                try {
                    $db_local->createCommand($FundInfoSql)->execute();
                } catch (Exception $e) {
                    parent::commandLog($e->getMessage(),1,$logFile);
                }
                unset($val,$fundInfoParam);
                parent::commandLog("更新fund_info sql:{$FundInfoSql}",0,$logFile);
                $n++;
            }
            unset($objHs);
            $fundInfoArr = $db_local->createCommand("SELECT * FROM fund_info ")->queryAll();
            if (!empty($fundInfoArr))
            {
                $b_type_fund = [];
                foreach ($fundInfoArr as $fundvalue) {
                    $redis->hset('fund_info',$fundvalue['FundCode'],json_encode($fundvalue,JSON_UNESCAPED_UNICODE));
                    if (strstr($fundvalue['FundName'], 'B')){
                        $redis->hset('b_type_fund',$fundvalue['FundCode'],$fundvalue['FundCode']);
                    }
                }
            }
            $loginfo = '共更新'.$n.'条数据 耗时'.(time()-$t1).' s';
            parent::commandLog($loginfo,0,$logFile);
        }else {
            parent::commandLog("T001接口返回为空,resT001:".var_export($resT001,true),1,$logFile);
            return false;
        }
    }
    /**
     * 获取当天最新行情数据
     * 00 23 * * * 每天晚上11点更新
     */
    public function actionGetdaymarket()
    {
        $t1 = time();
        set_time_limit(0);
        $logFile = 'Getdaymarket_'.date('Ymd').'.log';
        $db_local = Yii::$app->db_local;
        $db_juyuan = Yii::$app->db_juyuan;
        $redis = Yii::$app->redis;
        $fundArr = $db_local->createCommand("SELECT * FROM `fund_info`")->queryAll();
        if (empty($fundArr))
        {
            return false;
        }
        $n = 0;//计数
        foreach ($fundArr as $fundVal)
        {
            $innerCode = $fundVal['InnerCode'];
            //货基、理财债券型
            if ($fundVal['FundTypeCode'] =='1109' || $fundVal['FundTypeCode'] =='1106')
            {
                //货币基金 MF_MMYieldPerformance
                for($i=0;$i>-10;$i--)
                {
                    $tDay = date('Ymd',strtotime("{$i} day"));
                    $rsMMYieldPerfor = $db_juyuan->createCommand("SELECT * FROM MF_MMYieldPerformance WHERE InnerCode = '{$innerCode}' AND TradingDay = '{$tDay}'")->queryOne();
                    if(!empty($rsMMYieldPerfor)){
                        break;
                    }
                }
                if (empty($rsMMYieldPerfor)){
                    //Yii::error("聚源MF_MMYieldPerformance 表无法找到该基金信息:innercode:{$innerCode}",__METHOD__);
                    $pernetValue=$tradingDay=$dailyProfit=$latestWeeklyYield=$rRInSelectedWeek=$rRInSingleWeek=$rRInSingleMonth=$rRInSelectedMonth=$rRInThreeMonth=$rRInSixMonth=$rRInSingleYear=$rRSinceThisYear=$rRSinceStart=-404;
                }else {
                    $pernetValue= ($rsMMYieldPerfor['UnitNV']===null)?-404:$rsMMYieldPerfor['UnitNV'];
                    $tradingDay= ($rsMMYieldPerfor['TradingDay']===null)?-404:$rsMMYieldPerfor['TradingDay'];
                    $rRInSelectedWeek = ($rsMMYieldPerfor['RRInThisWeek']===null)?-404:$rsMMYieldPerfor['RRInThisWeek'];
                    $rRInSingleWeek = ($rsMMYieldPerfor['RRInSingleWeek']===null)?-404:$rsMMYieldPerfor['RRInSingleWeek'];
                    $rRInSingleMonth = ($rsMMYieldPerfor['RRInSingleMonth']===null)?-404:$rsMMYieldPerfor['RRInSingleMonth'];
                    $rRInSelectedMonth = ($rsMMYieldPerfor['RRInThisMonth']===null)?-404:$rsMMYieldPerfor['RRInThisMonth'];
                    $rRInThreeMonth = ($rsMMYieldPerfor['RRInThreeMonth']===null)?-404:$rsMMYieldPerfor['RRInThreeMonth'];
                    $rRInSixMonth = ($rsMMYieldPerfor['RRInSixMonth']===null)?-404:$rsMMYieldPerfor['RRInSixMonth'];
                    $rRInSingleYear = ($rsMMYieldPerfor['RRInSingleYear']===null)?-404:$rsMMYieldPerfor['RRInSingleYear'];
                    $rRSinceThisYear = ($rsMMYieldPerfor['RRSinceThisYear']===null)?-404:$rsMMYieldPerfor['RRSinceThisYear'];
                    $rRSinceStart = ($rsMMYieldPerfor['RRSinceStart']===null)?-404:$rsMMYieldPerfor['RRSinceStart'];
                    $dailyProfit = ($rsMMYieldPerfor['DailyProfit'] ===null)?-404:$rsMMYieldPerfor['DailyProfit'];//万份收益
                    $latestWeeklyYield = ($rsMMYieldPerfor['LatestWeeklyYield'] ===null)?-404:$rsMMYieldPerfor['LatestWeeklyYield'];//七日年化
                    parent::commandLog("innercode:{$innerCode}--TradingDay:{$tradingDay}--UpdateTime:{$rsMMYieldPerfor['UpdateTime']}",0,$logFile);
                }
            }else {
                //非货币基金  MF_NetValuePerformance
                $rsNetValuePerfor = $db_juyuan->createCommand("SELECT * FROM MF_NetValuePerformance WHERE InnerCode = '{$innerCode}'")->queryOne();
                if (empty($rsNetValuePerfor))
                {
                    Yii::error("聚源MF_NetValuePerformance表无法找到该基金信息:innercode:{$innerCode}",__METHOD__);
                    $pernetValue=$tradingDay=$nVDailyGrowthRate=$rRInSelectedWeek=$rRInSingleWeek=$rRInSingleMonth=$rRInSelectedMonth=$rRInThreeMonth=$rRInSixMonth=$rRInSingleYear=$rRSinceThisYear=$rRSinceStart=-404;
                }else {
                    $pernetValue= ($rsNetValuePerfor['UnitNV']===null)?-404:$rsNetValuePerfor['UnitNV'];
                    $tradingDay= ($rsNetValuePerfor['TradingDay']===null)?-404:$rsNetValuePerfor['TradingDay'];
                    $nVDailyGrowthRate = ($rsNetValuePerfor['NVDailyGrowthRate']===null)?-404:$rsNetValuePerfor['NVDailyGrowthRate'];
                    $rRInSelectedWeek = ($rsNetValuePerfor['RRInSelectedWeek']===null)?-404:$rsNetValuePerfor['RRInSelectedWeek'];
                    $rRInSingleWeek = ($rsNetValuePerfor['RRInSingleWeek']===null)?-404:$rsNetValuePerfor['RRInSingleWeek'];
                    $rRInSingleMonth = ($rsNetValuePerfor['RRInSingleMonth']===null)?-404:$rsNetValuePerfor['RRInSingleMonth'];
                    $rRInSelectedMonth = ($rsNetValuePerfor['RRInSelectedMonth']===null)?-404:$rsNetValuePerfor['RRInSelectedMonth'];
                    $rRInThreeMonth = ($rsNetValuePerfor['RRInThreeMonth']===null)?-404:$rsNetValuePerfor['RRInThreeMonth'];
                    $rRInSixMonth = ($rsNetValuePerfor['RRInSixMonth']===null)?-404:$rsNetValuePerfor['RRInSixMonth'];
                    $rRInSingleYear = ($rsNetValuePerfor['RRInSingleYear']===null)?-404:$rsNetValuePerfor['RRInSingleYear'];
                    $rRSinceThisYear = ($rsNetValuePerfor['RRSinceThisYear']===null)?-404:$rsNetValuePerfor['RRSinceThisYear'];
                    $rRSinceStart = ($rsNetValuePerfor['RRSinceStart']===null)?-404:$rsNetValuePerfor['RRSinceStart'];
                    parent::commandLog("innercode:{$innerCode}--TradingDay:{$tradingDay}--UpdateTime:{$rsNetValuePerfor['UpdateTime']}",0,$logFile);
                }
            }
            //管理费、销售服务费记录
            $manageFee = $marketFee = -404;
            $feeSql = "SELECT ChargeRateType,MinChargeRate FROM MF_ChargeRateNew WHERE InnerCode = '{$innerCode}' ".
                "AND IfExecuted =1 AND ClientType = 10 AND (ChargeRateType = '15000' OR ChargeRateType = '19000')";
            $feeRs = $db_juyuan->createCommand($feeSql)->queryAll();
            if (!empty($feeRs))
            {
                foreach ($feeRs as $feeval)
                {
                    if ($feeval['ChargeRateType']=='15000')
                    {
                        if (isset($feeval['MinChargeRate'])){
                            $manageFee = $feeval['MinChargeRate'];
                        }
                    }
                    if ($feeval['ChargeRateType']=='19000')
                    {
                        if (isset($feeval['MinChargeRate'])){
                            $marketFee = $feeval['MinChargeRate'];
                        }
                    }
                }
            }
            $fundInfoParam = ['PernetValue'=>$pernetValue,'TradingDay'=>$tradingDay,'NVDailyGrowthRate'=>isset($nVDailyGrowthRate)?$nVDailyGrowthRate:-404,'RRInSingleWeek'=>$rRInSingleWeek,'RRInSelectedWeek'=>$rRInSelectedWeek,
                    'RRInSingleMonth'=>$rRInSingleMonth,'RRInSelectedMonth'=>$rRInSelectedMonth,'RRInThreeMonth'=>$rRInThreeMonth,'RRInSixMonth'=>$rRInSixMonth,'RRInSingleYear'=>$rRInSingleYear,'RRSinceThisYear'=>$rRSinceThisYear,
                    'RRSinceStart'=>$rRSinceStart,'DailyProfit'=>isset($dailyProfit)?$dailyProfit:-404,'MarketFee'=>$marketFee,'ManageFee'=>$manageFee,
                    'LatestWeeklyYield'=>isset($latestWeeklyYield)?$latestWeeklyYield:-404,'SysTime'=>date('Y-m-d H:i:s')
                ];
            //修改原数据
            $fundInfoField = CommFun::JoinUpdateStr($fundInfoParam,1);
            $upSql = "UPDATE `fund_info` SET {$fundInfoField} WHERE FundCode = '{$fundVal['FundCode']}'";
            $db_local->createCommand($upSql)->execute();
            $n++;
        }
        //更新缓存
        $fundInfoArr = $db_local->createCommand("SELECT * FROM fund_info ")->queryAll();
        if (!empty($fundInfoArr))
        {
            foreach ($fundInfoArr as $value) {
                $redis->hset('fund_info',$value['FundCode'],json_encode($value,JSON_UNESCAPED_UNICODE));
            }
        }
        parent::commandLog("执行完成共修改{$n}条数据，共耗时".(time()-$t1).'秒',0,$logFile);
    }
    /**
     * 手动执行,恒生开盘后
     * 任务描述：优化后更新fund_info数据及缓存，已有基金更新状态，新添基金插入所有字段数据
     */
    public function actionOpen()
    {
        $t1 = time();
        set_time_limit(0);
        ini_set('memory_limit','512M');
        $logFile = 'fundinfo_'.date('Ymd').'.log';
        $db_local = Yii::$app->db_local;
        $db_juyuan = Yii::$app->db_juyuan;
        $redis = Yii::$app->redis;
        $objHs = new HundSun(0,null,1);
        $resT001 = $objHs->apiRequest('T001');
        if ($resT001['code'] ==HundSun::SUCC_CODE && !empty($resT001['items']))
        {
            $n = 0;
            foreach ($resT001['items'] as $val)
            {
                $rsFundInfo = $db_local->createCommand("SELECT * FROM fund_info WHERE FundCode = '{$val['fundcode']}'")->queryOne();
                if (empty($rsFundInfo))
                {
                    //从聚源净值表现查询所需字段--start//
                    $rsSecuMain = $db_juyuan->createCommand("SELECT InnerCode,SecuAbbr,ChiSpelling,ChiNameAbbr FROM SecuMain WHERE SecuCode = '{$val['fundcode']}' AND (SecuCategory=8 OR SecuCategory = 13)")->queryOne();
                    if (empty($rsSecuMain)){
                        //后端代码略过
                        continue;
                    }
                    $innerCode = $rsSecuMain['InnerCode'];//内部代码
                    $chiSpelling = $rsSecuMain['ChiSpelling'];//拼音简称
                    $fundName = $rsSecuMain['ChiNameAbbr'];//基金名称
                    //聚源MF_FundArchives 查询最低持有份额\基金类型
                    $rsArchives = $db_juyuan->createCommand("SELECT LowestSumForHolding,FundTypeCode,FundType,FundNature,InvestAdvisorCode FROM MF_FundArchives WHERE InnerCode = '{$innerCode}'")->queryOne();
                    //概况附表查询
                    $rsArchivesAttach = $db_juyuan->createCommand("SELECT * FROM MF_FundArchivesAttach WHERE InnerCode= '{$innerCode}' AND TypeCode = '10' ORDER BY StartDate DESC LIMIT 1")->queryOne();
                    $fundTypeCode = isset($rsArchivesAttach['DataCode'])?$rsArchivesAttach['DataCode']:-404;//基金类别(代码)
                    $fundType = isset($rsArchivesAttach['DataName'])?$rsArchivesAttach['DataName']:-404;//基金类别(字符串)
                    $fundNature = isset($rsArchives['FundNature'])?$rsArchives['FundNature']:-404;//基金性质
                    $rsAdvisor = $db_juyuan->createCommand("SELECT InvestAdvisorAbbrName FROM MF_InvestAdvisorOutline WHERE InvestAdvisorCode = '{$rsArchives['InvestAdvisorCode']}' ")->queryOne();
                    $investAdvisorName = isset($rsAdvisor['InvestAdvisorAbbrName'])?$rsAdvisor['InvestAdvisorAbbrName']:-404;//基金管理人
                    if ($fundTypeCode =='1109' || $fundTypeCode =='1106')
                    {
                        //货币基金 MF_MMYieldPerformance
                        for($i=0;$i>-10;$i--)
                        {
                            $tDay = date('Ymd',strtotime("{$i} day"));
                            $rsMMYieldPerfor = $db_juyuan->createCommand("SELECT * FROM MF_MMYieldPerformance WHERE InnerCode = '{$innerCode}' AND TradingDay = '{$tDay}'")->queryOne();
                            if(!empty($rsMMYieldPerfor)){
                                break;
                            }
                        }
                        if (empty($rsMMYieldPerfor))
                        {
                            parent::commandLog("聚源MF_MMYieldPerformance表无法找到该基金信息:innercode:{$innerCode}",1,$logFile);
                            $pernetValue=$tradingDay=$dailyProfit=$latestWeeklyYield=$rRInSelectedWeek=$rRInSingleWeek=$rRInSingleMonth=$rRInSelectedMonth=$rRInThreeMonth=$rRInSixMonth=$rRInSingleYear=$rRSinceThisYear=$rRSinceStart=-404;
                        }else {
                            $pernetValue= ($rsMMYieldPerfor['UnitNV']===null)?-404:$rsMMYieldPerfor['UnitNV'];
                            $tradingDay= ($rsMMYieldPerfor['TradingDay']===null)?-404:$rsMMYieldPerfor['TradingDay'];
                            $rRInSelectedWeek = ($rsMMYieldPerfor['RRInThisWeek']===null)?-404:$rsMMYieldPerfor['RRInThisWeek'];
                            $rRInSingleWeek = ($rsMMYieldPerfor['RRInSingleWeek']===null)?-404:$rsMMYieldPerfor['RRInSingleWeek'];
                            $rRInSingleMonth = ($rsMMYieldPerfor['RRInSingleMonth']===null)?-404:$rsMMYieldPerfor['RRInSingleMonth'];
                            $rRInSelectedMonth = ($rsMMYieldPerfor['RRInThisMonth']===null)?-404:$rsMMYieldPerfor['RRInThisMonth'];
                            $rRInThreeMonth = ($rsMMYieldPerfor['RRInThreeMonth']===null)?-404:$rsMMYieldPerfor['RRInThreeMonth'];
                            $rRInSixMonth = ($rsMMYieldPerfor['RRInSixMonth']===null)?-404:$rsMMYieldPerfor['RRInSixMonth'];
                            $rRInSingleYear = ($rsMMYieldPerfor['RRInSingleYear']===null)?-404:$rsMMYieldPerfor['RRInSingleYear'];
                            $rRSinceThisYear = ($rsMMYieldPerfor['RRSinceThisYear']===null)?-404:$rsMMYieldPerfor['RRSinceThisYear'];
                            $rRSinceStart = ($rsMMYieldPerfor['RRSinceStart']===null)?-404:$rsMMYieldPerfor['RRSinceStart'];
                            $dailyProfit = ($rsMMYieldPerfor['DailyProfit'] ===null)?-404:$rsMMYieldPerfor['DailyProfit'];//万份收益
                            $latestWeeklyYield = ($rsMMYieldPerfor['LatestWeeklyYield'] ===null)?-404:$rsMMYieldPerfor['LatestWeeklyYield'];//七日年化
                        }
                    }else {
                        //非货币基金  MF_NetValuePerformance
                        $rsNetValuePerfor = $db_juyuan->createCommand("SELECT * FROM MF_NetValuePerformance WHERE InnerCode = '{$innerCode}'")->queryOne();
                        if (empty($rsNetValuePerfor))
                        {
                            parent::commandLog("聚源MF_NetValuePerformance表无法找到该基金信息:innercode:{$innerCode}",1,$logFile);
                            $pernetValue=$tradingDay=$nVDailyGrowthRate=$rRInSelectedWeek=$rRInSingleWeek=$rRInSingleMonth=$rRInSelectedMonth=$rRInThreeMonth=$rRInSixMonth=$rRInSingleYear=$rRSinceThisYear=$rRSinceStart=-404;
                        }else {
                            $pernetValue= ($rsNetValuePerfor['UnitNV']===null)?-404:$rsNetValuePerfor['UnitNV'];
                            $tradingDay= ($rsNetValuePerfor['TradingDay']===null)?-404:$rsNetValuePerfor['TradingDay'];
                            $nVDailyGrowthRate = ($rsNetValuePerfor['NVDailyGrowthRate']===null)?-404:$rsNetValuePerfor['NVDailyGrowthRate'];
                            $rRInSelectedWeek = ($rsNetValuePerfor['RRInSelectedWeek']===null)?-404:$rsNetValuePerfor['RRInSelectedWeek'];
                            $rRInSingleWeek = ($rsNetValuePerfor['RRInSingleWeek']===null)?-404:$rsNetValuePerfor['RRInSingleWeek'];
                            $rRInSingleMonth = ($rsNetValuePerfor['RRInSingleMonth']===null)?-404:$rsNetValuePerfor['RRInSingleMonth'];
                            $rRInSelectedMonth = ($rsNetValuePerfor['RRInSelectedMonth']===null)?-404:$rsNetValuePerfor['RRInSelectedMonth'];
                            $rRInThreeMonth = ($rsNetValuePerfor['RRInThreeMonth']===null)?-404:$rsNetValuePerfor['RRInThreeMonth'];
                            $rRInSixMonth = ($rsNetValuePerfor['RRInSixMonth']===null)?-404:$rsNetValuePerfor['RRInSixMonth'];
                            $rRInSingleYear = ($rsNetValuePerfor['RRInSingleYear']===null)?-404:$rsNetValuePerfor['RRInSingleYear'];
                            $rRSinceThisYear = ($rsNetValuePerfor['RRSinceThisYear']===null)?-404:$rsNetValuePerfor['RRSinceThisYear'];
                            $rRSinceStart = ($rsNetValuePerfor['RRSinceStart']===null)?-404:$rsNetValuePerfor['RRSinceStart'];
                        }
                    }
                    //从聚源净值表现查询所需字段--end//
                    //S022查询交易限制字段--start//
                    $businFlagArray = ['020','022','024','090'];
                    $minSubscribAmount=$minPurchaseAmount=$minAddPurchaseAmount=$minRedemeShare=$minValuagrAmount=$minAddValuagrAmount=0;
                    foreach ($businFlagArray as $flagVal)
                    {
                        $resS022 = $objHs->apiRequest('S022',['fundcode'=>$val['fundcode'],'businflag'=>$flagVal],true);
                        if ($resS022['code']==HundSun::SUCC_CODE)
                        {
                            if (!empty($resS022['limits']))
                            {
                                switch ($flagVal)
                                {
                                   case '020':
                                       $minSubscribAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       break;
                                   case '022':
                                       $minPurchaseAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       $minAddPurchaseAmount=isset($resS022['limits'][0]['sndMinValue'])?$resS022['limits'][0]['sndMinValue']:-404;
                                       break;
                                   case '024':
                                       $minRedemeShare=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       break;
                                   case '090':
                                       $minValuagrAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                                       $minAddValuagrAmount=isset($resS022['limits'][0]['sndMinValue'])?$resS022['limits'][0]['sndMinValue']:-404;
                                       break;
                                }
                            }
                        }else {
                            parent::commandLog("S022查询出错fundcode:{$val['fundcode']},result=".var_export($resS022),1,$logFile);
                        }
                    }
                        //S022查询交易限制字段--end//
//                     //更改为聚源数据--start//
//                     $juyuan = $db_juyuan->createCommand("SELECT LowestSumSubLL,LowestSumPurLL,LowestSumRedemption,LowestSumForHolding FROM MF_FundArchives WHERE InnerCode = '{$innerCode}'")->queryOne();
//                     $minSubscribAmount = isset($juyuan['LowestSumSubLL'])?$juyuan['LowestSumSubLL']:0;//最低认购额
//                     $minPurchaseAmount = isset($juyuan['LowestSumPurLL'])?$juyuan['LowestSumPurLL']:0;//最低申购额
//                     $minAddPurchaseAmount = 0;//最低申购追加额
//                     $minRedemeShare = isset($juyuan['LowestSumRedemption'])?$juyuan['LowestSumRedemption']:0;//最低赎回额
//                     $minValuagrAmount = 0;//最低定投申购额
//                     $minAddValuagrAmount = 0;//最低定投追加
                    //更改为聚源数据--end//
                    //S010查询基金信息--start//
                    $resS010 = $objHs->apiRequest('S010',['fundcode'=>$val['fundcode']],false);
                    if ($resS010['code'] == HundSun::SUCC_CODE && !empty($resS010['returnlist'][0]))
                    {
                        $subScribeState = $resS010['returnlist'][0]['subscribestate'];//认购状态
                        $declareState = $resS010['returnlist'][0]['subscribestate'];//申购状态
                        $valuagrState = $resS010['returnlist'][0]['valuagrstate'];//定投状态
                        $withDrawState = $resS010['returnlist'][0]['withdrawstate'];//赎回状态
                        $minHoldShare = $resS010['returnlist'][0]['minshare'];//最小持有份额
                    }else {
                        parent::commandLog("S010查询出错fundcode:{$val['fundcode']}",1,$logFile);
                        $subScribeState=$declareState=$valuagrState=$withDrawState=$minHoldShare=-404;
                    }
                    //S010查询基金信息--end//
                    //管理费、销售服务费记录
                    $manageFee = $marketFee = -404;
                    $feeSql = "SELECT ChargeRateType,MinChargeRate FROM MF_ChargeRateNew WHERE InnerCode = '{$innerCode}' ".
                    "AND IfExecuted =1 AND ClientType = 10 AND (ChargeRateType = '15000' OR ChargeRateType = '19000')";
                    $feeRs = $db_juyuan->createCommand($feeSql)->queryAll();
                    if (!empty($feeRs))
                    {
                        foreach ($feeRs as $feeval)
                        {
                            if ($feeval['ChargeRateType']=='15000')
                            {
                                if (isset($feeval['MinChargeRate'])){
                                    $manageFee = $feeval['MinChargeRate'];
                                }
                            }
                            if ($feeval['ChargeRateType']=='19000')
                            {
                                if (isset($feeval['MinChargeRate'])){
                                    $marketFee = $feeval['MinChargeRate'];
                                }
                            }
                        }
                    }
                    //T001基本字段数据获取
                    $fundCode = $val['fundcode'];//基金代码
                    $fundState = $val['fundstate'];//基金状态
                    $shareType = $val['sharetype'];//收费类型
                    $fundRiskLevel = $val['risklevel'];//风险类别
                    //fund_info表字段数组
                    $fundInfoParam = ['FundCode'=>$fundCode,'InnerCode'=>$innerCode,'FundName'=>$fundName,'ChiSpelling'=>$chiSpelling,'PernetValue'=>$pernetValue,'TradingDay'=>$tradingDay,
                    'NVDailyGrowthRate'=>isset($nVDailyGrowthRate)?$nVDailyGrowthRate:-404,'RRInSingleWeek'=>$rRInSingleWeek,'RRInSelectedWeek'=>$rRInSelectedWeek,
                    'RRInSingleMonth'=>$rRInSingleMonth,'RRInSelectedMonth'=>$rRInSelectedMonth,'RRInThreeMonth'=>$rRInThreeMonth,
                    'RRInSixMonth'=>$rRInSixMonth,'RRInSingleYear'=>$rRInSingleYear,'RRSinceThisYear'=>$rRSinceThisYear,'RRSinceStart'=>$rRSinceStart,'DailyProfit'=>isset($dailyProfit)?$dailyProfit:-404,
                    'LatestWeeklyYield'=>isset($latestWeeklyYield)?$latestWeeklyYield:-404,'FundRiskLevel'=>$fundRiskLevel,'FundState'=>$fundState,'ShareType'=>$shareType,'DeclareState'=>$declareState,
                    'SubScribeState'=>$subScribeState,'ValuagrState'=>$valuagrState,'WithDrawState'=>$withDrawState,'MinHoldShare'=>$minHoldShare,'ManageFee'=>$manageFee,'MarketFee'=>$marketFee,
                    'MinRedemeShare'=>$minRedemeShare,'MinPurchaseAmount'=>$minPurchaseAmount,'MinSubscribAmount'=>$minSubscribAmount,'MinAddPurchaseAmount'=>$minAddPurchaseAmount,
                    'MinValuagrAmount'=>$minValuagrAmount,'MinAddValuagrAmount'=>$minAddValuagrAmount,'FundTypeCode'=>$fundTypeCode,'FundType'=>$fundType,'InvestAdvisorName'=>$investAdvisorName,
                    'FundNature'=>$fundNature,'SysTime'=>date('Y-m-d H:i:s')
                    ];
                    //为空插入新数据
                    $fundInfoField = CommFun::JoinInsertStr($fundInfoParam,1);
                    $FundInfoSql = "INSERT INTO `fund_info` ({$fundInfoField['fields']}) VALUES ({$fundInfoField['values']})";
                    
                }//存在只修改特定值
                else{
                    $fundInfoParam = ['FundState'=>$val['fundstate'],'ShareType'=>$val['sharetype'],'FundRiskLevel'=>$val['risklevel'],'SysTime'=>date('Y-m-d H:i:s')];
                    $fundInfoField = CommFun::JoinUpdateStr($fundInfoParam,1);
                    $FundInfoSql = "UPDATE `fund_info` SET {$fundInfoField} WHERE FundCode = '{$val['fundcode']}'";
                }
                try {
                    $db_local->createCommand($FundInfoSql)->execute();
                } catch (Exception $e) {
                    parent::commandLog($e->getMessage(),1,$logFile);
                }
                unset($val,$fundInfoParam);
                parent::commandLog("更新fund_info sql:{$FundInfoSql}",0,$logFile);
                $n++;
            }
            unset($objHs);
            $fundInfoArr = $db_local->createCommand("SELECT * FROM fund_info ")->queryAll();
            if (!empty($fundInfoArr))
            {
                $b_type_fund = [];
                foreach ($fundInfoArr as $fundvalue) {
                    $redis->hset('fund_info',$fundvalue['FundCode'],json_encode($fundvalue,JSON_UNESCAPED_UNICODE));
                    if (strstr($fundvalue['FundName'], 'B')){
                       $redis->hset('b_type_fund',$fundvalue['FundCode'],$fundvalue['FundCode']);
                    }
                }
            }
            $loginfo = '共更新'.$n.'条数据 耗时'.(time()-$t1).' s';
            parent::commandLog($loginfo,0,$logFile);
        }else {
            parent::commandLog("T001接口返回为空,resT001:".var_export($resT001,true),1,$logFile);
            return false;
        }
        //单独执行交易限制更新
//         $this->runAction('fundlimit');
    }
    /**
     * 单独更新S022/S010
     */
    public function actionFundlimit()
    {
        $t1 = time();
        set_time_limit(0);
        $logFile = 'fundlimit_'.date('Ymd').'.log';
        $db_local = Yii::$app->db_local;
        $db_juyuan = Yii::$app->db_juyuan;
        $redis = Yii::$app->redis;
        $objHs = new HundSun(0,null,1);
        $fundArr = $db_local->createCommand("SELECT * FROM `fund_info`")->queryAll();
        if (empty($fundArr))
        {
            return false;
        }
        $n = 0;//计数
        foreach ($fundArr as $val)
        {
            //S022查询交易限制字段--start//
            $businFlagArray = ['020','022','024','090'];
            $minSubscribAmount=$minPurchaseAmount=$minAddPurchaseAmount=$minRedemeShare=$minValuagrAmount=$minAddValuagrAmount=0;
            foreach ($businFlagArray as $flagVal)
            {
                $resS022 = $objHs->apiRequest('S022',['fundcode'=>$val['FundCode'],'businflag'=>$flagVal],true);
                if ($resS022['code']==HundSun::SUCC_CODE)
                {
                    if (!empty($resS022['limits']))
                    {
                        switch ($flagVal)
                        {
                           case '020':
                               $minSubscribAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                               break;
                           case '022':
                               $minPurchaseAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                               $minAddPurchaseAmount=isset($resS022['limits'][0]['sndMinValue'])?$resS022['limits'][0]['sndMinValue']:-404;
                               break;
                           case '024':
                               $minRedemeShare=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                               break;
                           case '090':
                               $minValuagrAmount=isset($resS022['limits'][0]['minValue'])?$resS022['limits'][0]['minValue']:-404;
                               $minAddValuagrAmount=isset($resS022['limits'][0]['sndMinValue'])?$resS022['limits'][0]['sndMinValue']:-404;
                               break;
                        }
                    }
                }else {
                    parent::commandLog("S022查询出错fundcode:{$val['FundCode']},result=".var_export($resS022),1,$logFile);
                }
            }
            //S022查询交易限制字段--end//
//             //更改为聚源数据--start//
//             $juyuan = $db_juyuan->createCommand("SELECT LowestSumSubLL,LowestSumPurLL,LowestSumRedemption,LowestSumForHolding FROM MF_FundArchives WHERE InnerCode = '{$val['InnerCode']}'")->queryOne();
//             $minSubscribAmount = isset($juyuan['LowestSumSubLL'])?$juyuan['LowestSumSubLL']:0;//最低认购额
//             $minPurchaseAmount = isset($juyuan['LowestSumPurLL'])?$juyuan['LowestSumPurLL']:0;//最低申购额
//             $minAddPurchaseAmount = 0;//最低申购追加额
//             $minRedemeShare = isset($juyuan['LowestSumRedemption'])?$juyuan['LowestSumRedemption']:0;//最低赎回额
//             $minValuagrAmount = 0;//最低定投申购额
//             $minAddValuagrAmount = 0;//最低定投追加
//             //更改为聚源数据--end//
            //S010查询基金信息--start//
            $resS010 = $objHs->apiRequest('S010',['fundcode'=>$val['FundCode']],false);
            if ($resS010['code'] == HundSun::SUCC_CODE && !empty($resS010['returnlist'][0]))
            {
                $subScribeState = $resS010['returnlist'][0]['subscribestate'];//认购状态
                $declareState = $resS010['returnlist'][0]['subscribestate'];//申购状态
                $valuagrState = $resS010['returnlist'][0]['valuagrstate'];//定投状态
                $withDrawState = $resS010['returnlist'][0]['withdrawstate'];//赎回状态
                $minHoldShare = $resS010['returnlist'][0]['minshare'];//最小持有份额
            }else {
                parent::commandLog("S010查询出错fundcode:{$val['FundCode']}",1,$logFile);
                $subScribeState=$declareState=$valuagrState=$withDrawState=$minHoldShare=-404;
            }
            unset($resS010);
            //S010查询基金信息--end//
            $fundInfoParam = ['DeclareState'=>$declareState,'SubScribeState'=>$subScribeState,'ValuagrState'=>$valuagrState,'WithDrawState'=>$withDrawState,'MinHoldShare'=>$minHoldShare,
                'MinRedemeShare'=>$minRedemeShare,'MinPurchaseAmount'=>$minPurchaseAmount,'MinSubscribAmount'=>$minSubscribAmount,'MinAddPurchaseAmount'=>$minAddPurchaseAmount,
                'MinValuagrAmount'=>$minValuagrAmount,'MinAddValuagrAmount'=>$minAddValuagrAmount,'SysTime'=>date('Y-m-d H:i:s')
            ];
            $fundInfoField = CommFun::JoinUpdateStr($fundInfoParam,1);
            $FundInfoSql = "UPDATE `fund_info` SET {$fundInfoField} WHERE FundCode = '{$val['FundCode']}'";
            try {
                $db_local->createCommand($FundInfoSql)->execute();
            } catch (Exception $e) {
                parent::commandLog("更新fund_info出错 sql:{$FundInfoSql}--".$e->getMessage(),1,$logFile);
            }
            parent::commandLog("更新fund_info sql:{$FundInfoSql}",0,$logFile);
            unset($val,$fundInfoParam,$FundInfoSql);
            $n++;
        }
        unset($objHs);
        $fundInfoArr = $db_local->createCommand("SELECT * FROM fund_info ")->queryAll();
        if (!empty($fundInfoArr))
        {
            $b_type_fund = [];
            foreach ($fundInfoArr as $fundvalue) {
                $redis->hset('fund_info',$fundvalue['FundCode'],json_encode($fundvalue,JSON_UNESCAPED_UNICODE));
                if (strstr($fundvalue['FundName'], 'B')){
                    $redis->hset('b_type_fund',$fundvalue['FundCode'],$fundvalue['FundCode']);
                }
            }
        }
        $loginfo = '共更新'.$n.'条数据 耗时'.(time()-$t1).' s';
        parent::commandLog($loginfo,0,$logFile);
    }
}