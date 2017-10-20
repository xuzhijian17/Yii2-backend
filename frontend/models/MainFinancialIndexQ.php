<?php
namespace frontend\models;
use Yii;
//基金管理人概况表
class MainFinancialIndexQ extends Model
{
	public static $tableName = 'MF_MainFinancialIndexQ';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'EndDate desc'
		];
		$data = MainFinancialIndexQ::_find($param);
		return $data;
	}
}