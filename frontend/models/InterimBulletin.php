<?php
namespace frontend\models;
use Yii;
//基金临时公告
class InterimBulletin extends Model
{
	public static $tableName = 'MF_InterimBulletin';
	public static function getInterimBulletinList($InnerCode, $page = 1)
	{
		$size = 10;
		$param = [
			'field' => 'MF_InterimBulletin.ID, InfoTitle, BulletinDate',
			'where' => 'ID in (select ID FROM MF_InterimBulletin_SE where CODE = '.$InnerCode.')',
			'order' => 'BulletinDate desc',
			'limit' => (($page - 1) * $size).', '.$size,
		];
		return self::_query($param);
	}
}