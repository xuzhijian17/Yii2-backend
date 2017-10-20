<?php

namespace frontend\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\lib\CommFun;

class User extends ActiveRecord
{
    //OpenStatus 开户状态
    const OS_DEL = -2; //已删除
    const OS_CANCEL = -1; //已销户
    const OS_NOOPEN = 0; //未开户
    const OS_OPEN = 1; //已开户

    //AccountStatus账户状态
    const AS_NORMAL = 0; //正常
    const AS_FREEZE = -1; //冻结

    
	public static function tableName()
    {
        return 'user';
    }

    /**
     * 通过用户id获取用户信息
     * @param $uid 用户id
     * @return mixed
     */
    public function getUserInfoByUid($uid)
    {
        $db_local = Yii::$app->db_local;
        $sql = "SELECT * FROM {$this->tableName()} WHERE id='{$uid}' AND OpenStatus=1";
        $user_info = $db_local->createCommand($sql)->queryOne();
        return $user_info;
    }
}
