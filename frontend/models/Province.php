<?php
namespace frontend\models;
use yii\db\ActiveRecord;
use Yii;
class Province extends ActiveRecord
{
	public static function tableName()
    {
        return 'province';
    }
	
	public static function getProvinceList()
    {
		$data = [];
		$command = Yii::$app->db->createCommand("select id, name, py_upper(name) py from province order by py asc");
		$province = $command->queryAll();
		foreach($province as $val){
			$data[$val['py']][] = $val;
		}
		return $data;
    }
	
	public static function getCityList($id)
    {
		$data = [];
		$command = Yii::$app->db->createCommand("select id, name, py_upper(name) py from city where pid={$id} order by py asc");
		$province = $command->queryAll();
		foreach($province as $val){
			$data[$val['py']][] = $val;
		}
		return $data;
    }
	
	public static function getBankList($id, $bankno)
    {
		$data = [];
		$command = Yii::$app->db->createCommand("select * from bank where cid={$id} and bankno='".$bankno."'");
		$data = $command->queryAll();
		return $data;
    }
}