<?php
namespace frontend\models;
use Yii;
class FundRating extends Model
{
	public static $tableName = 'MF_FundRating';
	public static function find($InnerCode){
		$param = [
			'cache' => $InnerCode,
			'where' => 'FundInnerCode = '.$InnerCode,
			'order' => 'EndDate desc',
			'limit' => '1'
		];
		$data = FundRating::_find($param);
		return $data;
	}
}