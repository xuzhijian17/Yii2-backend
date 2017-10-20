<?php

namespace clientend\models;

use Yii;


class VerifyCode
{
    public $db;
    public $table;

    public function __construct()
    {
        $this->db = Yii::$app->db_local;
        $this->table = "verifica_code";
    }

    /**
     * 插入用户验证码
     * @param $mobile
     * @param $code
     * @return mixed
     */
    public function insertVerifyCode($mobile, $code)
    {
        if (empty($mobile) || $code){
            return false;
        }
        $timeS = time();
        $sql = "insert into {$this->table} set Phone='{$mobile}', Code='{$code}', Used=0,Systime='{$timeS}'";
        $r = $this->db->createCommand($sql)->execute();
        return $r;
    }

    /**
     * 获取用户验证码
     * @param $Phone
     * @return mixed
     */
    public function getUserVerifyByPhone($Phone)
    {
        if (empty($Phone)) {
            return false;
        }
        $sql = "SELECT * FROM {$this->table} WHERE Phone='{$Phone}' AND Used=0 ORDER BY Sysyimr DESC LIMIT 1";
        $user_info = $this->db->createCommand($sql)->queryOne();
        return $user_info;
    }
}
