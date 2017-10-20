<?php
namespace frontend\modules\api\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\lib\CommFun;

class UserBank extends ActiveRecord
{
	public static function tableName()
    {
        return 'user_bank';
    }

    /**
     * 通过用户银行卡获取用户银行卡信息
     * @param $BankAcco 银行卡号
     * @return mixed
     */
    public static function getUserBankByBankAcco($BankAcco)
    {
        $db_local = Yii::$app->db_local;
        $tablename = self::tableName();
        $sql = "SELECT * FROM {$tablename} WHERE BankAcco='{$BankAcco}'";
        $user_info = $db_local->createCommand($sql)->queryOne();
        return $user_info;
    }

    /**
     * 通过用户银行卡获取用户银行卡信息
     * @param $BankAcco 银行卡号
     * @return mixed
     */
    public static function getUserBankByUid($uid, $return_key="TradeAcco")
    {
        $db_local = Yii::$app->db_local;
        $tablename = self::tableName();
        $sql = "SELECT * FROM {$tablename} WHERE Uid='{$uid}'";

        $bank_list_temp = $db_local->createCommand($sql)->queryAll();

        $bank_list = [];
        foreach ($bank_list_temp as $key=>$val) {
            $bank_list[$val[$return_key]] = $val;
        }
        return $bank_list;
    }
}
