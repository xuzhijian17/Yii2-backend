<?php

namespace clientend\models;

use Yii;
use frontend\models\User;

class ClientUser extends User
{
    public $db;
    public $table;

    public function __construct(array $config)
    {
        $this->db = Yii::$app->db_local;
        $this->table = $this->tableName();
        parent::__construct($config);
    }

    /**
     * 通过用户注册手机号获取用户信息
     * @param $RegPhone 注册手机号
     * @return mixed
     */
    public function getUserInfoByRegPhone($RegPhone)
    {
        if (empty($RegPhone)){
            return false;
        }
        $sql = "SELECT * FROM {$this->table} WHERE RegPhone='{$RegPhone}' AND AccountStatus=".self::AS_NORMAL;
        $user_info = $this->db->createCommand($sql)->queryOne();
        return $user_info;
    }

    /**
     * 注册用户
     * @param $mobile 注册手机号
     * @param $loginpass 登陆密码
     * @param $instid 渠道商户
     * @return mixed
     */
    public function addRegisterUser($mobile, $loginpass, $instid)
    {
        if (empty($mobile) || empty($loginpass) || empty($instid)) {
            return false;
        }
        $dateS = date("Y-m-d H:i:s");
        $sql = "insert into {$this->table} set RegPhone='{$mobile}',LoginPass='{$loginpass}', Instid='{$instid}',";
        $sql .= "AccountStatus=".self::AS_NORMAL.",OpenStatus=".self::OS_NOOPEN.",SysTime='{$dateS}'";
        $r = $this->db->createCommand($sql)->execute();
        return $r;
    }

    /**
     * 用户找回登陆密码
     * @param $mobile 注册手机号
     * @param $loginpass 登陆密码
     * @return mixed
     */
    public function editUserPassword($mobile, $loginpass)
    {
        if (empty($mobile) || $loginpass) {
            return false;
        }
        $sql = "update {$this->table} set LoginPass='{$loginpass}' where RegPhone='{$mobile}' limit 1";
        $r = $this->db->createCommand($sql)->execute();
        return $r;
    }
}
