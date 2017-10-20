<?php
namespace console\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;


class FundHzfCommands extends ActiveRecord
{
	public static function tableName()
    {
        return 'fund_hzf_commands';
    }
	/**
	 * 此方法将在上线前删除
	 */
	public static function getQueryInfo($file='queryinfo')
	{
		$db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM ".self::tableName()." where CommandName='".$file."' and CommandStatus=0 order by id asc";
        $arr = $db_local->createCommand($sql)->queryAll();
        
        return $arr;
	}
	/**
	 * 此方法将在上线前删除
	 */
	public static function updateHzfStatus($file='queryinfo')
	{
		 $db_local = Yii::$app->db_local;
         $sql = "update ".self::tableName()." set CommandStatus = 1,LastDealTime='".date('Y-m-d H:i:s')."' where CommandName='".$file."'";
         $arr = $db_local->createCommand($sql)->execute();
         
         return $arr;
	}
	
// 	public static function clearZero()
// 	{
// 		$db_local = Yii::$app->db_local;
		
// 		$sql = "update ".self::tableName()." set CommandStatus = 0 where CommandName='queryinfo'";
		
// 		$rst = $db_local->createCommand($sql)->execute();
		
// 		if ($rst >0)
// 		{
// 			return true;
// 		}else {
// 			\Yii::error("sql未成功执行:{$sql}",__METHOD__);
// 			return false;
// 		}
// 	}
}
