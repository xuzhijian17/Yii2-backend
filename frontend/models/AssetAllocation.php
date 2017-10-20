<?php
namespace frontend\models;
use Yii;
//基金股票组合明细
class AssetAllocation extends Model
{
	public static $tableName = 'MF_AssetAllocation';
	//基金股票组合明细
	public static function getAssetAllocationList($InnerCode){
		$param = [
			'field' => 'max(ReportDate) ReportDate',
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'ReportDate desc',
		];
		$data = AssetAllocation::_find($param); //查出最新时间
		$maxDate = $data['ReportDate'];
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and ReportDate = "'.$maxDate.'"
						and AssetTypeCode in(10020, 10010, 10090, 1000202)',
		];
		$data = AssetAllocation::_query($param);
		return $data;
	}
}