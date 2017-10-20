<?php
namespace frontend\modules\api\models;
use Yii;
use yii\base\Model;

/**
 * model 基类，主要包含了平时常用的insert,update,query；
 * 在子类中继承，否则每做一个model就要copy一份
 *
 * Class BaseModel
 * @package frontend\models
 */
class BaseModel extends Model
{

    public $bsNo = 0;        //来源商户号
    public $field = [];     //数据参数['fieldname'=>'fieldvalue'...]非空
    public $tbName = '';    //数据表名
    public $db;

    public function __construct($bsNo = 0, $field=[]) {
        $this->bsNo = $bsNo;
        $this->field = $field;
        $this->db = Yii::$app->db_local;
    }
    
    /**
     * 插入数据
     * @return mixed 成功:最新id/false失败
     */
    public function insert()
    {
        if (!empty($this->field) && is_array($this->field))
        {
            $fieldStr = $valueStr = '';
            foreach ($this->field as $key => $value) {
                if ($value === null)
                {
                    continue;
                }else {
                    $fieldStr .='`'.$key.'`,';
                    $valueStr .= "'{$value}',";
                }
            }
            $fieldStr = rtrim($fieldStr,',');
            $valueStr = rtrim($valueStr,',');

            $sql = /** @lang text */ "INSERT INTO {$this->tbName} ({$fieldStr}) VALUES ({$valueStr})";
            $rs = $this->db->createCommand($sql)->execute();
            if ($rs >0) {
                return $this->db->getLastInsertID();
            }else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
    }

    /**
     * 查询方法
     * @param string $where sql语句where部分
     * @param string $rs 'one'一条;'all'多条
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function query($where, $rs='one', $order='', $limit='')
    {
        $sql =  /** @lang text */ "SELECT * FROM {$this->tbName} WHERE ".$where.$order.$limit;
        $command = $this->db->createCommand($sql)->query();
        if ($rs == 'all'){
            $result = [];
            while ($row = $command->read()) {
                $result[$row['id']] = array_change_key_case($row, CASE_LOWER);
            }
        }else {
            $result = $command->read();
        }
        return $result;
    }

    /*
     * 
     * @param string $where sql语句where部分
     * @param string $rs
     * @param string $order
     * @param string $limit
     * @return unknown
     */
    public function update($where)
    {
        if (!empty($this->field) && is_array($this->field)) {
            $fieldStr = $valueStr = '';
            foreach ($this->field as $key => $value) {
                $fieldStr .='`'.$key.'` = \''.$value.'\',';
            }
            $fieldStr = rtrim($fieldStr,',');
            $sql = /** @lang text */ "UPDATE {$this->tbName} SET {$fieldStr} WHERE {$where} ";
            $rs = $this->db->createCommand($sql)->execute();
            if ($rs >0) {
                return true;
            } else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
    }

    /**
     * 根据条件查询表的总数量
     * @param $where
     * @return int
     */
    public function getRecordsCount($where="")
    {
        $sql = /** @lang text */ "SELECT count(*) AS count FROM {$this->tbName} ";
        if (!empty($where)) {
            $sql .= "WHERE {$where}";
        }
        $one = $this->db->createCommand($sql)->queryOne();
        if (empty($one)) {
            return 0;
        }
        return $one['count'];
    }
}