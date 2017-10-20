<?php
namespace common\models;
use Yii;
use yii\base\Model;
use common\lib\CommFun;
use Exception;

class TaskDeduct extends Model
{
    public static function tableName()
    {
        return 'task_deduct';
    }
    /**
     * 插入数据
     * @param array $param ['Oid'=>'trade_order表id','Instid'=>'商户号','Uid'=>'用户id',
     * 'ApplySerial'=>'申请编号','TaskTime'=>'任务开始时间']
     */
    public static function insert($param)
    {
        $db_local = Yii::$app->db_local;
        $arr_field = CommFun::JoinInsertStr($param);
        $sql = "INSERT INTO `task_deduct` ({$arr_field['fields']}) VALUES ({$arr_field['values']})";
        try {
            $db_local->createCommand($sql)->execute();
        } catch (Exception $e) {
            Yii::error($e->getMessage(),__METHOD__);
        }
    }
}
