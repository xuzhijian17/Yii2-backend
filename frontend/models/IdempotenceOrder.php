<?php
namespace frontend\models;
use Yii;
use Exception;

class IdempotenceOrder extends Model
{
    public $bsNo = 0;//来源商户号
    public $field = [];//数据参数['fieldname'=>'fieldvalue'...]非空
    public $tbName = '';//数据表名
    
    const PURCHASE_TYPE = 2; //购买
    const SALE_TYPE = 3; //赎回
    const VALU_TYPE = 1; //定投
    const WITHDRAW_TYPE = 4;//撤单
    const PORTFOLIO_PURCHASE = 5; //组合购买
    const PORTFOLIO_SALE = 6; //组合赎回
    const PORTFOLIO_WITHDRAW = 7; //组合撤单
    
    function __construct($field=[],$bsNo = 0) {
        $this->bsNo = $bsNo;
        $this->field = $field;
        $this->tbName = 'valutrade_plan_'.$bsNo;
    }
    /**
     * 查询
     * @param string $merid 商户号
     * @param string $orderno 订单号
     */
    public static function find($merid,$orderno)
    {
        $db_local = Yii::$app->db_local;
        try {
            $rs = $db_local->createCommand("SELECT * FROM `idempotence_order_{$merid}` WHERE OrderNo = '{$orderno}'")->queryOne();
            return $rs;
        } catch (Exception $e) {
            \Yii::error($e->getMessage(),__METHOD__);
            return FALSE;
        }
    }
    /**
     * 新增订单
     * @param string $merid 商户号
     * @param array $param 数据字段
     * @return boolean true成功/false失败
     */
    public static function insert($merid,$param)
    {
        if (!empty($param) && is_array($param))
        {
            $db_local = Yii::$app->db_local;
            $fieldStr = $valueStr = '';
            foreach ($param as $key => $value) {
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
            $sql = "INSERT INTO `idempotence_order_{$merid}` ({$fieldStr}) VALUES ({$valueStr})";
            try {
                $rs = $db_local->createCommand($sql)->execute();
                if ($rs >0)
                {
                    return true;
                }else {
                    \Yii::error("sql未成功执行:{$sql}",__METHOD__);
                    return false;
                }
            } catch (Exception $e) {
                \Yii::error("操作异常:{$e}-sql:{$sql}",__METHOD__);
            }
        }else {
            \Yii::error('参数不正确:'.json_encode($param),__METHOD__);
            return false;
        }
    }
}