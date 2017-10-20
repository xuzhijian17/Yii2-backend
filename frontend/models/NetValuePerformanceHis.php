<?php
namespace frontend\models;
use Yii;
class NetValuePerformanceHis extends Model
{
	public static $tableName = 'MF_NetValuePerformanceHis';
	public static function getFundLine($InnerCode, $type = 1)
	{
		$data = [];
		$endData = self::find([
			'field' => 'TradingDay, UnitNV',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'TradingDay desc'
		]);//最新日期
		//echo self::$lastSql;
		if(!$endData) return false;
		$startDate = date("Y-m-d", strtotime("-{$type} month", strtotime($endData['TradingDay'])));
		$startData = self::find([
			'field' => 'TradingDay, UnitNV',
			'where' => 'InnerCode = '.$InnerCode.' and TradingDay > "'.$startDate.'"',
		]);//开始日期
		$RiskAnalys = self::query([
			'field' => 'TradingDay, UnitNV',
			'where' => 'InnerCode = '.$InnerCode.' and TradingDay > "'.$startData['TradingDay'].'"',
			//'order' => 'TradingDay desc'
		]);
		if($RiskAnalys){
			$data[substr($startData['TradingDay'], 0, 10)] = ['time' => strtotime($startData['TradingDay']), 'fundRate' => 0, 'fundNet' => round($startData['UnitNV'], 2)];
			foreach($RiskAnalys as $val){
				$data[substr($val['TradingDay'], 0, 10)] = [
					'time' => strtotime($val['TradingDay']),
					'fundRate' => round((($val['UnitNV']-$startData['UnitNV']) * 100)/$startData['UnitNV'], 2), // 当天减去开始第一天净值
					'fundNet' => round($val['UnitNV'], 2),
				];
			}
		}
		return $data;
	}
	# 获取同类基金的k线
	public static function getFundGroupLine($InnerCode, $type = 1, $typeCode)
	{
		$data = [];
		$endData = self::find([
			'field' => 'TradingDay, UnitNV',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'TradingDay desc'
		]);
		//echo self::$lastSql;
		if(!$endData) return false;
		$startDate = date("Y-m-d", strtotime("-{$type} month", strtotime($endData['TradingDay'])));
		$startData = self::find([
			'field' => 'TradingDay, avg(UnitNV) GUnitNV',
			'where' => 'InnerCode IN (SELECT InnerCode FROM MF_FundArchivesAttach WHERE DataCode = '.$typeCode.'
						AND TypeCode = 10) and TradingDay > "'.$startDate.'"',
			'group' => 'TradingDay',
			'order' => 'TradingDay asc',
		]);
		//echo self::$lastSql;
		$RiskAnalys = self::query([
			'field' => 'TradingDay, avg(UnitNV) GUnitNV',
			'where' => 'InnerCode IN (SELECT InnerCode FROM MF_FundArchivesAttach WHERE DataCode = '.$typeCode.'
						AND TypeCode = 10) and TradingDay > "'.$startData['TradingDay'].'"',
			'group' => 'TradingDay',
			'order' => 'TradingDay asc'
		]);
		//print_r($startDate);
		if($RiskAnalys){
			$data[substr($startData['TradingDay'], 0, 10)] = ['time' => strtotime($startData['TradingDay']), 'gfundRate' => 0, 'gfundNet' => round($startData['GUnitNV'], 2)];
			foreach($RiskAnalys as $val){
				$data[substr($val['TradingDay'], 0, 10)] = [
					'time' => strtotime($val['TradingDay']),
					'gfundRate' => round((($val['GUnitNV']-$startData['GUnitNV']) * 100)/$startData['GUnitNV'], 2),
					'gfundNet' => round($val['GUnitNV'], 2),
				];
			}
		}
		return $data;
	}
}