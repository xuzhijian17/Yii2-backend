<?php
namespace frontend\models;
use Yii;
//基金分红
class Dividend extends Model
{
	public static $tableName = 'MF_Dividend';
	public static function getDividendList($InnerCode, $page = 1){
		$size = 10;
		$param = [
			'where' => 'InnerCode = '.$InnerCode,
			'order' => 'EndDate desc',
			'limit' => (($page - 1) * $size).', '.$size,
		];
		return self::_query($param);
	}
}