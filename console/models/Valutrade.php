<?php
namespace console\models;

use Yii;
use yii\base\Model;
use common\lib\HundSun;
use common\lib\CommFun;
use common\models\HandleErr;
use Exception;

/**
 * 定投数据查询同步
 *
 * @author Jason<anwen2218@gmail.com>
 */
class Valutrade extends Model 
{
    public $db;
    public $cid;                    //channel id 商户id
    public $valutrade_plan_x;       //定投计划协议表，x-商户id，分表标识
    public $trade_order_x;          //用户交易信息表，x-商户id，分表标识
    public $fund_position_x;        //用户持仓表，x-商户id，分表标识
    public $funId = 'S003';         //'S002'当天，'S003'-历史
    public $log_query;              //Hundsun系统查询日志
    public $log_dodata;             //数据处理日志
    public $log_cron;               //crontab 处理日志

    public function __construct($cid = 1) 
    {
        $this->cid   = $cid;
        $this->db    = Yii::$app->db;
        $this->fund_position_x  = 'fund_position_'.$cid;
        $this->valutrade_plan_x = 'valutrade_plan_'.$cid;
        $this->trade_order_x    = 'trade_order_'.$cid;
        //log
        $this->log_query  = 'valuTrade_query_'.$this->cid.'_'.date('Ymd').'.log';
        $this->log_dodata = 'valuTrade_doData_'.$this->cid.'_'.date('Ymd').'.log';
        $this->log_cron   = 'Valutrade_'.date('Ymd').'.log';
    }

    /*
     * 事务 操作trade_order_x
     * */
    public function iuOrderPosition()
    {
        $plans = $this->getValutradePlans();
        $n = 0;
        if($plans)
        {
            $tradeDay = CommFun::getApplyTradeDay();
            foreach($plans as $plan)
            {
                $errLog = '';
                $tmpId  = $plan['id'];
                $tmpUid = $plan['Uid'];
                $tmpXyh = $plan['Xyh'];
                $tmpFundCode  = $plan['FundCode'];
                $tmpTradeacco = $plan['Tradeacco'];
                //时间是当天
                $iArr['startdate'] = $iArr['enddate'] = date('Ymd'); 
                if($tmpXyh){
                    $iArr['xyh'] = $tmpXyh;
                }else{
                    //记录处理
                    $errLog .= "{$this->valutrade_plan_x}_id:{$tmpId} Xyh 不存在";
                    $this->logValutrade($errLog, 1, $this->log_cron);
                    continue;
                }
                $iArr['fundcode'] = $tmpFundCode;
                $iArr['tradeacco'] = $tmpTradeacco;
                $arr = $this->queryValutradeOrder($tmpUid, $iArr);
                if(!empty($arr)  && is_array($arr))
                {
                    $tat = "{$arr['applydate']} {$arr['applytime']}";
                    $tmpApplyTime = date("Y-m-d H:i:s",strtotime($tat));
                    $toExistSql   = "select * from {$this->trade_order_x} where Xyh={$tmpXyh} and ApplyTime = '{$tmpApplyTime}' for update"; //----needs to add index-/
                    $rsExist = $this->db->createCommand($toExistSql)->queryOne();
                    if($rsExist){// trade_order_x..exist
                        continue;
                    }else{
                        try{
                            $orderNo = CommFun::getOrderNo($tmpUid);
                            //insert trade_order_x
                            $cols  = 'Uid,FundCode,OrderNo,ApplyShare,ApplyAmount,TradeAcco,TradeType,TradeStatus,OtherInfo,ApplySerial,Xyh,ApplyTime,DeductMoney,TradeDay';
                            $vals = "{$tmpUid},'{$tmpFundCode}','{$orderNo}',{$arr['applyshare']},{$arr['applysum']},'{$tmpTradeacco}',3,{$arr['confirmflag']},'{$arr['businflagStr']}','{$arr['applyserial']}','{$tmpXyh}','{$tmpApplyTime}','{$arr['kkstat']}','{$tradeDay}' ";
                            $inSql = "insert into {$this->trade_order_x} ({$cols}) values ({$vals})";
                            $rsInTrade = $this->db->createCommand($inSql)->execute();
                            $this->logValutrade("定投产生sql:{$inSql}",0, $this->log_cron);
                        }catch (Exception $e){
                            $msg  = CommFun::decodeUnicode(json_encode($e->getMessage()));
                            //write log 
                            $errLog .= "处理数据到{$this->trade_order_x};{$this->fund_position_x} 失败;{$this->valutrade_plan_x};id:{$tmpId};__error:{$msg}"  ;
                            $this->logValutrade($errLog, 1, $this->log_cron);
                            $errArr = ['OidType'=>3,'Oid'=>$tmpId,'Instid'=>$this->cid,'Info'=>$errLog,'SystemTime'=>date('Y-m-d H:i:s')];
                            HandleErr::insert($errArr);
                            continue;
                        }
                    }
                }else {
                    //记录处理
                    $errInfo = "S003查询定投返回失败，参数:".json_encode($iArr);
                    $errArr = ['OidType'=>3,'Oid'=>$tmpId,'Instid'=>$this->cid,'Info'=>$errInfo,'SystemTime'=>date('Y-m-d H:i:s')];
                    HandleErr::insert($errArr);
                    continue;
                }
                $n++;
            }
        }
        return $n;
    }
    /*
     * 取定投交易信息
     * @param uid 用户id
     * @param iArr['tradeacco'] 交易账号
     * @param iArr['fundcode'] 基金代码
     * @param iArr['xyh']    协议号
     * @param iArr['startdate']  起始日期
     * @param iArr['enddate']    结束日期
     * @return []
     * */
    public function queryValutradeOrder($uid=0, $iArr=[]) 
    {
        $iArr['applyrecordno']        = 1; //申请条数
        $iArr['querydeclarevaluavgr'] = 1; //是否是查询申购定投申请

        $hs = new HundSun($uid);
        $hs->loginHs(); //登陆hs
        $rs = $hs->apiRequest($this->funId,$iArr); //S003
        if($hs::SUCC_CODE == $rs['code'] && !empty($rs['returnlist']))
        {        
            return $rs['returnlist'][0];
        }else{
            $logMsg = "未从S003查到此定投申请记录:request params:".var_export($iArr,true).";;return message:".var_export($rs,true);
            $this->logValutrade($logMsg, 1, $this->log_cron);           
            return false;
        }
    }
    /*
     * 当日需要查询的定投计划 
     *
     * */
    public function getValutradePlans() 
    {
        $rList = []; 
        $where = " where State ='A' "; //终止暂停不统计
        //首次交易年月 (格式:201604)
        $ym    = date('Ym');
        $where .= " and `Scjyrq` <= '{$ym}' ";     //-------/
        //终止日期 (格式:20180313)
        $eymd  = date('Ymd');
        $where .= " and `Zzrq` >= '{$eymd}' ";     //-------/
        $sql  = "select * from {$this->valutrade_plan_x} {$where} ;";
        $list = $this->db->createCommand($sql)->queryAll();
        if( is_array($list) && !empty($list) )
        {
            //$wd = date('w') + 1;//交易周期为周时，交易日期传值规则为2-6（周一-周五)
            $md = date('d'); 
            foreach($list as $val)
            {
                $tmpCycleunit = $val['Cycleunit'];    //'交易周期单位 "0":月,"1":周,"2":日',
                $tmpJyrq      = $val['Jyrq'];    //交易日期（日期后两位）
                //if( ('0' == $tmpCycleunit && $tmpJyrq == $md) ||( '1' == $tmpCycleunit && $tmpJyrq == $wd ))
                if( ('0' == $tmpCycleunit && $tmpJyrq == $md) ) //周期月
                {
                    $rList[] = $val;
                }
            }
        }
        return $rList;
    }
    /*
     * write valueTrade log 
     * @msg string
     * @level 0-info，1-error
     * @fName file name
     *
     * */
    public function logValutrade($msg='', $level=0, $fName = '') 
    {   
        $fdir='/data/log/command/';
        $td = date('Y-m-d');
        if(1 == $level)
            $lv = '/error/';
        else if(0 == $level)
            $lv = '/info/';
        if(!$fName)
            $fName = 'valuTrade_'.date('Y-m-d').'.log';
        $fdir  .= $lv;    
        if(!file_exists($fdir))  
            mkdir($fdir,0777,true);   
        $fName = $fdir.$fName;
        $msg .= "\n";
        file_put_contents($fName,$msg,FILE_APPEND);
    }
    /**
     * command掉单补充操作valutrade
     * @param int $oid valutrade_plan_x表id
     * @param int $instid 商户号
     * @param resource $db_local 数据库连接组件
     * @return bool 操作是否成功
     */
    public function makeUpValu($oid,$instid,$db_local)
    {
        $vpRs = $db_local->createCommand("SELECT * FROM `valutrade_plan_{$instid}` WHERE id = '{$oid}'")->queryOne();
        $errLog = '';
        $tmpId  = $vpRs['id'];
        $tmpUid = $vpRs['Uid'];
        $tmpXyh = $vpRs['Xyh'];
        $tmpFundCode  = $vpRs['FundCode'];
        $tmpTradeacco = $vpRs['Tradeacco'];
        $iArr['startdate'] = $iArr['enddate'] = date('Ymd');
        $iArr['xyh'] = $tmpXyh;
        if ($tmpFundCode) {
            $iArr['fundcode'] = $tmpFundCode;
        }
        if ($tmpTradeacco) {
            $iArr['tradeacco'] = $tmpTradeacco;
        }
        $arr = $this->queryValutradeOrder($tmpUid, $iArr);
        $hs = new HundSun($tmpUid);
        $hs->loginHs(); //登陆hs
        $rs = $hs->apiRequest('S003',$iArr); //S003
        if($hs::SUCC_CODE == $rs['code'] && !empty($rs['returnlist']))
        {
            $arr = $rs['returnlist'][0];
        }else{
            $this->logValutrade('S003查询失败,参数:'.json_encode($iArr), 1,$this->log_cron);
            return false;
        }
            $tat = "{$arr['applydate']} {$arr['applytime']}";
            $tmpApplyTime = date("Y-m-d H:i:s",strtotime($tat));
            $toExistSql   = "select * from {$this->trade_order_x} where Xyh={$tmpXyh} and ApplyTime = '{$tmpApplyTime}' for update"; //----needs to add index-/
            $rsExist = $this->db->createCommand($toExistSql)->queryOne();
            if($rsExist){// trade_order_x..exist
                continue;
            }else{
                try{
                    $orderNo = CommFun::getOrderNo($tmpUid);
                    //insert trade_order_x
                    $cols  = 'Uid,FundCode,OrderNo,ApplyShare,ApplyAmount,TradeAcco,TradeType,TradeStatus,OtherInfo,ApplySerial,Xyh,ApplyTime';
                    $vals = "{$tmpUid},'{$tmpFundCode}','{$orderNo}',{$arr['applyshare']},{$arr['applysum']},'{$tmpTradeacco}',3,{$arr['confirmflag']},'{$arr['businflagStr']}','{$arr['applyserial']}','{$tmpXyh}','{$tmpApplyTime}'";
                    $inSql = "insert into {$this->trade_order_x} ({$cols}) values ({$vals})";
                    $this->db->createCommand($inSql)->execute();
                    return true;
                }catch (Exception $e){
                    $msg  = CommFun::decodeUnicode(json_encode($e->getMessage()));
                    //write log
                    $errLog .= "处理数据到{$this->trade_order_x};{$this->fund_position_x} 失败;{$this->valutrade_plan_x};id:{$tmpId};__error:{$msg}";
                    $this->logValutrade($errLog, 1,$this->log_cron);
                    return false;
                }
            }
    }
}
