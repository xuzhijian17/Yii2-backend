<?php
namespace frontend\models;
use Yii;
class SystemConst extends Model
{
	public static $tableName = 'CT_SystemConst';
	//基金类型
	public static function getFundType()
	{
		$data = [];
		$param = [
			'cache' => 1249,
			'field' => 'MS, DM',
			'where' => 'LB = 1249',
		];
		$FundTypeCode = SystemConst::query($param);
		if($FundTypeCode){
			foreach($FundTypeCode as $val){
				$data[$val['DM']] = $val;
			}
		}
		return $data;
	}
	//基金星级
	public static function getStarRank()
	{
		$data = [];
		$param = [
			'cache' => 1364,
			'field' => 'MS, DM',
			'where' => 'LB = 1364',
		];
		$FundTypeCode = SystemConst::query($param);
		if($FundTypeCode){
			foreach($FundTypeCode as $val){
				$data[$val['DM']] = $val;
			}
		}
		return $data;
	}
	
	//基金风险评级
	public static function getRiskEvaluation()
	{
		$data = [];
		$param = [
			'cache' => 1365,
			'field' => 'MS, DM',
			'where' => 'LB = 1365',
		];
		$FundTypeCode = SystemConst::query($param);
		if($FundTypeCode){
			foreach($FundTypeCode as $val){
				$data[$val['DM']] = $val;
			}
		}
		return $data;
	}
}