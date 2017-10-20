<?php
namespace frontend\models;
use Yii;
//基金净值
class BenchmarkGrowthRate extends Model
{
	public static $tableName = 'MF_BenchmarkGrowthRate';
	//获取基金最近表现（货币基金和理财型基金）
	public static function getFundResult($type, $InnerCode)
	{
		$data = [];
		$param = [
			'field' => 'InnerCode InnerCode, BenchGRForThisWeek RRInSingleWeek, 
						MonthlyBenchGR RRInSingleMonth, BenchGRFor3Month RRInThreeMonth, 
						BenchGRFor6Month RRInSixMonth, BenchGRFor1Year RRInSingleYear, BenchGRForThisYear RRSinceThisYear',
			//'join' => 'left join MF_FundArchives on MF_FundArchives.InnerCode = MF_BenchmarkGrowthRate.InnerCode',
			'where' => 'InnerCode IN (SELECT InnerCode FROM MF_FundArchivesAttach WHERE DataCode = '.$type.' and TypeCode=10)
					AND EndDate = (SELECT max(EndDate) EndDate FROM MF_BenchmarkGrowthRate WHERE MF_BenchmarkGrowthRate.InnerCode = '.$InnerCode.' and MF_BenchmarkGrowthRate.EndDate = EndDate)',
			//'limit' => 1000,
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
	
}