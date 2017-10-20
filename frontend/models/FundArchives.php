<?php
namespace frontend\models;
use Yii;
//基金概况
class FundArchives extends Model
{
	public static $tableName = 'MF_FundArchives';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'InnerCode = '.$InnerCode
		];
		$data = FundArchives::_find($param);
		return $data;
	}
}