<?php

namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "{{%post}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $insertTime
 */
class FundInfo extends ActiveRecord
{
    
	/**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * db 链接
     */
    public static function getDb($db_name='')
    {
        return $db_name ? Yii::$app->$db_name : Yii::$app->db;
    }
	
	public static function getFundInfo($page='1',$pagesize='15',$orderby='FundCode',$stwhere=''){
		
		$start = ($page - 1) * $pagesize;
		
		$sql = "select FundCode,FundName,PernetValue,DailyProfit,RRInSingleMonth,RRInSelectedMonth,RRInThreeMonth,RRInSixMonth,RRInSingleYear,RRSinceThisYear from fund_info";
		
		if(!empty($stwhere))  $sql = $sql." where ".$stwhere;
		
		$sql = $sql." order by ".$orderby." limit ".$start.",".$pagesize;
		
		//echo $sql;
		$command = Yii::$app->db_local->createCommand($sql);
		$rs = $command->queryAll();
        return $rs;
	}
	
	public static function getFundCount($stwhere=''){
		
		$sql = "select count(*) as cnt from fund_info";
		
		if(!empty($stwhere)) $sql = $sql." where ".$stwhere;
		
		$command = Yii::$app->db_local->createCommand($sql);
		
		$rs = $command->queryOne();
		
		return $rs;
		
	}
}
