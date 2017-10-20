<?php
namespace frontend\models;
use Yii;
class FundArchivesAttach extends Model
{
	public static $tableName = 'MF_FundArchivesAttach';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'InnerCode = '.$InnerCode." and TypeCode=10"
		];
		$data = FundArchivesAttach::_find($param);
		return $data;
	}
}