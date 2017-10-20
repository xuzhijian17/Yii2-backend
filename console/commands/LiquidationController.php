<?php
namespace console\commands;

use Yii;
use Exception;
use common\lib\CommFun;
use common\lib\HundSun;
/**
 * 结算功能相关类
 *
 */
class LiquidationController extends Controller
{
    public function actionIndex()
    {
        return FALSE;
    }
    /**
     * 尾佣/销售服务费按存量天数计算 自然日统计8:30
     */
    public function actionTrail()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Liquidation_Trail_'.date('Ymd').'.log';
        $tradeDay = date('Y-m-d',strtotime('-1 day'));
        //循环查找所有商户交易表
        $partners = $db_local->createCommand("SELECT * FROM `partner` where `Status` = 0")->queryAll();
        foreach ($partners as $p)
        {
            $positionTable = 'fund_position_'.$p['Instid'];//持仓表
            $liquidationTable = 'liquidation_'.$p['Instid'];//结算表
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '%{$positionTable}%'")->queryOne();
            //未建表跳过数据处理
            if (empty($existTable))
            {
                continue;
            }
            $positionRs = $db_local->createCommand("SELECT FundCode,SUM(CurrentRemainShare) AS CurrentRemainShare FROM `{$positionTable}` WHERE CurrentRemainShare >0 GROUP BY FundCode")->queryAll();
            if (!empty($positionRs) && is_array($positionRs))
            {
                foreach ($positionRs as $posival) 
                {
                    $fundInfo = CommFun::GetFundInfo($posival['FundCode']);
                    if (empty($fundInfo))
                    {
                        parent::commandLog("FundCode={$posival['FundCode']}获取不到fundinfo数据",1,$logFile);
                        continue;
                    }
                    //尾佣
                    $tailPoundage = sprintf('%01.2f',($fundInfo['ManageFee']/365)*$fundInfo['CustodyFee']*$posival['CurrentRemainShare']*$fundInfo['PernetValue']);
                    //销售服务费
                    $saleServicePoundage = sprintf('%01.2f',($fundInfo['MarketFee']/365)*$posival['CurrentRemainShare']*$fundInfo['PernetValue']);
                    if ($tailPoundage==0 && $saleServicePoundage==0)
                    {
                        continue;//都是0的跳过进入下只基金
                    }
                    $inSql = "INSERT INTO {$liquidationTable} (`FundCode`,`TradeDay`,`TailPoundage`,`SaleServicePoundage`) VALUES('{$posival['FundCode']}','{$tradeDay}','{$tailPoundage}','{$saleServicePoundage}')".
                    " ON DUPLICATE KEY UPDATE TailPoundage='{$tailPoundage}',SaleServicePoundage='{$saleServicePoundage}'";
                    try {
                        $db_local->createCommand($inSql)->execute();
                    } catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }
                    //日志记录
                    $tailLog = $inSql."-param:ManageFee({$fundInfo['ManageFee']})*CustodyFee({$fundInfo['CustodyFee']})*CurrentRemainShare({$posival['CurrentRemainShare']})*PernetValue({$fundInfo['PernetValue']})";
                    parent::commandLog($tailLog,0,$logFile);
                }
            }else {
                continue;
            }
        }
    }
    /**
     * 交易费用(认/申购费、赎回费) T+1确认数据回后统计 交易日统计18:00
     */
    public function actionTradePoundage()
    {
        if (in_array(date('Y-m-d'),Yii::$app->params['holidays']))
        {
            //节假日退出
            return false;
        }
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Liquidation_Trade_'.date('Ymd').'.log';
        $tradeDay = CommFun::getLastTradeDay();
        //循环查找所有商户交易表
        $partners = $db_local->createCommand("SELECT * FROM `partner` where `Status` = 0")->queryAll();
        foreach ($partners as $p)
        {
            $tradeTable = 'trade_order_'.$p['Instid'];//交易表
            $liquidationTable = 'liquidation_'.$p['Instid'];//结算表
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '%{$tradeTable}%'")->queryOne();
            //未建表跳过数据处理
            if (empty($existTable))
            {
                continue;
            }
            $tradeRs = $db_local->createCommand("SELECT * FROM `{$tradeTable}` WHERE TradeDay ='{$tradeDay}' AND TradeType !=2")->queryAll();
            if (empty($tradeRs)){
                continue;
            }
            foreach ($tradeRs as $tradeval)
            {
                if (in_array($tradeval['TradeStatus'], [1,2,3,5]))
                {
                    $fundinfo = CommFun::GetFundInfo($tradeval['FundCode']);
                    if (empty($fundinfo)){
                        parent::commandLog("FundCode={$tradeval['FundCode']}获取不到fundinfo数据",1,$logFile);
                    }
                    //需要结算的数据
                    $fundcode = $tradeval['FundCode'];
                    $subscrib=$purchas=$redem=$transferin=$transferout=$saleservice=0;
                    if ($tradeval['TradeType']==0)//认申购
                    {
                        if ($tradeval['CallingCode']=='120')
                        {
                            $subscrib = $tradeval['Poundage'];
                        }
                        if ($tradeval['CallingCode']=='122')
                        {
                            $purchas = $tradeval['Poundage'];
                        }
                    }elseif ($tradeval['TradeType']==3)//定投(申购)
                    {
                        $subscrib = $tradeval['Poundage'];
                    }elseif ($tradeval['TradeType']==1)//赎回
                    {
                        $redem = $tradeval['AgentSum'];
                    }
                    if($subscrib==0 && $purchas==0 && $redem==0)
                    {
                        continue;//都是0的跳过进入下只基金
                    }
                    $inSql = "INSERT INTO {$liquidationTable} (`FundCode`,`TradeDay`,`SubscribPoundage`,`PurchasPoundage`,`RedemPoundage`,".
                    "`TransferInPoundage`,`TransferOutPoundage`) VALUES ('{$tradeval['FundCode']}','{$tradeval['TradeDay']}',".
                    "'{$subscrib}','{$purchas}','{$redem}','{$transferin}','{$transferout}') ".
                    "ON DUPLICATE KEY UPDATE SubscribPoundage='{$subscrib}',PurchasPoundage='{$purchas}',RedemPoundage='{$redem}',".
                    "TransferInPoundage='{$transferin}',TransferOutPoundage='{$transferout}'";
                    try {
                        $db_local->createCommand($inSql)->execute();
                        parent::commandLog($inSql,0,$logFile);
                    } catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }
                }elseif ($tradeval['TradeStatus'] ==9)
                {
                    $unliqui = $db_local->createCommand("SELECT * FROM `unliquidation` WHERE TradeOrderId = {$tradeval['id']} AND Instid = {$p['Instid']}")->queryOne();
                    if (empty($unliqui))
                    {
                        //确认数据未回，记录到unliquidation
                        $unliSql = "INSERT INTO `unliquidation` (`Instid`,`TradeOrderId`) VALUES ('{$p['Instid']}','{$tradeval['id']}')";
                        try {
                            $db_local->createCommand($unliSql)->execute();
                            parent::commandLog($unliSql,0,$logFile);
                        } catch (Exception $e) {
                            parent::commandLog($e->getMessage(),1,$logFile);
                        }
                    }
                }else {
                    continue;//跳过的数据 
                }
            }
        }
        //处理unliquidation
        $this->Unliquidation();
    }
    /**
     * unliquidation 处理
     */
    public function Unliquidation()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Liquidation_Un_'.date('Ymd').'.log';
        $unRs = $db_local->createCommand("SELECT * FROM `unliquidation` WHERE `Status` = 0")->queryAll();
        if (!empty($unRs) && is_array($unRs))
        {
            foreach ($unRs as $unVal)
            {
                $tradeTable = 'trade_order_'.$unVal['Instid'];
                $tradeval = $db_local->createCommand("SELECT * FROM `{$tradeTable}` WHERE id = {$unVal['TradeOrderId']}")->queryOne();
                if (empty($tradeval)){
                    parent::commandLog("{$tradeTable}中找不到该记录id={$unVal['TradeOrderId']}",1,$logFile);
                    continue;
                }
                if (in_array($tradeval['TradeStatus'], [1,2,3,5]))
                {
                    $fundinfo = CommFun::GetFundInfo($tradeval['FundCode']);
                    if (empty($fundinfo)){
                        parent::commandLog("FundCode={$tradeval['FundCode']}获取不到fundinfo数据",1,$logFile);
                    }
                    //需要结算的数据
                    $fundcode = $tradeval['FundCode'];
                    $subscrib=$purchas=$redem=$transferin=$transferout=$saleservice=0;
                    if ($tradeval['TradeType']==0)//认申购
                    {
                        if ($tradeval['CallingCode']=='120')
                        {
                            $subscrib = $tradeval['Poundage'];
                        }
                        if ($tradeval['CallingCode']=='122')
                        {
                            $purchas = $tradeval['Poundage'];
                        }
                    }elseif ($tradeval['TradeType']==3)//定投(申购)
                    {
                        $subscrib = $tradeval['Poundage'];
                    }elseif ($tradeval['TradeType']==1)//赎回
                    {
                        $redem = $tradeval['AgentSum'];
                    }
                    if($subscrib==0 && $purchas==0 && $redem==0)
                    {
                        $upSql = "UPDATE unliquidation SET `Status` = 1 WHERE id = {$unVal['id']}";
                        $db_local->createCommand($upSql)->execute();
                        parent::commandLog($upSql,0,$logFile);
                        continue;//都是0的跳过进入下只基金
                    }
                    $inSql = "INSERT INTO liquidation_{$unVal['Instid']} (`FundCode`,`TradeDay`,`SubscribPoundage`,`PurchasPoundage`,`RedemPoundage`,".
                        "`TransferInPoundage`,`TransferOutPoundage`) VALUES ('{$tradeval['FundCode']}','{$tradeval['TradeDay']}',".
                        "'{$subscrib}','{$purchas}','{$redem}','{$transferin}','{$transferout}') ".
                        "ON DUPLICATE KEY UPDATE SubscribPoundage='{$subscrib}',PurchasPoundage='{$purchas}',RedemPoundage='{$redem}',".
                        "TransferInPoundage='{$transferin}',TransferOutPoundage='{$transferout}'";
                    $upSql = "UPDATE unliquidation SET `Status` = 1 WHERE id = {$unVal['id']}";
                    $transaction = $db_local->beginTransaction();
                    try {
                        $db_local->createCommand($inSql)->execute();
                        $db_local->createCommand($upSql)->execute();
                        $transaction->commit();
                        parent::commandLog("insql=>{$inSql};upsql=>{$upSql}",0,$logFile);
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }
                }elseif ($tradeval['TradeStatus'] ==9)
                {
                    //确认数据未回，跳过
                    continue;
                }else {
                   //确认失败的数据
                    $upSql = "UPDATE unliquidation SET `Status` = 1 WHERE id = {$unVal['id']}";
                    $db_local->createCommand($upSql)->execute();
                    parent::commandLog("产生确认失败的数据,upsql={$upSql}",0,$logFile);
                }
            }
        }
    }
    /**
     * 历史数据处理trade_order增加AgentSum/CallingCode
     */
    public function actionHandleTrade()
    {
        $t0 = time();
        $i=0;
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Liquidation_HandleTrade_'.date('Ymd').'.log';
        //循环查找所有商户交易表
        $partners = $db_local->createCommand("SELECT * FROM `partner` where `Status` = 0")->queryAll();
        foreach ($partners as $p)
        {
            $tradeTable = 'trade_order_'.$p['Instid'];//交易表
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '%{$tradeTable}%'")->queryOne();
            //未建表跳过数据处理
            if (empty($existTable))
            {
                continue;
            }
            $tradeRs = $db_local->createCommand("SELECT * FROM `{$tradeTable}` WHERE TradeStatus in(1,2,3,5)  AND TradeType !=2")->queryAll();
            if (empty($tradeRs)){
                continue;
            }
            foreach ($tradeRs as $tradeval)
            {
                if (empty($tradeval['ApplySerial']))
                {
                    parent::commandLog("ApplySerial为空,id={$tradeval['id']}",1,$logFile);
                    continue;
                }
                $hs_obj = new HundSun($tradeval['Uid']);
                $resS004 = $hs_obj->apiRequest('S004',['requestno'=>$tradeval['ApplySerial'],'applyrecordno'=>'1']);
                if ($resS004['code'] == HundSun::SUCC_CODE && !empty($resS004['returnlist'][0]) && is_array($resS004['returnlist'][0]))
                {
                    $agentsum = isset($resS004['returnlist'][0]['agentsum']) && !empty($resS004['returnlist'][0]['agentsum'])?$resS004['returnlist'][0]['agentsum']:0;//代理费
                    $callingcode = isset($resS004['returnlist'][0]['callingcode'])?$resS004['returnlist'][0]['callingcode']:0;//业务代码
                    $upSql = "UPDATE {$tradeTable} SET AgentSum = '{$agentsum}',CallingCode = '{$callingcode}' WHERE id = {$tradeval['id']}";
                    try {
                        $db_local->createCommand($upSql)->execute();
                        parent::commandLog($upSql,0,$logFile);
                    } catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }
                    $i++;
                }else{
                    parent::commandLog('S004返回错误:'.var_export($resS004,true),1,$logFile);
                    continue;
                }
            }
        }
        parent::commandLog("共处理{$i}条数据,响应时间:".round(time()-$t0,3),0,$logFile);
    }
    
}