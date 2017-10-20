<?php
namespace frontend\models;
use Yii;
//基金管理人概况表
class InvestAdvisorOutline extends Model
{
	public static $tableName = 'MF_InvestAdvisorOutline';
	public static function find($InvestAdvisorCode){
		$param = [
			'cache' => $InvestAdvisorCode,
			'where' => 'InvestAdvisorCode = '.$InvestAdvisorCode,
		];
		$data = InvestAdvisorOutline::_find($param);
		return $data;
	}
}