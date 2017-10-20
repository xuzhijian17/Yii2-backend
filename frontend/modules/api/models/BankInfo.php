<?php
namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\lib\CommFun;

class BankInfo extends ActiveRecord
{
    const BANKINFO_KEY = 'bank_info'; //银行列表缓存key
    
	public static function tableName()
    {
        return 'bank_info';
    }

    /**
     * 获取银行限额列表
     * @return array|mixed ['银行编号'=>['BankSerial'=>'银行编号','BankName'=>'银行名称','OnceLimit'=>'单次限额',
     * 'DayLimit'=>'单日限额','MonthLimit'=>'单月限额','Icon'=>'图标地址'],...] /null
     */
    public static function getBankQuotaList()
    {
        $redis = Yii::$app->redis;
        $bankinfo = $redis->get(self::BANKINFO_KEY);
        if (empty($bankinfo))
        {
            $db_local = Yii::$app->db_local;
            $sql = 'SELECT * FROM '.self::tableName();
            $list = $db_local->createCommand($sql)->queryAll();
            $bankArray = array_reduce($list,function(&$bankArray,$v){
                $bankArray[$v['BankSerial']] = array_change_key_case($v,CASE_LOWER);
                return $bankArray;
            });
            if (!empty($bankArray))
            {
                $redis->set(self::BANKINFO_KEY,json_encode($bankArray));
                return $bankArray;
            }
        }else {
            return json_decode($bankinfo,true);
        }
    }

    /**
     * 获取银行限额详情(单个银行)
     * @param string $BankSerial //银行编号
     * @return array ['BankSerial'=>'银行编号','BankName'=>'银行名称','OnceLimit'=>'单次限额',
     * 'DayLimit'=>'单日限额','MonthLimit'=>'单月限额','Icon'=>'图标地址'] /null
     */
    public static function getBankQuotaInfo($BankSerial)
    {
        $bankInfo = self::getBankQuotaList();
        return empty($bankInfo[$BankSerial])?null:$bankInfo[$BankSerial];
    }
}
