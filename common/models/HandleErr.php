<?php
namespace common\models;
use Yii;
use common\lib\CommFun;

/**
 *处理错误记录model
 */
class HandleErr
{
    /**
     * 插入数据
     * @param array ['OidType'=>'目标表','Oid'=>'目标表主键','Instid'=>'商户号','Info'=>'信息摘要','SystemTime'=>'系统时间']
     */
    public static function insert($param)
    {
        $db_local = Yii::$app->db_local;
        $param['Info'] = mb_substr($param['Info'],0,100,'utf-8');
        $arr_field = CommFun::JoinInsertStr($param);
        $sql = "INSERT INTO `handle_err` ({$arr_field['fields']}) VALUES ({$arr_field['values']})";
        try {
            $db_local->createCommand($sql)->execute();
        } catch (Exception $e) {
            Yii::error("sql:{$sql} --{$e}",__METHOD__);
        }
    }
}