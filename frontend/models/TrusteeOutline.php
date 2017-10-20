<?php
namespace frontend\models;
use Yii;
//基金托管人代码
class TrusteeOutline extends Model
{
	public static $tableName = 'MF_TrusteeOutline';
	public static function find($TrusteeCode){
		$param = [
			'cache' => $TrusteeCode,
			'where' => 'TrusteeCode = '.$TrusteeCode,
		];
		$data = TrusteeOutline::_find($param);
		return $data;
	}
}