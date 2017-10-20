<?php
namespace console\commands;

use Yii;
use Exception;
use common\lib\CommFun;
/**
 *成本计算相关类
 */
class CostController extends Controller
{
    /**
     * 记录每一条交易产生的成本 交易日18:00
     */
    public function actionIndex()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Cost_'.date('Ymd').'.log';
        $tradeDay = CommFun::getLastTradeDay();
        $confirmDay = date('Y-m-d');
        //循环查找所有商户交易表
        $partners = $db_local->createCommand("SELECT * FROM `partner` where `Status` = 0")->queryAll();
        foreach ($partners as $p)
        {
            $tradeTable = 'trade_order_'.$p['Instid'];//交易表
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '%{$tradeTable}%'")->queryOne();
            if (empty($existTable)){
                continue;
            }
            $tradeRs = $db_local->createCommand("SELECT * FROM {$tradeTable} WHERE TradeDay = '{$tradeDay}'")->queryAll();
            if (empty($tradeRs) || !is_array($tradeRs)) {
                continue;//无数据跳出
            }
            foreach ($tradeRs as $tradeVal)
            {
                $paymentExpense = $superviseExpense=$amount = 0;
                $fundInfo = CommFun::GetFundInfo($tradeVal['FundCode']);
                if (empty($fundInfo)){
                    parent::commandLog("FundCode={$tradeVal['FundCode']}获取不到fundinfo数据，未处理{$tradeTable} id={$tradeVal['id']}",1,$logFile);
                    continue;
                }
                if ($tradeVal['TradeStatus'] ==9)
                {
                    $unCost = $db_local->createCommand("SELECT * FROM `uncost` WHERE TradeOrderId = {$tradeVal['id']} AND Instid = {$p['Instid']}")->queryOne();
                    if (empty($unCost))
                    {
                        $unCostSql = "INSERT INTO uncost (`Instid`,`TradeOrderId`) VALUES ('{$p['Instid']}','{$tradeVal['id']}')";
                        try {
                            $db_local->createCommand($unCostSql)->execute();
                            parent::commandLog($unCostSql,0,$logFile);
                        } catch (Exception $e) {
                            parent::commandLog($e->getMessage(),1,$logFile);
                        }
                    }
                    continue;
                }
                //买入(认/申购、定期定额)
                if ($tradeVal['TradeType'] ==0 || $tradeVal['TradeType'] ==3)
                {
                    //扣款成功
                    if ($tradeVal['DeductMoney']==2)
                    {
                        //支付成本
                        if ($fundInfo['MarketFee'] ==0)
                        {
                            //前后端收费基金
                            $paymentExpense = bcmul($tradeVal['ApplyAmount'],0.003,3)<3?3:bcmul($tradeVal['ApplyAmount'],0.003,3);
                        }else {
                            //按保有量收取营销费基金
                            $paymentExpense = bcmul($tradeVal['ApplyAmount'],0.0002,3);
                            if ($paymentExpense <2){
                                $paymentExpense = 2;
                            }elseif ($paymentExpense >20){
                                $paymentExpense = 20;
                            }
                        }
                        //监管成本
                        if ($fundInfo['FundTypeCode']==1109 || $fundInfo['FundTypeCode']==1106)
                        {
                            $superviseExpense = 0;//货基/理财型为零
                        }elseif ($fundInfo['FundTypeCode']==1105){
                            $superviseExpense = bcmul($tradeVal['ApplyAmount'],0.0005,3);//债券型万五
                        }elseif (in_array($fundInfo['FundTypeCode'], [1101,1110,1103]))
                        {
                            //股票、混合、QDII基金
                            if ($p['Instid'] ==1000)
                            {
                                //机构企业做金额判断
                                $isStand = self::getStand($fundInfo['InnerCode'], $tradeVal['CallingCode'], $tradeVal['ApplyAmount']);
                                if ($isStand){
                                    $superviseExpense = 100;
                                }else {
                                    $superviseExpense = bcmul($tradeVal['ApplyAmount'],0.001,3);
                                }
                            }else {
                                //普通商户都是千一
                                $superviseExpense = bcmul($tradeVal['ApplyAmount'],0.001,3);
                            }
                        }
                        //失败 ->划出款
                        if ($tradeVal['TradeStatus']==0)
                        {
                            $isRegulatoryBank = self::RegulatoryBank($db_local, $tradeVal['Uid'], $p['Instid']);
                            //企业端的如果用民生银行无手续费
                            if(!$isRegulatoryBank){
                                if ($tradeVal['ApplyAmount']<50000)
                                {
                                    $superviseExpense +=2 ;
                                }else {
                                    $superviseExpense +=5.5;
                                }
                            }
                        }
                    }
                    $amount = $tradeVal['ApplyAmount'];
                }
                //卖出
                elseif ($tradeVal['TradeType'] ==1)
                {
                    //企业端的如果用民生银行无手续费
                    if (in_array($tradeVal['TradeStatus'], [1,2,3,5]))
                    {
                        $isRegulatoryBank = self::RegulatoryBank($db_local, $tradeVal['Uid'], $p['Instid']);
                        if(!$isRegulatoryBank){
                            if ($tradeVal['ConfirmAmount']<50000)
                            {
                                $superviseExpense +=2 ;
                            }else {
                                $superviseExpense +=5.5;
                            }
                        }
                    }
                    $amount = $tradeVal['ConfirmAmount'];
                }
                //撤单
                elseif ($tradeVal['TradeType']==2)
                {
                    $oriTrade = $db_local->createCommand("SELECT * FROM {$tradeTable} WHERE ApplySerial = '{$tradeVal['OriginalApplyserial']}'")->queryOne();
                    if (empty($oriTrade)){
                        parent::commandLog("查询不到原订单，sql=SELECT * FROM {$tradeTable} WHERE ApplySerial = '{$tradeVal['OriginalApplyserial']}'",1,$logFile);
                        continue;
                    }
                    if ($oriTrade['TradeType']==0 || $oriTrade['TradeType']==3)
                    {
                        $isRegulatoryBank = self::RegulatoryBank($db_local, $tradeVal['Uid'], $p['Instid']);
                        if (!$isRegulatoryBank)
                        {
                            if ($oriTrade['ApplyAmount']<50000)
                            {
                                $superviseExpense +=2 ;
                            }else {
                                $superviseExpense +=5.5;
                            }
                            $amount = $oriTrade['ApplyAmount'];
                        }
                    }
                }
                if ($paymentExpense ==0 && $superviseExpense==0 ){
                    continue;//成本都为0时不记录
                }
                $rs = $db_local->createCommand("SELECT u.`Name` FROM `user` u WHERE u.id = {$tradeVal['Uid']}")->queryOne();
                $name = empty($rs['Name'])?'未知':$rs['Name'];
                $userType = $p['Instid']==1000?'o':'p';
                $tradeAcco = $tradeVal['TradeAcco'];
                $tradeType = $tradeVal['TradeType'];
                $tradeStatus = $tradeVal['TradeStatus'];
                $fundType = $fundInfo['FundTypeCode'];
                $inSql = "INSERT INTO cost_{$p['Instid']} (`TradeId`,`Uid`,`Name`,`UserType`,`TradeAcco`,`Amount`,`TradeType`,`TradeStatus`,`FundType`,`PaymentExpense`,`SuperviseExpense`,`TradeDay`)".
                " VALUES ('{$tradeVal['id']}','{$tradeVal['Uid']}','{$name}','{$userType}','{$tradeAcco}','{$amount}','{$tradeType}','{$tradeStatus}','{$fundType}','{$paymentExpense}',".
                "'{$superviseExpense}','{$confirmDay}') ON DUPLICATE KEY UPDATE `PaymentExpense`='{$paymentExpense}',`SuperviseExpense` = '{$superviseExpense}'";
                try {
                    $db_local->createCommand($inSql)->execute();
                    parent::commandLog($inSql,0,$logFile);
                } catch (Exception $e) {
                    parent::commandLog($e->getMessage(),1,$logFile);
                }
                
            }//循环结束
        }
        $this->Uncost();
    }
    /**
     * 处理uncost数据
     */
    public function Uncost()
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
        $logFile = 'Cost_Un_'.date('Ymd').'.log';
        $unRs = $db_local->createCommand("SELECT * FROM `uncost` WHERE `Status` = 0")->queryAll();
        if (!empty($unRs) && is_array($unRs))
        {
            $tradeDay = date('Y-m-d');
            foreach ($unRs as $value)
            {
                $tradeTable = "trade_order_{$value['Instid']}";
                $tradeVal = $db_local->createCommand("SELECT * FROM {$tradeTable} WHERE id = {$value['TradeOrderId']}")->queryOne();
                if (empty($tradeVal)){
                    parent::commandLog("查询不到交易表记录 sql=SELECT * FROM {$tradeTable} WHERE id = {$value['TradeOrderId']}",1,$logFile);
                    continue;
                }
                /*******单条记录处理开始*********************/
                $paymentExpense = $superviseExpense=$amount = 0;
                $fundInfo = CommFun::GetFundInfo($tradeVal['FundCode']);
                if (empty($fundInfo)){
                    parent::commandLog("FundCode={$tradeVal['FundCode']}获取不到fundinfo数据，未处理{$tradeTable} id={$tradeVal['id']}",1,$logFile);
                    continue;
                }
                if ($tradeVal['TradeStatus'] ==9)
                {
                    continue;
                }
                //买入(认/申购、定期定额)
                if ($tradeVal['TradeType'] ==0 || $tradeVal['TradeType'] ==3)
                {
                    //扣款成功
                    if ($tradeVal['DeductMoney']==2)
                    {
                        //支付成本
                        if ($fundInfo['MarketFee'] ==0)
                        {
                            //前后端收费基金
                            $paymentExpense = bcmul($tradeVal['ApplyAmount'],0.003,3)<3?3:bcmul($tradeVal['ApplyAmount'],0.003,3);
                        }else {
                            //按保有量收取营销费基金
                            $paymentExpense = bcmul($tradeVal['ApplyAmount'],0.0002,3);
                            if ($paymentExpense <2){
                                $paymentExpense = 2;
                            }elseif ($paymentExpense >20){
                                $paymentExpense = 20;
                            }
                        }
                        //监管成本
                        if ($fundInfo['FundTypeCode']==1109 || $fundInfo['FundTypeCode']==1106)
                        {
                            $superviseExpense = 0;//货基/理财型为零
                        }elseif ($fundInfo['FundTypeCode']==1105){
                            $superviseExpense = bcmul($tradeVal['ApplyAmount'],0.0005,3);//债券型万五
                        }elseif (in_array($fundInfo['FundTypeCode'], [1101,1110,1103]))
                        {
                            //股票、混合、QDII基金
                            if ($value['Instid'] ==1000)
                            {
                                //机构企业做金额判断
                                $isStand = self::getStand($fundInfo['InnerCode'], $tradeVal['CallingCode'], $tradeVal['ApplyAmount']);
                                if ($isStand){
                                    $superviseExpense = 100;
                                }else {
                                    $superviseExpense = bcmul($tradeVal['ApplyAmount'],0.001,3);
                                }
                            }else {
                                //普通商户都是千一
                                $superviseExpense = bcmul($tradeVal['ApplyAmount'],0.001,3);
                            }
                        }
                        //失败 ->划出款
                        if ($tradeVal['TradeStatus']==0)
                        {
                            $isRegulatoryBank = self::RegulatoryBank($db_local, $tradeVal['Uid'], $value['Instid']);
                            //企业端的如果用民生银行无手续费
                            if(!$isRegulatoryBank){
                                if ($tradeVal['ApplyAmount']<50000)
                                {
                                    $superviseExpense +=2 ;
                                }else {
                                    $superviseExpense +=5.5;
                                }
                            }
                        }
                    }
                    $amount = $tradeVal['ApplyAmount'];
                }
                //卖出
                elseif ($tradeVal['TradeType'] ==1)
                {
                    //企业端的如果用民生银行无手续费
                    if (in_array($tradeVal['TradeStatus'], [1,2,3,5]))
                    {
                        $isRegulatoryBank = self::RegulatoryBank($db_local, $tradeVal['Uid'], $value['Instid']);
                        if(!$isRegulatoryBank){
                            if ($tradeVal['ConfirmAmount']<50000)
                            {
                                $superviseExpense +=2 ;
                            }else {
                                $superviseExpense +=5.5;
                            }
                        }
                    }
                    $amount = $tradeVal['ConfirmAmount'];
                }
                //撤单
                elseif ($tradeVal['TradeType']==2)
                {
                    $oriTrade = $db_local->createCommand("SELECT * FROM {$tradeTable} WHERE ApplySerial = '{$tradeVal['OriginalApplyserial']}'")->queryOne();
                    if (empty($oriTrade)){
                        parent::commandLog("查询不到原订单，sql=SELECT * FROM {$tradeTable} WHERE ApplySerial = '{$tradeVal['OriginalApplyserial']}'",1,$logFile);
                        continue;
                    }
                    if ($oriTrade['TradeType']==0 || $oriTrade['TradeType']==3)
                    {
                        $isRegulatoryBank = self::RegulatoryBank($db_local, $tradeVal['Uid'], $value['Instid']);
                        if (!$isRegulatoryBank)
                        {
                            if ($oriTrade['ApplyAmount']<50000)
                            {
                                $superviseExpense +=2 ;
                            }else {
                                $superviseExpense +=5.5;
                            }
                            $amount = $oriTrade['ApplyAmount'];
                        }
                    }
                }
                if ($paymentExpense ==0 && $superviseExpense==0 ){
                    $upSql = "UPDATE `uncost` SET `Status` = 1 WHERE id = {$value['id']}";
                    $db_local->createCommand($upSql)->execute();
                    parent::commandLog($upSql,0,$logFile);
                    continue;//成本都为0时不记录
                }
                $rs = $db_local->createCommand("SELECT u.`Name` FROM `user` u WHERE u.id = {$tradeVal['Uid']}")->queryOne();
                $name = empty($rs['Name'])?'未知':$rs['Name'];
                $userType = $value['Instid']==1000?'o':'p';
                $tradeAcco = $tradeVal['TradeAcco'];
                $tradeType = $tradeVal['TradeType'];
                $tradeStatus = $tradeVal['TradeStatus'];
                $fundType = $fundInfo['FundTypeCode'];
                $inSql = "INSERT INTO cost_{$value['Instid']} (`TradeId`,`Uid`,`Name`,`UserType`,`TradeAcco`,`Amount`,`TradeType`,`TradeStatus`,`FundType`,`PaymentExpense`,`SuperviseExpense`,`TradeDay`)".
                    " VALUES ('{$tradeVal['id']}','{$tradeVal['Uid']}','{$name}','{$userType}','{$tradeAcco}','{$amount}','{$tradeType}','{$tradeStatus}','{$fundType}','{$paymentExpense}',".
                    "'{$superviseExpense}','{$tradeDay}') ON DUPLICATE KEY UPDATE `PaymentExpense`='{$paymentExpense}',`SuperviseExpense` = '{$superviseExpense}'";
                $upSql = "UPDATE `uncost` SET `Status` = 1 WHERE id = {$value['id']}";
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
                /******单条记录处理结束***************************/
            }
        }
    }
    /**
     * 判断是否监管银行(民生)免手续费
     * @param resource $db_local 数据库连接串
     * @param int $uid 用户id
     * @param int $instid 商户id
     * @return bool true/false
     */
    private static function RegulatoryBank($db_local,$uid,$instid)
    {
        if ($instid ==1000)
        {
            $regulatorybankstr = '民生';
            $bankName = $db_local->createCommand("SELECT BankName FROM `company_attach` WHERE Uid = {$uid}")->queryScalar();
            if(strpos($bankName, $regulatorybankstr) ===false)
            {
                return false;
            }else {
                return true;
            }
        }else {
            return false;
        }
    }
    /**
     * 判断基金购买是否按笔收费(大额)
     * @param string $innercode 基金内部编码(聚源)
     * @param string $callingcode 业务代码 122申购/120认购
     * @param string $amount 金额
     * @return bool true/false
     */
    private static function getStand($innercode,$callingcode,$amount)
    {
        if ($callingcode =='122'){
            $chargeratetype = '11010';
        }elseif ($callingcode =='120'){
            $chargeratetype = '10010';
        }else {
            return false;
        }
        $sql = "SELECT * FROM MF_ChargeRateNew WHERE InnerCode = '{$innercode}' AND IfExecuted = 1 AND ClientType = 10 AND ChargeRateType = '{$chargeratetype}'";
        $resArr = Yii::$app->db_juyuan->createCommand($sql)->queryAll();
        if (!empty($resArr))
        {
            $rs = false;
            foreach ($resArr as $val)
            {
                if ($val['ChargeRateUnit']==7 && $val['DivStandUnit1']==4 && ($val['StDivStand1']*10000 <=$amount ))
                {
                    $rs = true;
                    break;
                }
            }
            return $rs;
        }else {
            return false;
        }
    }
}