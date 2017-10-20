<?php
namespace frontend\models;
use Yii;
//基金净值历史净值
class NetValueCashe extends Model
{
	public static $tableName = 'MF_NetValueCashe';
	//获取历史净值列表
	public static function getNetValueList($InnerCode, $page = 1, $limit = 0)
	{
		$data = [];
		$size = $limit ? : 10;
		$param = [
			'field' => 'EndDate, UnitNV, AccumulatedUnitNV, NVDailyGrowthRate',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'EndDate desc',
			'limit' => (($page - 1) * $size).', '.$size,
		];
		$data = self::query($param);
		if(!$data) return $data;
		foreach($data as &$val){
			$val['EndDate'] = substr($val['EndDate'], 0, 10);
			$val['UnitNV'] = sprintf("%.4f", round($val['UnitNV'], 4));
			$val['AccumulatedUnitNV'] = sprintf("%.4f", round($val['AccumulatedUnitNV'], 4));
			$val['NVDailyGrowthRate'] = sprintf("%.2f", round($val['NVDailyGrowthRate'], 2));
		}
		return $data;
	}
	
	public static function getNetValueOrderList($InnerCode, $date, $page = 1)
	{
		$data = []; $size = 10;
		$param = [
			'field' => 'EndDate, UnitNV, AccumulatedUnitNV, NVDailyGrowthRate',
			'where' => 'InnerCode = '.$InnerCode.' and EndDate >="'.$date.'"',
			'order' => 'EndDate desc',
			'limit' => (($page - 1) * $size).', '.$size,
		];
		$data = self::query($param);
		if(!$data) return $data;
		foreach($data as &$val){
			$val['EndDate'] = substr($val['EndDate'], 0, 10);
			$val['UnitNV'] = sprintf("%.4f", round($val['UnitNV'], 4));
		}
		return $data;
	}
}