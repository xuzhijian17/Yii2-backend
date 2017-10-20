<?php
namespace common\models;
use frontend\modules\api\models\BaseModel;
use Yii;
use yii\base\Model;

/**
 * @author witheng
 *操作model类涉及fund_position_x表(x为商户号，0表示默认从app及网站来源交易记录)
 */
class PortfolioPosition extends BaseModel
{
    function __construct($field=[], $bsNo = 0) {
		parent::__construct($bsNo, $field);
        $this->tbName = 'portfolio_position_'.$bsNo;
    }

	/*
	 * 更新基金数据
	 * @param $portfolioid
	 * @param $updatearr
	 * @return bool
	 */
	public function updateFundData($portfolioid, $updatearr)
	{
		if (!empty($updatearr) && is_array($updatearr)) {
            $fieldStr = $valueStr = '';
            foreach ($updatearr as $key => $value) {
                $fieldStr .='`'.$key.'` = \''.$value.'\',';
            }
            $fieldStr = rtrim($fieldStr,',');
            $sql = "UPDATE {$this->tbName} SET {$fieldStr} WHERE id='{$portfolioid}'";
            $rs = $this->db->createCommand($sql)->execute();
            if ($rs >0) {
                return true;
            } else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
	}

	/*
	 * 交易、持仓表事务处理
	 * @param int $merid 商户号
	 * @param int $portfolioid 组合编号
	 * @param int $type 交易类型 0:买入 1:卖出 2:撤单
	 * @param array $tradeFields 交易表字段值
	 * @return mixed 最新订单表id/false 失败
	 */
	public static function HandleOrderPosition($merid, $portfolioid, $type, $tradeFields)
	{

	    $db_local = Yii::$app->db_local;
	    $trade_table = 'trade_order_'.$merid;
	    $position_table = 'fund_position_'.$merid;
		$portfolio_position_table = 'portfolio_position_'.$merid;

	    if (empty($tradeFields)  || empty($tradeFields['Uid']) || empty($portfolioid)) {
	        \Yii::error("参数错误为空",__METHOD__);
	        return false;
	    }

		//用户基金总持仓(包含单支基金持仓和组合基金持仓)
		$fund_posit_sql = "SELECT * FROM {$position_table} WHERE Uid = {$tradeFields['Uid']} AND FundCode = '{$tradeFields['FundCode']}'";
		$rs_position = $db_local->createCommand($fund_posit_sql)->queryOne();
		//组合基金持仓
		$portfolio_sql = "SELECT * FROM {$portfolio_position_table} WHERE Uid = {$tradeFields['Uid']} AND FundCode = '{$tradeFields['FundCode']}' AND PortfolioId='{$portfolioid}'";
		$rs_portfolio = $db_local->createCommand($portfolio_sql)->queryOne();

	    if ($type ==0) { //申购基金
	        $arr_trade = FundPosition::JoinStr($tradeFields);
	        $sql_trade = "INSERT INTO {$trade_table} ({$arr_trade['fields']}) VALUES ({$arr_trade['values']})"; //交易记录

	    } elseif ($type ==1) { //赎回基金
	        if (empty($rs_position) || empty($rs_portfolio)) { //无持仓,报错
	            Yii::error("赎回操作无持仓,sql:{$fund_posit_sql}",__METHOD__);
	            return false;
	        }
			//有持仓修改 低于可卖份额 报错
			if ($rs_portfolio['CurrentRemainShare']-$rs_portfolio['FreezeSellShare'] < $tradeFields['ApplyShare']) {
				Yii::error("赎回份额低于可卖份额ApplyShare:{$tradeFields['ApplyShare']}rs_position:".json_encode($rs_position),__METHOD__);
				return false;
			}
			//基金总持仓加上赎回冻结份额
			$sql_position = "UPDATE {$position_table} SET FreezeSellShare = FreezeSellShare+{$tradeFields['ApplyShare']} WHERE id = {$rs_position['id']} ";

			//组合基金持仓加上赎回冻结份额
			$sql_portfolio_position = "UPDATE {$portfolio_position_table} SET FreezeSellShare = FreezeSellShare+{$tradeFields['ApplyShare']} WHERE id = {$rs_portfolio['id']} ";
			$arr_trade = FundPosition::JoinStr($tradeFields);
			$sql_trade = "INSERT INTO {$trade_table} ({$arr_trade['fields']}) VALUES ({$arr_trade['values']})";

	    } elseif ($type ==2) { //撤单
	        $fund_trade_sql = "SELECT * FROM {$trade_table} WHERE ApplySerial = '{$tradeFields['OriginalApplyserial']}'";
	        $rs_trade = $db_local->createCommand($fund_trade_sql)->queryOne();
	        //要撤单的订单为空 报错，否则根据原订单修改持仓
	        if (empty($rs_trade)) {
	            Yii::error("要撤单的订单为空,sql:{$fund_trade_sql}",__METHOD__);
	            return false;
	        }
	        //查找原对应持仓记录
	        if (empty($rs_position)) {
	            Yii::error("要撤单的持仓不存在,sql:{$fund_posit_sql}",__METHOD__);
	            return false;
	        }
	        //判断原订单申购、赎回 相应修改持仓
			if ($rs_trade['TradeType'] ==1) {
	            //原赎回撤单
	            $sql_position = "UPDATE {$position_table} SET FreezeSellShare = FreezeSellShare-{$rs_trade['ApplyShare']} WHERE id = {$rs_position['id']} ";
				$sql_portfolio_position = "UPDATE {$portfolio_position_table} SET FreezeSellShare = FreezeSellShare-{$rs_trade['ApplyShare']} WHERE id = {$rs_portfolio['id']} ";
	        } else {
	            Yii::error("原订单类型不可撤单,sql:{$fund_trade_sql}",__METHOD__);
	            return false;
	        }
	        $arr_trade = FundPosition::JoinStr($tradeFields);
	        $sql_trade = "INSERT INTO {$trade_table} ({$arr_trade['fields']}) VALUES ({$arr_trade['values']})";
	        $sql_trade_withdrow = "UPDATE `{$trade_table}` SET TradeStatus = 4 WHERE id = {$rs_trade['id']}";
	    } else {
	        Yii::error("传参有误:{$type}",__METHOD__);
	        return false;
	    }
	    $transaction = $db_local->beginTransaction();
	    try {
	        $db_local->createCommand($sql_trade)->execute(); //交易记录表
	        $last_trade_id = $db_local->getLastInsertID();
			if (!empty($sql_position) ) {
				$db_local->createCommand($sql_position)->execute(); //基金总持仓表
			}
			if (!empty($sql_portfolio_position)) {
				$db_local->createCommand($sql_portfolio_position)->execute(); //组合持仓表
			}
	        if (!empty($sql_trade_withdrow)) {
	            //撤单后修改原订单状态
	            $db_local->createCommand($sql_trade_withdrow)->execute();
	        }
	        $transaction->commit();
	    } catch (Exception $e) {
	        $transaction->rollBack();
	        Yii::error("catch异常:{$e->getMessage()}", __METHOD__);
			return false;
	    }
	    return true;
	}

	public static function updatePortfolioPositionByFundPosition($uid, $instid, $fundcode, $totalShares, $totalIncome)
	{

	}
}