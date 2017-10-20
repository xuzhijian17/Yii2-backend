<?php
namespace frontend\models;
use Yii;
class ChargeRate extends Model
{
	public static $tableName = 'MF_ChargeRate';
	# 费率表 type 2 买入 3 卖出
	public static function getChargeRateList($InnerCode, $type = 2){
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and IfExecuted=1 and ChargeRateType = '.$type.' and 
						ExcuteDate = (select max(ExcuteDate) ExcuteDate from MF_ChargeRate where InnerCode = '.$InnerCode.' and ChargeRateType = '.$type.') 
						and (ChargePattern in (1, 2, 102, 202) or ChargePattern is null) ',
			'order' => 'ExcuteDate desc',
		];
		//$param['where'] = $type == 2 ? 'InnerCode = '.$InnerCode.' and IfExecuted=1 and ChargeRateType = '.$type.' and ChargePattern in (1, 2, 102, 202) ',
		$data = self::_query($param);
		if($data){
			$EndOfApplySumInterval = []; $EndOfHoldTermInterval = [];
			foreach($data as $key=>$val){
				if($type == 2){
					if(in_array($val['EndOfApplySumInterval'], $EndOfApplySumInterval)){
						unset($data[$key]); continue;
					}
					$EndOfApplySumInterval[] = $val['EndOfApplySumInterval'];
				}
				if($type == 3){
					if(in_array($val['EndOfHoldTermInterval'], $EndOfHoldTermInterval)){
						unset($data[$key]); continue;
					}
					$EndOfHoldTermInterval[] = $val['EndOfHoldTermInterval'];
				}
			}
		}
		//echo self::$lastSql;
		return $data;
	}
	# type 9 托管费 8 服务费 
	public static function getChargeRateFind($InnerCode, $type = 2){
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and IfExecuted=1 and SerialNumber = 1 and ChargeRateType = '.$type,
			'order' => 'ExcuteDate desc',
		];
		$data = self::_find($param);
		return $data;
	}
	/**
	 * 查询卖出费率
	 * @param string $fundcode 基金代码
	 * @param int $type 2:申购费 3:赎回费
	 * @return array['IntervalDescription'=>'持有期描述','ChargeRateDesciption'=>'费率']
	 */
	public static function getChargeRateData($fundcode,$type=3)
	{
	    $sql = "SELECT c.IntervalDescription,c.ChargeRateDesciption FROM MF_ChargeRate c LEFT JOIN `SecuMain` m 
	        ON c.InnerCode = m.InnerCode WHERE c.ChargeRateType = '{$type}' AND m.SecuCode='{$fundcode}'";
	    $command = Yii::$app->db_juyuan->createCommand($sql);
	    $data = $command->queryAll();
	    return $data;
	}
}