<?php
namespace frontend\models;
use Yii;
class SecuMain extends Model
{
	public static $tableName = 'SecuMain';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'InnerCode = '.$InnerCode
		];
		$data = SecuMain::_find($param);
		return $data;
	}
}