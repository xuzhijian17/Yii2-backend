<?php
namespace common\models;
use Yii;
use yii\base\Model;


class FundInfo extends Model
{
	public static function tableName()
	{
		return 'fund_info';
	}

	public static function getFundInfoCount($where)
	{
		$db_local = Yii::$app->db_local;
		$table   = self::tableName();
		$sql = " SELECT COUNT(*) AS count FROM {$table} ";
		if (!empty($where)) {
			$sql .= " WHERE {$where}";
		}
		$arr = $db_local->createCommand($sql)->queryOne();
		return !empty($arr) ? $arr['count'] : 0;
	}

	public static function getFundInfoList($where, $rs="", $order="", $limit="")
	{
		$db_local = Yii::$app->db_local;
		$table   = self::tableName();
		$sql = " SELECT * FROM {$table} ";
		if (!empty($where)) {
			$sql .= "WHERE {$where}";
		}
		if (!empty($order)) {
			$sql .= " order by ".$order;
		}
		if (!empty($limit)) {
			$sql .= " limit ".$limit;
		}
		if ($rs == 'all') {
			$arr = $db_local->createCommand($sql)->queryAll();
		} else {
			$arr = $db_local->createCommand($sql)->queryOne();
		}
		return $arr;
	}
}