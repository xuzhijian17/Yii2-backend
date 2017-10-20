<?php
namespace frontend\models;
use Yii;
/**
 * @author zk
 *操作model类涉及trade_order_x表(x为商户号，0表示默认从app及网站来源交易记录)
 */
class TradeOrder extends Model
{
    const TRADE_TYPE_BUY = 0;   //买入
    const TRADE_TYPE_SALE = 1;   //卖出
    const TRADE_TYPE_WITHDRAW = 2;   //撤单
    const TRADE_TYPE_BONUS = 3;   //分红
    
    const TS_INVALID = -2; //已失效
    const TS_NOPAY    = -1; //未付款
    const TS_CONFIRM_FAIL = 0; //确认失败
    const TS_CONFIRM_SUCCESS = 1; //确认成功
    const TS_PART_CONFIRM_SUCCESS = 2; //部分确认成功
    const TS_RT_CONFIRM_SUCCESS = 3; //实时确认成功
    const TS_WITHDRAW = 4; //撤单
    const TS_ACTION_SUCCESS = 5; //行为确认
    const TS_NOHANDLE = 9; //未处理

    public $bsNo = 0;//来源商户号
    public $field = [];//数据参数['fieldname'=>'fieldvalue'...]非空
    public $tbName = '';//数据表名

    function __construct($field=[],$bsNo = 0) {
        $this->bsNo = $bsNo;
        $this->field = $field;
        $this->tbName = 'trade_order_'.$bsNo;
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
	
	public function getFundTotal($uid, $FundCode)
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM {$this->tbName} WHERE Uid={$uid} and FundCode = '{$FundCode}' and TradeType in(0, 2, 3) and TradeStatus=2";
        $arr = $db_local->createCommand($sql)->queryAll();
        return $arr;
    }
	
	public function getOrderList($uid, $FundCode, $page = 1)
    {
        $db_local = Yii::$app->db_local;
		$limit = ($page-1) * 10;
        $sql = "SELECT * FROM {$this->tbName} WHERE Uid={$uid} and FundCode = '{$FundCode}' and TradeType in(0, 1, 2) order by SysTime desc limit {$limit}, 10";
        $arr = $db_local->createCommand($sql)->queryAll();
        return $arr;
    }

    /**
     * 根据条件查询订单的总数量
     * @param $where
     * @return int
     */
    public function getTradeOrderCount($where)
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT count(*) AS count FROM {$this->tbName} WHERE ".$where;
        $one = $db_local->createCommand($sql)->queryOne();
        if (empty($one)) {
            return 0;
        }
        return $one['count'];
    }

    /**
     * 通过组合交易id查出来所有组合详细的交易记录并以portfTradeId为key返回一个三维数组
     * @param $uid
     * @param $portfolioTradeIds
     * @return array
     */
    public function getPortfolioTradeOrderGroup($uid, $portfolioTradeIds)
    {
        if (empty($portfolioTradeIds)) {
            return [];
        }
        $db_local = Yii::$app->db_local;
        $sql =  /** @lang text */ "SELECT * FROM {$this->tbName} WHERE Uid='{$uid}' AND PortfTradeId IN ($portfolioTradeIds)";
        $command = $db_local->createCommand($sql)->query();
        $result = [];
        while ($row = $command->read()) {
            $result[$row['PortfTradeId']][] = array_change_key_case($row, CASE_LOWER);
        }
        return $result;
    }
}