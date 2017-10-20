<?php
namespace frontend\models;
use Yii;
//基金经理
class FundManager extends Model
{
	public static $tableName = 'MF_FundManager';
	public static function getManagerFund($args, $InnerCode){
		$args = unserialize(base64_decode(str_pad(strtr($args, '-_', '+/'), strlen($args) % 4, '=', STR_PAD_RIGHT)));
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and Name = "'.$args['Name'].'" and Gender = '.$args['Gender'].'
						and PostName = 1',
			'order' => 'AccessionDate desc',
		];
		$manager = self::_find($param);
		return $manager;
	}
	# 基金经理所管理的全部基金
	public static function getManagerFundList($args){
		$args = unserialize(base64_decode(str_pad(strtr($args, '-_', '+/'), strlen($args) % 4, '=', STR_PAD_RIGHT)));
		$param = [
			'field' => 'AccessionDate, DimissionDate, Performance, SecuMain.ChiNameAbbr
',
			'join' => 'left join SecuMain on SecuMain.InnerCode = MF_FundManager.InnerCode',
			'where' => 'Name = "'.$args['Name'].'" and Gender = '.$args['Gender'].'
						and PostName = 1',
			'order' => 'AccessionDate desc',
		];
		$manager = self::_query($param);
		return $manager;
	}
	
	public static function query($InnerCode){
		$param = [
			'where' => 'InnerCode = '.$InnerCode.' and PostName = 1 and DimissionDate is null',
			'order' => 'AccessionDate desc',
		];
		$manager = FundManager::_query($param);
		return $manager;
	}
}