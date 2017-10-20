<?php
namespace frontend\models;
use Yii;
//基金净值（货币和理财）
class NetValue extends Model
{
	public static $tableName = 'MF_NetValue';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'EndDate desc'
		];
		$data = NetValue::_find($param);
		return $data;
	}
	public static function getNetValueList($InnerCode, $page = 1, $limit = 0)
	{
		$data = [];
		$size = $limit ? : 10;
		$param = [
			'field' => 'EndDate, DailyProfit UnitNV, LatestWeeklyYield NVDailyGrowthRate',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'EndDate desc',
			'limit' => (($page - 1) * $size).', '.$size,
		];
		$data = self::query($param);
		//echo self::$lastSql;
		if(!$data) return $data;
		foreach($data as &$val){
			$val['EndDate'] = substr($val['EndDate'], 0, 10);
			$val['UnitNV'] = sprintf("%.4f", round($val['UnitNV'], 4));
			$val['NVDailyGrowthRate'] = sprintf("%.4f", round($val['NVDailyGrowthRate']*100, 4));
		}
		return $data;
	}
	
	public static function getNetValueOrderList($InnerCode, $date, $page = 1)
	{
		$data = []; $size = 10;
		$param = [
			'field' => 'EndDate, DailyProfit UnitNV, LatestWeeklyYield NVDailyGrowthRate',
			'where' => 'InnerCode = '.$InnerCode.' and EndDate >="'.$date.'"',
			'order' => 'EndDate desc',
			'limit' => (($page - 1) * $size).', '.$size,
		];
		$data = self::query($param);
		//echo self::$lastSql;
		if(!$data) return $data;
		foreach($data as &$val){
			$val['EndDate'] = substr($val['EndDate'], 0, 10);
			$val['UnitNV'] = sprintf("%.4f", round($val['UnitNV'], 4));
		}
		return $data;
	}
	
	public static function getFundLine($InnerCode, $type = 1)
	{
		$data = [];
		$endData = self::_find([
			'field' => 'EndDate, DailyProfit, LatestWeeklyYield',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'EndDate desc'
		]);
		//echo self::$lastSql;
		if(!$endData) return false;
		$startDate = date("Y-m-d", strtotime("-{$type} month", strtotime($endData['EndDate'])));
		$startData = self::_find([
			'field' => 'EndDate, DailyProfit',
			'where' => 'InnerCode = '.$InnerCode.' and EndDate > "'.$startDate.'"',
		]);
		$RiskAnalys = self::_query([
			'field' => 'EndDate, DailyProfit, LatestWeeklyYield',
			'where' => 'InnerCode = '.$InnerCode.' and EndDate > "'.$startData['EndDate'].'"',
			//'order' => 'TradingDay desc'
		]);
		if($RiskAnalys){
			foreach($RiskAnalys as $val){
				$data[substr($val['EndDate'], 0, 10)] = [
					'time' => strtotime($val['EndDate']),
					'fundRate' => round($val['LatestWeeklyYield']*100, 2),
					'fundNet' => round($val['DailyProfit'], 2),
				];
			}
		}
		return $data;
	}
}