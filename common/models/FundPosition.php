<?php
namespace common\models;
use Yii;
use yii\base\Model;
use common\lib\HundSun;
use Exception;
use console\commands\Controller;
/**
 * @author ddz
 *操作model类涉及fund_position_x表(x为商户号，0表示默认从app及网站来源交易记录)
 */
class FundPosition extends Model
{
    public $bsNo = 0;//来源商户号
    public $field = [];//数据参数['fieldname'=>'fieldvalue'...]非空
    public $tbName = '';//数据表名

    function __construct($field=[],$bsNo = 0) {
        $this->bsNo = $bsNo;
        $this->field = $field;
        $this->tbName = 'fund_position_'.$bsNo;
    }
    
    /**
     * 插入数据
     * @return mixed 成功:最新id/false失败
     */
    public function insert()
    {
        if (!empty($this->field) && is_array($this->field))
        {
            $db_local = Yii::$app->db_local;
            $fieldStr = $valueStr = '';
            foreach ($this->field as $key => $value) {
                if ($value === null)
                {
                    continue;
                }else {
                    $fieldStr .='`'.$key.'`,';
                    $valueStr .= "'{$value}',";
                }
            }
            $fieldStr = rtrim($fieldStr,',');
            $valueStr = rtrim($valueStr,',');
            $sql = "INSERT INTO {$this->tbName} ({$fieldStr}) VALUES ({$valueStr})";
            $rs = $db_local->createCommand($sql)->execute();
            if ($rs >0)
            {
                return $db_local->getLastInsertID();
            }else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
    }
    /**
     * 查询
     * @param string $where sql语句where部分
     * @param string $rs 'one'一条;'all'多条
     * @return array
     */
    public function query($where,$rs='one',$order='',$limit='')
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM {$this->tbName} WHERE ".$where;
		if (!empty($order)) {
			$sql .= " order by ".$order;
		}
		if (!empty($limit)) {
			$sql .= " limit ".$limit;
		}

        if ($rs == 'all'){
            $arr = $db_local->createCommand($sql)->queryAll();
        }else {
            $arr = $db_local->createCommand($sql)->queryOne();
        }
        return $arr;
    }
    /**
     * 
     * @param string $where sql语句where部分
     * @param string $rs
     * @param string $order
     * @param string $limit
     * @return unknown
     */
    public function update($where)
    {
        $db_local = Yii::$app->db_local;
        if (!empty($this->field) && is_array($this->field))
        {
            $db_local = Yii::$app->db_local;
            $fieldStr = $valueStr = '';
            foreach ($this->field as $key => $value) {
                $fieldStr .='`'.$key.'` = \''.$value.'\',';
            }
            $fieldStr = rtrim($fieldStr,',');
            $sql = "UPDATE {$this->tbName} SET {$fieldStr} WHERE {$where} ";
            $rs = $db_local->createCommand($sql)->execute();
            if ($rs >0)
            {
                return true;
            }else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
    }
	
	public function getFundTotal($uid, $FundCode)
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM {$this->tbName} WHERE Uid={$uid} and FundCode = '{$FundCode}' and TradeType in(0, 2, 3) and TradeStatus=2";
        $arr = $db_local->createCommand($sql)->queryAll();
        return $arr;
    }
	
	/**
	 *  更新基金数据
	 */
	public function updateFundData($uid,$fundcode,$updatearr)
	{
		$db_local = Yii::$app->db_local;
		if (!empty($updatearr) && is_array($updatearr))
        {
            $db_local = Yii::$app->db_local;
            $fieldStr = $valueStr = '';
            foreach ($updatearr as $key => $value) {
                $fieldStr .='`'.$key.'` = \''.$value.'\',';
            }
            $fieldStr = rtrim($fieldStr,',');
            $sql = "UPDATE {$this->tbName} SET {$fieldStr} WHERE Uid=".$uid." and FundCode='".$fundcode."'";
			//echo $sql."\n";
            $rs = $db_local->createCommand($sql)->execute();
            if ($rs >0)
            {
                return true;
            }else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
	}
	/**
	 * 交易、持仓表事务处理
	 * @param int $merid 商户号
	 * @param int $type 交易类型 0:买入 1:卖出 2:撤单
	 * @param array $tradeFields 交易表字段值
	 * @return mixed 最新订单表id/false 失败
	 */
	public static function HandleOrderPosition($merid,$type,$tradeFields)
	{
	    $db_local = Yii::$app->db_local;
	    $trade_table = 'trade_order_'.$merid;
	    $position_table = 'fund_position_'.$merid;
	    if (empty($tradeFields)  || empty($tradeFields['Uid']))
	    {
	        \Yii::error("参数错误为空".var_export($tradeFields,true),__METHOD__);
	        return false;
	    }
	    if ($type ==0)
	    {
	        $arr_trade = self::JoinStr($tradeFields);
	        $sql_trade = "INSERT INTO {$trade_table} ({$arr_trade['fields']}) VALUES ({$arr_trade['values']})";
	    }elseif ($type ==1)
	    {
	        $fund_posit_sql = "SELECT * FROM {$position_table} WHERE Uid = {$tradeFields['Uid']} AND FundCode = '{$tradeFields['FundCode']}'";
	        $rs_position = $db_local->createCommand($fund_posit_sql)->queryOne();
	        if (empty($rs_position))
	        {
	            //无持仓,报错
	            Yii::error("赎回操作无持仓,sql:{$fund_posit_sql}",__METHOD__);
	            $hs = new HundSun($tradeFields['Uid']);
	            $resS001 = $hs->apiRequest('S001',['fundcode'=>$tradeFields['FundCode']]);
	            if ($resS001['code']==HundSun::SUCC_CODE && !empty($resS001['returnlist'][0]))
	            {
	                $sql_position = "INSERT INTO {$position_table} (`Uid`,`FundCode`,`CurrentRemainShare`,`FreezeSellShare`,`InitTime`,`Lastuptime`) VALUES 
	                ('{$tradeFields['Uid']}','{$tradeFields['FundCode']}','{$resS001['returnlist'][0]['currentremainshare']}','{$tradeFields['ApplyShare']}',
	                '".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')";
	            }else {
	                Yii::error("赎回操作无持仓,sql:{$fund_posit_sql},且S001出错:".var_export($resS001,true),__METHOD__);
	            }
	        }else {
	            //有持仓修改 低于可卖份额 报错
	            if(bcsub($rs_position['CurrentRemainShare'], $rs_position['FreezeSellShare'],2) < $tradeFields['ApplyShare'])
	            {
	                Yii::error("赎回份额低于可卖份额ApplyShare:{$tradeFields['ApplyShare']}rs_position:".var_export($rs_position,true),__METHOD__);
	            }
	            $sql_position = "UPDATE {$position_table} SET FreezeSellShare = FreezeSellShare+{$tradeFields['ApplyShare']},Lastuptime = '".date('Y-m-d H:i:s')."' WHERE id = {$rs_position['id']} ";
	        }
	        $arr_trade = self::JoinStr($tradeFields);
	        $sql_trade = "INSERT INTO {$trade_table} ({$arr_trade['fields']}) VALUES ({$arr_trade['values']})";
	    }elseif ($type ==2)
	    {
	        $fund_trade_sql = "SELECT * FROM {$trade_table} WHERE ApplySerial = '{$tradeFields['OriginalApplyserial']}'";
	        $rs_trade = $db_local->createCommand($fund_trade_sql)->queryOne();
	        //要撤单的订单为空 报错，否则根据原订单修改持仓
	        if (empty($rs_trade))
	        {
	            Yii::error("要撤单的订单为空,sql:{$fund_trade_sql}",__METHOD__);
	        }
	        //查找原对应持仓记录
	        $fund_posit_sql = "SELECT * FROM {$position_table} WHERE Uid = {$rs_trade['Uid']} AND FundCode = '{$rs_trade['FundCode']}'";
	        $rs_position = $db_local->createCommand($fund_posit_sql)->queryOne();
	        if (!empty($rs_position) && $rs_trade['TradeType'] ==1)
	        {
	            $freezeSellShare = ($rs_position['FreezeSellShare'] < $rs_trade['ApplyShare'])?0:$rs_position['FreezeSellShare'] - $rs_trade['ApplyShare'];
	            //原赎回撤单
	            $sql_position = "UPDATE {$position_table} SET FreezeSellShare = {$freezeSellShare},Lastuptime = '".date('Y-m-d H:i:s')."' WHERE id = {$rs_position['id']} ";
	        }
	        $arr_trade = self::JoinStr($tradeFields);
	        $sql_trade = "INSERT INTO {$trade_table} ({$arr_trade['fields']}) VALUES ({$arr_trade['values']})";
	        $sql_trade_withdrow = "UPDATE `{$trade_table}` SET TradeStatus = 4,HandleStatus=1,HandleTime='{$tradeFields['ApplyTime']}' WHERE id = {$rs_trade['id']}";
	    }else {
	        Yii::error("传参有误:{$type}",__METHOD__);
	        return false;
	    }
	    $transaction = $db_local->beginTransaction();
	    try {
	        $db_local->createCommand($sql_trade)->execute();
	        $last_trade_id = $db_local->getLastInsertID();
	        if (!empty($sql_position))
	        {
	            $db_local->createCommand($sql_position)->execute();
	        }
	        if (!empty($sql_trade_withdrow))
	        {
	            //撤单后修改原订单状态
	            $db_local->createCommand($sql_trade_withdrow)->execute();
	        }
	        $transaction->commit();
	    } catch (Exception $e) {
	        $transaction->rollBack();
	        Yii::error($e->getMessage(),__METHOD__);
	    }
	    return empty($last_trade_id)?0:$last_trade_id;
	}
	/**
	 * 封装处理字段拼装
	 * @param array $array 数据库字段键值对
	 * @return array ['fields'=>'字段名','values'=>'字段值']
	 */
	public static function JoinStr($array)
	{
	    if (empty($array)){
	        return false;
	    }
	    $fieldStr = $valueStr = '';
	    foreach ($array as $key => $value) {
	        if ($value === null)
	        {
	            continue;
	        }else {
	            $fieldStr .='`'.$key.'`,';
	            $valueStr .= "'{$value}',";
	        }
	    }
	    $fieldStr = rtrim($fieldStr,',');
	    $valueStr = rtrim($valueStr,',');
	    return ['fields'=>$fieldStr,'values'=>$valueStr];
	}

	/*
	 * 通过用户id获取用户的总持仓市值
	 * @param $uid
	 * @return array['sum'=>'总市值','sumdayprofitloss'=>'当日盈亏汇总','sumtotalprofitloss'=>'累记盈亏汇总']
	 */
	public function getUserTotalPositionByUid($uid)
	{
		$db_local = Yii::$app->db_local;
		$sql = "SELECT SUM(fp.CurrentRemainShare*f.PernetValue) AS sum,SUM(fp.UnpaidIncome) AS unpaid,SUM(fp.DayProfitLoss) AS sumdayprofitloss,
		SUM(fp.TotalProfitLoss) AS sumtotalprofitloss  FROM {$this->tbName} as fp LEFT JOIN fund_info as f ON fp.fundCode=f.fundCode WHERE fp.Uid='{$uid}'";
		$arr = $db_local->createCommand($sql)->queryOne();
		return $arr;
	}

	/*
	 * 通过用户id获取用户的持仓基金数量
	 * @param $uid
	 * @return float
	 */
	public function getUserPositionFundNum($where="")
	{
		$db_local = Yii::$app->db_local;
		$sql = "SELECT COUNT(*) AS count FROM {$this->tbName} ";
		if (!empty($where)) {
			$sql .= " WHERE {$where}";
		}
		$arr = $db_local->createCommand($sql)->queryOne();
		return !empty($arr) ? $arr['count'] : 0.0;
	}
    /**
     * command掉单补充操作trade/Confirmback 
     * @param int $oid trade_order_x表id
     * @param int $instid 商户号
     * @param resource $db_local 数据库连接组件
     * @return bool 操作是否成功
     */
	public static function makeUpConfirmBack($oid,$instid,$db_local)
	{
	    $logFile = "confirmback_".date("Y-m-d");
	    $tradeOrderTable = 'trade_order_'.$instid;//交易表
	    $positionTable = 'fund_position_'.$instid;//持仓表
	    $tradeRs = $db_local->createCommand("SELECT * FROM `{$tradeOrderTable}` WHERE id = '{$oid}'")->queryOne();
	    if (empty($tradeRs))
	    {
	        Yii::error("{$tradeOrderTable}表无此id{$oid}",__METHOD__);
	        return false;
	    }
	    if (empty($tradeRs['ApplySerial']) || empty($tradeRs['Uid']))
	    {
	        //进入处理记录
	        $errInfo = "{$tradeOrderTable}表ApplySerial或Uid字段数据为空id:{$tradeRs['id']}";
	        Yii::error($errInfo,__METHOD__);
	        return false;
	    }
	    $datetime = date('Y-m-d H:i:s');
	    //查询S004 返回结果
	    $hundsun = new HundSun($tradeRs['Uid']);
	    $hundsun->loginHs();
	    $resS004 = $hundsun->apiRequest('S004',['requestno'=>$tradeRs['ApplySerial'],'applyrecordno'=>'1']);
	    if ($resS004['code'] == HundSun::SUCC_CODE && !empty($resS004['returnlist'][0]) && is_array($resS004['returnlist'][0]))
	    {
	        $poundage = $resS004['returnlist'][0]['poundage'];//手续费
	        $confirmShare = $resS004['returnlist'][0]['tradeconfirmshare'];//交易确认份额
	        $confirmAmount = $resS004['returnlist'][0]['tradeconfirmsum'];//交易确认金额
	        $confirmNetValue = $resS004['returnlist'][0]['netvalue'];//净值
	        $tradeStatus = $resS004['returnlist'][0]['confirmflag'];//交易确认标识
	        $handleStatus = 1;
	        $confirmTime = $resS004['returnlist'][0]['confirmdate'];
	        $tradeSql = "UPDATE {$tradeOrderTable} SET `ConfirmShare`='{$confirmShare}',`ConfirmAmount`='{$confirmAmount}',`ConfirmNetValue`='{$confirmNetValue}',`Poundage`='{$poundage}',
	        `TradeStatus`='{$tradeStatus}',`HandleStatus`='{$handleStatus}',`ConfirmTime`='{$confirmTime}' WHERE id = '{$oid}'";
	        //判断申购赎回更改持仓
	        $fundPositionSql = "SELECT * FROM {$positionTable} WHERE Uid = {$tradeRs['Uid']} AND FundCode = '{$tradeRs['FundCode']}'";
	        $rsPosition = $db_local->createCommand($fundPositionSql)->queryOne();
	        //确认成功更改持仓
	        if (in_array($tradeStatus, ['1','2','3']))
	        {
	            //买入情况
	            if ($tradeRs['TradeType'] == 0 || $tradeRs['TradeType'] == 3)
	            {
	                //持仓为空，建仓
	                if (empty($rsPosition))
	                {
	                    //插入持仓数据...
	                    $positionSql = "INSERT INTO `{$positionTable}` (`Uid`,`FundCode`,`CurrentRemainShare`,`InitTime`) VALUES ('{$tradeRs['Uid']}','{$tradeRs['FundCode']}','{$confirmShare}','{$datetime}')";
	                }else {
	                    //更改持仓数量
	                    $positionSql = "UPDATE {$positionTable} SET `CurrentRemainShare` = `CurrentRemainShare`+{$confirmShare} WHERE id='{$rsPosition['id']}' ";
	                }
	                //卖出情况
	            }elseif ($tradeRs['TradeType'] ==1){
	                if (empty($rsPosition))
	                {
	                    //持仓为空，错误
	                    $logInfo = "严重错误:卖出确认数据回来时无持仓,position-sql:{$fundPositionSql}";
	                    Yii::error($logInfo,__METHOD__);
	                    Controller::writeLog($logFile,$logInfo,'ERROR');
	                }else {
	                    $currentRemainShare = ($rsPosition['CurrentRemainShare'] - $confirmShare)<0?0:$rsPosition['CurrentRemainShare'] - $confirmShare;
	                    if ($rsPosition['CurrentRemainShare'] - $confirmShare < 0){
	                        //持仓不足，错误
	                        $logInfo = "严重错误:卖出确认数据回来时持有份额小于可卖份额,确认份额:{$confirmShare},当前份额:{$rsPosition['CurrentRemainShare']},position-sql:{$fundPositionSql}";
	                        Yii::error($logInfo,__METHOD__);
	                        Controller::writeLog($logFile,$logInfo,'ERROR');
	                    }else {
	                        $positionSql = "UPDATE {$positionTable} SET `CurrentRemainShare` = {$currentRemainShare} WHERE id='{$rsPosition['id']}' ";
	                    }
	                }
	            }
	        }else {
	            //失败记录日志，不更改
	            $logInfo = "确认数据未成功:sql:{$tradeSql}，接口返回:".var_export($resS004['returnlist'][0],true);
	            Controller::writeLog($logFile,$logInfo,'INFO');
	        }
	        $transaction = $db_local->beginTransaction();
	        try {
                $db_local->createCommand($tradeSql)->execute();
                $db_local->createCommand($positionSql)->execute();
                $transaction->commit();
                return true;
            } catch (Exception $e) {
	            $transaction->rollBack();
	            Yii::error($e->getMessage(),__METHOD__);
	            return false;
            }
	    }else
        {
            $errInfo = "查询S004不成功table:{$tradeOrderTable};id:{$tradeRs['id']};code={$resS004['code']};message={$resS004['message']}";
            Yii::error($errInfo.var_export($resS004,true),__METHOD__);
            return false;
	    }
	}
}