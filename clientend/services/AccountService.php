<?php
namespace clientend\services;
use clientend\lib\ClientCommFun;
use clientend\lib\SendSMS;
use clientend\models\ClientUser;
use clientend\models\VerifyCode;
use clientend\services\BaseService;
use Yii;


class AccountService extends BaseService
{
    /*
     * 构造方法
     * @param int $uid 用户id
     * @param int $instId 商户号
     */
    public function __construct ( $instId=0, $uid=0)
    {
        parent::__construct ( $instId, $uid );
    }


    /**
     * 用户登陆
     * @param $mobile 用户注册手机号
     * @param $password 用户密码
     * @return array
     */
    public function login($mobile, $password)
    {
        if (empty($mobile) || empty($password)) {
            return ClientCommFun::clientHandleCode(-3);
        }
        $user = new ClientUser();
        $user_info = $user->getUserInfoByRegPhone($mobile);
        if (empty(($user_info))) {
            return ClientCommFun::clientHandleCode(-14);
        }
        $password = $this->getPasswordEncrypt($password);
        if ($password !== $user_info['LoginPass']) {
            return ClientCommFun::clientHandleCode(-8);
        }
        $data = ClientCommFun::clientHandleCode(0);
        unset($user_info['Pass'], $user_info['LoginPass']);
        $data['data']['user'] = $user_info;
        $this->uid = $user_info['id'];
        return $data;
    }

    /**
     * 用户注册
     * @param $mobile 手机号
     * @param $password 登陆密码
     * @param $verifyCode  验证码
     * @return array|mixed
     */
    public function register($mobile, $password, $verifyCode)
    {
        if (empty($mobile) || empty($password) || empty($verifyCode)) {
            return ClientCommFun::clientHandleCode(-3);
        }
        $user = new ClientUser();
        $user_info = $user->getUserInfoByRegPhone($mobile);
        if (!empty(($user_info))) {
            return ClientCommFun::clientHandleCode(-14);
        }
        $password = $this->getPasswordEncrypt($password);
        $verifyObj = new VerifyCode();
        $verify = $verifyObj->getUserVerifyByPhone($mobile);
        if (empty($verify)) {
            return ClientCommFun::clientHandleCode(-15);
        }
        if ($verify['Code'] != $verifyCode) {
            return ClientCommFun::clientHandleCode(-16);
        }
        if (time() > $verify['Systime'] + 60) { //验证码有效期60秒
            return ClientCommFun::clientHandleCode(-17);
        }
        $r = $user->addRegisterUser($mobile, $password, $this->instId);
        if ($r) {
            $data = ClientCommFun::clientHandleCode(0);
            $user_info = $user->getUserInfoByRegPhone($mobile);
            unset($user_info['Pass'], $user_info['LoginPass']);
            $data['data']['user'] = $user_info;
            $this->uid = $user_info['id'];
        } else {
            $data = ClientCommFun::clientHandleCode(-208);
        }
        return $data;
    }


    /**
     * 用户找回密码
     * @param $mobile 手机号
     * @param $password 登陆密码
     * @param $verifyCode  验证码
     * @return array|mixed
     */
    public function ForgetPassword($mobile, $password, $verifyCode)
    {
        if (empty($mobile) || empty($password) || empty($verifyCode)) {
            return ClientCommFun::clientHandleCode(-3);
        }
        $user = new ClientUser();
        $user_info = $user->getUserInfoByRegPhone($mobile);
        if (empty(($user_info))) {
            return ClientCommFun::clientHandleCode(-14);
        }

        $verifyObj = new VerifyCode();
        $verify = $verifyObj->getUserVerifyByPhone($mobile);
        if (empty($verify)) {
            return ClientCommFun::clientHandleCode(-15);
        }
        if ($verify['Code'] != $verifyCode) {
            return ClientCommFun::clientHandleCode(-16);
        }
        if (time() > $verify['Systime'] + 60) { //验证码有效期60秒
            return ClientCommFun::clientHandleCode(-17);
        }

        $password = $this->getPasswordEncrypt($password);
        $r = $user->editUserPassword($mobile, $password, $this->instId);
        if ($r) {
            $data = ClientCommFun::clientHandleCode(0);
        } else {
            $data = ClientCommFun::clientHandleCode(-208);
        }
        return $data;
    }

    /**
     * 发送验证码
     * @param $mobile
     * @param int $type
     * @return mixed
     */
    public function sendUserVerify($mobile, $type=1)
    {
        $sms = new SendSMS();
        $rand_num = "0123456789";
        $rand_num = substr(str_shuffle($rand_num), 0, 6);
        $reg = '您申请注册的验证码为：_num_,【汇成世纪】';
        $find_pass = '您申请找回密码的验证码为：_num_,【汇成世纪】';
        if ($type == 1) {
            $content = str_replace('_num_', $rand_num, $reg);
        } else {
            $content = str_replace('_num_', $rand_num, $find_pass);
        }
        $r = $sms->send($mobile, $content);
        if ($r['code'] == 0) {
            $verifyObj = new VerifyCode();
            $verifyObj->insertVerifyCode($mobile, $rand_num);
        }
        return $r;
    }

    /* 把用户明文加密成密文
     * @param $password
     * @return mixed
     */
    public function getPasswordEncrypt($password)
    {
        return md5(md5(trim($password)));
    }
}