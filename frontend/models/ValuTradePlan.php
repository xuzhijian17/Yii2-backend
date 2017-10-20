<?php
namespace frontend\models;
use Yii;
/**
 * 操作model类涉及valutrade_plan_x表(x为商户号，0表示默认从自己渠道来源交易记录)
 */
class ValuTradePlan extends Model
{
    public $bsNo = 0;//来源商户号
    public $field = [];//数据参数['fieldname'=>'fieldvalue'...]非空
    public $tbName = '';//数据表名
    
    function __construct($field=[],$bsNo = 0) {
        $this->bsNo = $bsNo;
        $this->field = $field;
        $this->tbName = 'valutrade_plan_'.$bsNo;
    }
    /**
     * 插入数据
     * @return mixed 成功:最新id/false失败
     */
    public function insert()
    {
        if (!empty($this->field) && is_array($this->field))
        {
            $db_local = Yii::$app->db_local;
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
            $sql = "INSERT INTO {$this->tbName} ({$fieldStr}) VALUES ({$valueStr})";
            $rs = $db_local->createCommand($sql)->execute();
            if ($rs >0)
            {
                return $db_local->getLastInsertID();
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
     * 查询
     * @param string $where sql语句where部分
     * @param string $rs 'one'一条;'all'多条
     * @return array
     */
    public function query($where,$rs='one',$order='',$limit='')
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM {$this->tbName} WHERE ".$where.$order.$limit;
        if ($rs == 'all'){
            $arr = $db_local->createCommand($sql)->queryAll();
        }else {
            $arr = $db_local->createCommand($sql)->queryOne();
        }
        return $arr;
    }
    /**
     *
     * @param string $where sql语句where部分
     * @param string $rs
     * @param string $order
     * @param string $limit
     * @return unknown
     */
    public function update($where)
    {
        $db_local = Yii::$app->db_local;
        if (!empty($this->field) && is_array($this->field))
        {
            $db_local = Yii::$app->db_local;
            $fieldStr = $valueStr = '';
            foreach ($this->field as $key => $value) {
                $fieldStr .='`'.$key.'` = \''.$value.'\',';
            }
            $fieldStr = rtrim($fieldStr,',');
            $sql = "UPDATE {$this->tbName} SET {$fieldStr} WHERE {$where} ";
            $rs = $db_local->createCommand($sql)->execute();
            if ($rs >0)
            {
                return true;
            }else {
                \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                return false;
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($this->field),__METHOD__);
            return false;
        }
    }
}