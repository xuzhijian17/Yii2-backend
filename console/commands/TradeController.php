<?php
namespace console\commands;

use Yii;
use common\lib\HundSun;
use common\models\HandleErr;
use Exception;
use common\lib\CommFun;
class TradeController extends Controller
{
    /**
     * 日终确认数据返回后更新交易表/持仓表(只新建不更改持仓数量，防止造成持仓数量重复累加)
     * 00 16 * * 1-5 周一到周五 16:00确认数据返回
     */
    public function actionConfirmback()
    {
        if (in_array(date('Y-m-d'),Yii::$app->params['holidays']))
        {
            //节假日退出
            return false;
        }
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Confirmback_'.date('Ymd').'.log';
        $tradeDay = CommFun::getLastTradeDay();
        //循环查找所有商户交易表
        $partners = $db_local->createCommand("SELECT * FROM `partner` where `Status` = 0")->queryAll();
        if (empty($partners))
        {
            parent::commandLog("商户号为空",1,$logFile);
            exit();
        }
        $endday = date('Ymd');//调增调减结束日期
        $redis = Yii::$app->redis;
        foreach ($partners as $p)
        {
            $n = 0;//计数
            $t1 = time();
            $tradeOrderTable = 'trade_order_'.$p['Instid'];//交易表
            $positionTable = 'fund_position_'.$p['Instid'];//持仓表
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '%{$tradeOrderTable}%'")->queryOne();
            //未建表跳过数据处理
            if (empty($existTable))
            {
                continue;
            }
            $tradeRs = $db_local->createCommand("SELECT * FROM `{$tradeOrderTable}` WHERE TradeDay <='{$tradeDay}' AND HandleStatus = 0 AND TradeStatus='9' AND TradeType !=2")->queryAll();
            if (!empty($tradeRs))
            {
                $datetime = date('Y-m-d H:i:s');
                foreach ($tradeRs as $tradevalue)
                {
                    if (empty($tradevalue['ApplySerial']) || empty($tradevalue['Uid']))
                    {
                        //进入处理记录
                        $errInfo = "{$tradeOrderTable}表ApplySerial或Uid字段数据为空id:{$tradevalue['id']}";
                        parent::commandLog($errInfo,1,$logFile);
                        $errArr = ['OidType'=>0,'Oid'=>$tradevalue['id'],'Instid'=>$p['Instid'],'Info'=>$errInfo,'SystemTime'=>$datetime];
                        HandleErr::insert($errArr);
                        continue;
                    }
                    //撤单的跳过
                    if ($tradevalue['TradeStatus'] == '4')
                    {
                        continue;
                    }
                    //购买基金，扣款失败的跳过/置为确认失败
                    if($tradevalue['TradeType'] == 0 && $tradevalue['DeductMoney'] ==1)
                    {
                        $db_local->createCommand("UPDATE {$tradeOrderTable} SET TradeStatus = 0,ConfirmTime = '{$datetime}',HandleTime='{$datetime}' WHERE id = '{$tradevalue['id']}'")->execute();
                        continue;
                    }
                    //查询S004 返回结果
                    if ($p['Instid']==1000){
                        $pubParams = ['usertype'=>'o'];
                    }else {
                        $pubParams = null;
                    }
                    $hundsun = new HundSun($tradevalue['Uid'],$pubParams,1);
                    $resS004 = $hundsun->apiRequest('S004',['requestno'=>$tradevalue['ApplySerial'],'applyrecordno'=>'1']);
                    if ($resS004['code'] == HundSun::SUCC_CODE && !empty($resS004['returnlist'][0]) && is_array($resS004['returnlist'][0]))
                    {
                        $poundage = empty($resS004['returnlist'][0]['poundage'])?'0.00':$resS004['returnlist'][0]['poundage'];//手续费
                        $confirmShare = empty($resS004['returnlist'][0]['tradeconfirmshare'])?'0.00':$resS004['returnlist'][0]['tradeconfirmshare'];//交易确认份额
                        $confirmAmount = empty($resS004['returnlist'][0]['tradeconfirmsum'])?'0.00':$resS004['returnlist'][0]['tradeconfirmsum'];//交易确认金额
                        $confirmNetValue = empty($resS004['returnlist'][0]['netvalue'])?'0.00':$resS004['returnlist'][0]['netvalue'];//净值
                        $tradeStatus = $resS004['returnlist'][0]['confirmflag'];//交易确认标识
                        $agentSum = empty($resS004['returnlist'][0]['agentsum'])?0:$resS004['returnlist'][0]['agentsum'];
                        $callingCode = empty($resS004['returnlist'][0]['callingcode'])?'':$resS004['returnlist'][0]['callingcode'];
                        $handleStatus = (isset($resS004['returnlist'][0]['confirmflag']) && $resS004['returnlist'][0]['confirmflag']==9)?0:1;
                        $confirmTime = $resS004['returnlist'][0]['confirmdate'];
                        $tradeSql = "UPDATE {$tradeOrderTable} SET `ConfirmShare`='{$confirmShare}',`ConfirmAmount`='{$confirmAmount}',`ConfirmNetValue`='{$confirmNetValue}',".
                        "`AgentSum`='{$agentSum}',`CallingCode`='{$callingCode}',`Poundage`='{$poundage}',`TradeStatus`='{$tradeStatus}',`HandleStatus`='{$handleStatus}',".
                        "`ConfirmTime`='{$confirmTime}',`HandleTime`='{$datetime}' WHERE id = '{$tradevalue['id']}'";
                        //判断申购赎回更改持仓
                        $fundPositionSql = "SELECT * FROM {$positionTable} WHERE Uid = {$tradevalue['Uid']} AND FundCode = '{$tradevalue['FundCode']}'";
                        $rsPosition = $db_local->createCommand($fundPositionSql)->queryOne();
                        //确认成功更改持仓
                        if (in_array($tradeStatus, ['1','2','3']))
                        {
                            //买入情况
                            if ($tradevalue['TradeType'] == 0 || $tradevalue['TradeType'] == 3)
                            {
                                //持仓为空，建仓
                                if (empty($rsPosition))
                                {
                                    //插入持仓数据...
                                    $positionSql = "INSERT INTO `{$positionTable}` (`Uid`,`FundCode`,`CurrentRemainShare`,`InitTime`) VALUES ('{$tradevalue['Uid']}','{$tradevalue['FundCode']}','{$confirmShare}','{$datetime}')";
                                }else {
                                    //更改持仓数量
                                    $positionSql = "UPDATE {$positionTable} SET `CurrentRemainShare` = `CurrentRemainShare`+{$confirmShare} WHERE id='{$rsPosition['id']}' ";
                                }
                                //卖出情况
                            }elseif ($tradevalue['TradeType'] ==1){
                                if (empty($rsPosition))
                                {
                                    //持仓为空，错误
                                    $logInfo = "严重错误:卖出确认数据回来时无持仓,position-sql:{$fundPositionSql}";
                                    parent::commandLog($logInfo,1,$logFile);
                                }else {
                                    $currentRemainShare = ($rsPosition['CurrentRemainShare'] - $confirmShare)<0?0:$rsPosition['CurrentRemainShare'] - $confirmShare;
                                    if ($rsPosition['CurrentRemainShare'] - $confirmShare < 0){
                                        //持仓不足，错误
                                        $logInfo = "严重错误:卖出确认数据回来时持有份额小于可卖份额,确认份额:{$confirmShare},当前份额:{$rsPosition['CurrentRemainShare']},position-sql:{$fundPositionSql}";
                                        parent::commandLog($logInfo,1,$logFile);
                                    }else {
                                        $positionSql = "UPDATE {$positionTable} SET `CurrentRemainShare` = {$currentRemainShare} WHERE id='{$rsPosition['id']}' ";
                                    }
                                }
                            }
                            //$portfolio_sql = $this->portfolio_position_process($p['Instid'], $tradevalue, $resS004['returnlist'][0]);
                        }else {
                            //失败记录日志，不更改
                            $logInfo = "确认数据未成功:sql:{$tradeSql}，接口返回:".var_export($resS004['returnlist'][0],true);
                            parent::commandLog($logInfo,0,$logFile);
                        }
                        $transaction = $db_local->beginTransaction();
                        try {
                            $db_local->createCommand($tradeSql)->execute();
                            parent::commandLog($tradeSql,0,$logFile);
                            if (!empty($positionSql))
                            {
                                $db_local->createCommand($positionSql)->execute();
                                parent::commandLog($positionSql,0,$logFile);
                            }
                            /**if (!empty($portfolio_sql))
                            {
                                $db_local->createCommand($portfolio_sql)->execute();
                                parent::commandLog($portfolio_sql, 0 ,$logFile);
                            }*/
                            $transaction->commit();
                        } catch (Exception $e) {
                            //进入处理记录
                            $transaction->rollBack();
                            parent::commandLog($e->getMessage(),1,$logFile);
                            $errArr = ['OidType'=>0,'Oid'=>$tradevalue['id'],'Instid'=>$p['Instid'],'Info'=>$e->getMessage(),'SystemTime'=>$datetime];
                            HandleErr::insert($errArr);
                        }
                    }else 
                    {
                        if ($resS004['code'] == HundSun::SUCC_CODE)
                        {
                            $resS003 = $hundsun->apiRequest('S003',['applyserial'=>$tradevalue['ApplySerial'],'applyrecordno'=>'1']);
                            if ($resS003['code'] == HundSun::SUCC_CODE )
                            {
                                $handleStatus = (isset($resS003['returnlist'][0]['confirmflag']) && $resS003['returnlist'][0]['confirmflag'] == '9')?0:1;
                                $tradeStatus = !isset($resS003['returnlist'][0]['confirmflag'])?'0':$resS003['returnlist'][0]['confirmflag'];
                                $otherInfo = !isset($resS003['returnlist'][0]['cperrormsg'])?'':$resS003['returnlist'][0]['cperrormsg'];
                                $deductMoney = !isset($resS003['returnlist'][0]['kkstat'])?'1':$resS003['returnlist'][0]['kkstat'];
                                $upTradeSql = "UPDATE `{$tradeOrderTable}` SET TradeStatus = '{$tradeStatus}',`HandleStatus`='{$handleStatus}',
                                OtherInfo ='{$otherInfo}',DeductMoney='{$deductMoney}',HandleTime ='{$datetime}' WHERE id  = '{$tradevalue['id']}'";
                                try {
                                    $db_local->createCommand($upTradeSql)->execute();
                                    parent::commandLog($upTradeSql,0,$logFile);
                                } catch (Exception $e) {
                                    parent::commandLog($e->getMessage(),1,$logFile);
                                }
                            }else {
                                //查询不成功，进入处理记录
                                $errInfo = "查询S003不成功table:{$tradeOrderTable};id:{$tradevalue['id']};code={$resS003['code']};message={$resS003['message']}";
                                parent::commandLog($errInfo,1,$logFile);
                                $errArr = ['OidType'=>0,'Oid'=>$tradevalue['id'],'Instid'=>$p['Instid'],'Info'=>$errInfo,'SystemTime'=>$datetime];
                                HandleErr::insert($errArr);
                            }
                        }else {
                            //查询不成功，进入处理记录
                            $errInfo = "查询S004不成功table:{$tradeOrderTable};id:{$tradevalue['id']};code={$resS004['code']};message={$resS004['message']}";
                            parent::commandLog($errInfo.var_export($resS004,true),1,$logFile);
                            $errArr = ['OidType'=>0,'Oid'=>$tradevalue['id'],'Instid'=>$p['Instid'],'Info'=>$errInfo,'SystemTime'=>$datetime];
                            HandleErr::insert($errArr);
                        }
                    }
                    //TA调增调减Start
                    if ($redis->hget('b_type_fund',$tradevalue['FundCode']))
                    {
                        $this->actionTiaozeng($p['Instid'], $tradevalue['Uid'], date('Ymd',strtotime($tradevalue['ApplyTime'])), $endday);
                    }
                    //TA调增调减End
                    $n++;
                }
            }else {
                //继续下一个表 
                continue;
            }
            parent::commandLog("商户号:{$p['Instid']},更新{$n}条数据 耗时".(time()-$t1).'秒',0,$logFile);
        }
        //同步持仓脚本
        $this->run('queryinfov2/index');
        sleep(60);
        //每一条交易产生的成本 交易日统计
        $this->run('cost/index');
        sleep(60);
        //交易费用(认/申购费、赎回费) 交易日统计
        $this->run('liquidation/trade-poundage');
    }

    /**
     * 查询扣款状态每分钟检查（改进后每10s查询一次）
     */
    public function actionCheckdeduct()
    {
        $db_local = Yii::$app->db_local;
        $taskArr = $db_local->createCommand("SELECT * FROM `task_deduct` WHERE DeductMoney = '0' or DeductMoney='3' ")->queryAll();
        if (!empty($taskArr) && is_array($taskArr))
        {
            foreach ($taskArr as $taskVal)
            {
                if ($taskVal['TaskTime'] < date('Y-m-d 00:00:00'))
                {
                    continue;
                }
                $pubParams = null;
                if ($taskVal['Instid'] == 1000) { //如果等于企业用户
                    $pubParams['usertype'] = 'o';
                }
                $hundsun = new HundSun($taskVal['Uid'], $pubParams,1);
                $resS003 = $hundsun->apiRequest('S003',['applyserial'=>$taskVal['ApplySerial'],'applyrecordno'=>'1']);
                if ($resS003['code'] == HundSun::SUCC_CODE && !empty($resS003['returnlist'][0]['kkstat']))
                {
                    $deductMoney = $resS003['returnlist'][0]['kkstat'];
                    try {
                        if ($deductMoney =='1')
                        {
                            $errmsg = empty($resS003['returnlist'][0]['cperrormsg'])?'扣款失败':$resS003['returnlist'][0]['cperrormsg'];
                            $addsql = " ,OtherInfo='{$errmsg}',TradeStatus='0',HandleTime='".date('Y-m-d H:i:s')."' ";
                        }else {
                            $addsql = '';
                        }
                        //更改原申请单
                        $db_local->createCommand("UPDATE `trade_order_{$taskVal['Instid']}` SET DeductMoney = '{$deductMoney}' {$addsql} WHERE id = '{$taskVal['Oid']}'")->execute();
                        //更改本表记录
                        $db_local->createCommand("UPDATE task_deduct SET DeductMoney = '{$deductMoney}',HandleTime = '".date('Y-m-d H:i:s')."' WHERE id = '{$taskVal['id']}'")->execute();
                    } catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,'Checkdeduct_'.date('Ymd').'.log');
                    }
                }else {
                    //查询不成功，推迟下次执行(旧版)
//                     $db_local->createCommand("UPDATE task_deduct SET TaskTime = '".date('Y-m-d H:i:00',time()+120)."' WHERE id = '{$taskVal['id']}'")->execute();
                    //新版直接跳过下个10s继续执行
                    continue;
                }
            }
        }else {
            return false;
        }
    }

    //组合持仓处理，为了不和之前的代码混淆影响之前代码，故避开一个方法
    public function portfolio_position_process($Instid, $trade, $hs_trade)
    {
        if (empty($trade) || $trade['PortfTradeId'] <=0) {
            return "";
        }
        $db = Yii::$app->db_local;
        $portfolio_trade = $db->createCommand("SELECT * FROM `portfolio_trade_{$Instid}` WHERE DeductMoney = '0' ")->queryOne();
        if (empty($portfolio_trade)) {
            return '';
        }
        $confirmShare = empty($hs_trade['tradeconfirmshare'])?'0.00':$hs_trade['tradeconfirmshare'];    //交易确认份额
        $confirmAmount = empty($hs_trade['tradeconfirmsum'])?'0.00':$hs_trade['tradeconfirmsum'];       //交易确认金额
        $portfolio_table = "portfolio_position_{$Instid}";
        $portfolio_position = $db->createCommand("SELECT * FROM {$portfolio_table} WHERE DeductMoney = '0' ")->queryOne();
        $lasttime = date("Y-m-d H:i:s");
        if (empty($portfolio_position)) {
            return "";
        }
        $pp_id = $portfolio_position['id'];
        if ($trade['TradeType'] == 0) { //买入
            $sql = "update {$portfolio_table} set CurrentRemainShare=CurrentRemainShare+{$confirmShare},";
            $sql .= "FreezeBuyAmount='{$trade['ApplyAmount']}',Lastuptime='{$lasttime}' where id='{$pp_id}'";
            return $sql;
        } else if ($trade['TradeType'] == 1) { //卖出
            $sql = "update {$portfolio_table} set CurrentRemainShare=CurrentRemainShare-{$confirmShare},";
            $sql .= "FreezeSellShare='{$trade['ApplyShare']}',Lastuptime='{$lasttime}' where id='{$pp_id}'";
            return $sql;
        }
        return "";
    }

    //处理用户申购B类基金，强制调增或调减成A类基金的业务处理
    public function actionTiaozeng($instId, $uid, $starttime, $endtime)
    {
        $trade_order_table = "trade_order_".$instId;
        $fund_position_table = "fund_position_".$instId;
        $db = Yii::$app->db_local;
        $date = date("Y-m-d H:i:s");
        $pubParams = null;
        if ($instId == 1000) { //如果等于企业用户
            $pubParams['usertype'] = 'o';
        }
        $hundsun = new HundSun($uid, $pubParams,1);
        $resS004 = $hundsun->apiRequest('S004', ['startdate'=>$starttime, 'enddate'=>$endtime, 'pageno'=>1, 'applyrecordno'=>100]);
        $logFile = 'Confirmback_'.date('Ymd').'.log';
        if ($resS004['code'] == HundSun::SUCC_CODE && !empty($resS004['returnlist'][0])){
            foreach ($resS004['returnlist'] as $key=>$info) {
                if ($info['callingcode'] == 144 || $info['callingcode'] == 145){
                    $sql = "SELECT * FROM {$trade_order_table} WHERE ApplySerial='{$info['applyserial']}'";
                    $is_exists = $db->createCommand($sql)->queryOne();
                    if (!empty($is_exists)) {
                        continue;
                    }
                }
                $sql = "SELECT * FROM {$fund_position_table} WHERE Uid='{$uid}' AND FundCode='{$info['fundcode']}'";
                $fund_position = $db->createCommand($sql)->queryOne();

                if ($info['callingcode'] == 144 && $info['returncode'] == 0) { //强制调增

                    if (!empty($fund_position)) { //如果存在持仓则加上相应调增的份额，否则新建持仓
                        $sql = "update {$fund_position_table} set CurrentRemainShare=CurrentRemainShare+{$info['tradeconfirmshare']},";
                        $sql .= "Lastuptime='{$date}' where id='{$fund_position['id']}'";
                    } else {
                        $sql = "insert into {$fund_position_table} set Uid='{$uid}',FundCode='{$info['fundcode']}',";
                        $sql .= "CurrentRemainShare='{$info['tradeconfirmshare']}',InitTime='{$date}',Lastuptime='{$date}'";
                    }
                    $orderno = CommFun::getOrderNo($date);
                    $add_trade_sql = "insert into {$trade_order_table} set Orderno='{$orderno}',Uid='{$uid}',FundCode='{$info['fundcode']}',";
                    $add_trade_sql .= "ApplyShare='{$info['requestshares']}',ApplyAmount='{$info['requestbalance']}',";
                    $ConfirmAmount = $info['tradeconfirmshare']*$info['netvalue'];
                    $add_trade_sql .= "ConfirmAmount='{$ConfirmAmount}',ConfirmShare='{$info['tradeconfirmshare']}',CallingCode='{$info['callingcode']}',";
                    $add_trade_sql .= "ConfirmNetValue='{$info['netvalue']}',Poundage='{$info['poundage']}',TradeAcco='{$info['tradeacco']}',TradeType='0',";
                    $add_trade_sql .= "TradeStatus='1',HandleStatus='1',OtherInfo='强制调增,调增份额来源于基金{$info['targetfundcode']}',ApplySerial='{$info['applyserial']}_0',DeductMoney='2',TradeDay='{$date}',";
                    $applydate = date("Y-m-d H:i:s", strtotime($info['applydate']));
                    $confirmdate = date("Y-m-d H:i:s", strtotime($info['confirmdate']));

                    $add_trade_sql .= "ApplyTime='{$applydate}',ConfirmTime='{$confirmdate}',HandleTime='{$date}'";
                    try {
                        $r1 = $db->createCommand($sql)->execute();
                        $r2 = $db->createCommand($add_trade_sql)->execute();
                        $r1 = (int)$r1;
                        $r2 = (int)$r2;
                        parent::commandLog($sql.",执行结果：".$r1, 0, $logFile);
                        parent::commandLog($add_trade_sql.",执行结果：".$r2, 0, $logFile);
                    } catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }
                } else if ($info['callingcode'] == 145 && $info['returncode'] == 0 ) { //强制调减
                    /* 在同步持仓处更新持仓
                    if (!empty($fund_position)) { //如果调减基金有持仓份额则减去相应的份额，否则不做操作
                        $sql = "update {$fund_position_table} set CurrentRemainShare=CurrentRemainShare-{$info['tradeconfirmshare']},";
                        $sql .= "Lastuptime='{$date}' where id='{$fund_position['id']}'";
                        $db->createCommand($sql)->execute();
                    }
                    */
                    $orderno = CommFun::getOrderNo($date);
                    $add_trade_sql = "insert into {$trade_order_table} set Orderno='{$orderno}',Uid='{$uid}',FundCode='{$info['fundcode']}',ApplyShare='{$info['requestshares']}',";
                    $ConfirmAmount = $info['tradeconfirmshare']*$info['netvalue'];
                    $add_trade_sql .= "ApplyAmount='{$info['requestbalance']}',ConfirmAmount='{$ConfirmAmount}',ConfirmShare='{$info['tradeconfirmshare']}',ConfirmNetValue='{$info['netvalue']}',Poundage='{$info['poundage']}',CallingCode='{$info['callingcode']}',TradeAcco='{$info['tradeacco']}',TradeType='1',";
                    $add_trade_sql .= "TradeStatus='1',HandleStatus='1',OtherInfo='强制调减,基金份额转移至基金{$info['targetfundcode']}',ApplySerial='{$info['applyserial']}_1',DeductMoney='2',TradeDay='{$date}',";
                    $applydate = date("Y-m-d H:i:s", strtotime($info['applydate']));
                    $confirmdate = date("Y-m-d H:i:s", strtotime($info['confirmdate']));
                    $add_trade_sql .= "ApplyTime='{$applydate}',ConfirmTime='{$confirmdate}',HandleTime='{$date}'";
                    try{
                        $r = $db->createCommand($add_trade_sql)->execute();
                        $r = (int)$r;
                        parent::commandLog($add_trade_sql.",执行结果：".$r, 0, $logFile);
                    }catch (Exception $e) {
                        parent::commandLog($e->getMessage(),1,$logFile);
                    }

                }
            }
        }
    }
}