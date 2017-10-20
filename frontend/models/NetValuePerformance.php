<?php
namespace frontend\models;
use Yii;
class NetValuePerformance extends Model
{
	public static $tableName = 'MF_NetValuePerformance';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'InnerCode = '.$InnerCode,
		];
		$data = NetValuePerformance::_find($param);
		return $data;
	}
	//获取基金最近表现（股票债券 混合）
	public static function getFundResult($fundType)
	{
		$data = [];
		$param = [
			'field' => 'MF_FundArchivesAttach.InnerCode InnerCode, RRInSingleWeek, RRInSingleMonth, RRInThreeMonth, 
						RRInSixMonth, RRInSingleYear, RRSinceThisYear',
			'join' => 'left join MF_FundArchivesAttach on MF_FundArchivesAttach.InnerCode = MF_NetValuePerformance.InnerCode',
			'where' => 'MF_FundArchivesAttach.DataCode = '.$fundType.' and MF_FundArchivesAttach.TypeCode=10',
		];
		$result = self::query($param);
		//echo self::$lastSql;
		if($result){
			foreach($result as $val){
				$data[$val['InnerCode']] = $val;
			}
		}
		return $data;
	}
	
	//基金同类型排序
	public static function getFundResultSort($data, $typeField)
	{
		//print_r($data);
		return self::sortAlgorithm($data, $typeField);
	}
	
	//排序算法
	public static function sortAlgorithm($data, $field = 'RRInSingleWeek')
	{
		if(!$data)	return [];
		$sortKey = [];
		foreach($data as $key=>$val){
			if($val[$field] != "")
				$sortKey[$key] = $val[$field];
		}
		arsort($sortKey); $sortData = [];
		$i = 1;
		foreach($sortKey as $key=>$val){
			$sortData[$key] = $data[$key];
			$sortData[$key]['sort'] = $i;
			$i++;
		}
		return $sortData;
	}
}