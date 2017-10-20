<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * 用户基金持仓收益表
 */
class PositionProfitloss extends Model
{
	public $bsNo = 0;//来源商户号
    public $field = [];//数据参数['fieldname'=>'fieldvalue'...]非空
    public $tbName = '';//数据表名
	
    function __construct($field=[],$bsNo = 0) {
        $this->bsNo = $bsNo;
        $this->field = $field;
        $this->tbName = 'position_profitloss_'.$bsNo;
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
    public function query($where,$rs='one',$order='id desc',$limit='1')
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM {$this->tbName} WHERE ".$where." order by ".$order." limit ".$limit;
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

    /*
     * 汇总出用户每日收益
     * @param $uid
     * @return mixed
     */
    public function getUserEveryDayProfit($uid, $start=0, $limit=30)
    {
        $sql = "SELECT SUM(ProfitLoss) AS dayprofit,TradeDay FROM position_profitloss_".$this->bsNo;
        $sql .= " WHERE Uid='{$uid}' GROUP BY TradeDay ORDER BY TradeDay DESC LIMIT {$start},{$limit}";
        $db = Yii::$app->db_local;
        $list = $db->createCommand($sql)->queryAll();
        return $list;
    }

    /**
     * 汇总用户每日收益总数量
     * @param $uid
     * @return int
     */
    public function getUserEveryDayProfitCount($uid)
    {
        $sql = "SELECT COUNT(DISTINCT(TradeDay)) AS count FROM position_profitloss_".$this->bsNo;
        $sql .= " WHERE Uid='{$uid}'";
        $db = Yii::$app->db_local;
        $r = $db->createCommand($sql)->queryOne();
        return !empty($r) ? $r['count'] : 0;
    }

    /*
     * @param $uid
     * @param $fundcode
     * @return mixed
     */
    public function getUserFundCodeEveryDayProfit($uid, $fundcode, $start=0, $limit=30)
    {
        $sql = "SELECT ProfitLoss AS dayprofit,FundCode,TradeDay FROM position_profitloss_".$this->bsNo;
        $sql .= " WHERE Uid='{$uid}' AND FundCode='{$fundcode}' ORDER BY TradeDay DESC LIMIT {$start},{$limit}";
        $db = Yii::$app->db_local;
        $list = $db->createCommand($sql)->queryAll();
        return $list;
    }

    /**
     * 每日收益总记录数
     * @param $uid
     * @param $fundcode
     * @return int
     */
    public function getUserFundCodeEveryDayProfitCount($uid, $fundcode)
    {
        $sql = "SELECT COUNT(*) AS count FROM position_profitloss_".$this->bsNo;
        $sql .= " WHERE Uid='{$uid}' AND FundCode='{$fundcode}'";
        $db = Yii::$app->db_local;
        $r = $db->createCommand($sql)->queryOne();
        return !empty($r) ? $r['count'] : 0;
    }
}
