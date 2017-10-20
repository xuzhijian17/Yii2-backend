<?php
namespace clientend\controllers;

use clientend\services\AccountService;
use Yii;

/**
 * 账户/个人设置相关controller
 */
class AccountController extends BaseController
{
    //用户登陆视图
    public function ActionLogin_v()
    {
        return $this->renderPartial("login");
    }

    //用户登陆提交
    public function ActionLogin()
    {
        $mobile = $this->post('mobile');
        $password  = $this->post('password');
        $instid    = $this->post('instid');

        $accountService = new AccountService($instid);
        $login = $accountService->login($mobile, $password);
        if ($login['code'] == 0) { //登陆成功，并记录session
            Yii::$app->session['user_login'] = $login['data']['user'];
        } else {
            $this->errPage($login['message']); //登陆失败
        }
    }

    //用户注册视图
    public function ActionRegister_v()
    {
        return $this->renderPartial("register");
    }

    //用户注册提交
    public function ActionRegister()
    {
        $mobile = $this->post('mobile'); //注册手机号
        $password  = $this->post('password'); //登陆密码
        $instid    = $this->post('instid'); //渠道商户
        $verifyCode = $this->post("verifycode"); //验证码

        $accountService = new AccountService($instid);
        $login = $accountService->register($mobile, $password, $verifyCode);
        if ($login['code'] == 0) {
            Yii::$app->session['user_login'] = $login['data']['user'];
        } else {
            $this->errPage($login['message']);
        }
    }

    //用户注册视图
    public function ActionForget_v()
    {
        return $this->renderPartial("forget");
    }

    //用户忘记密码找回
    public function ActionForgetPassword()
    {
        $mobile = $this->post('mobile'); //注册手机号
        $password  = $this->post('password'); //新登陆密码
        $instid    = $this->post('instid'); //渠道商户
        $verifyCode = $this->post("verifycode"); //验证码

        $accountService = new AccountService($instid);
        $login = $accountService->ForgetPassword($mobile, $password, $verifyCode);
        if ($login['code'] == 0) {
            //找回成功，跳转
        } else {
            $this->errPage($login['message']);
        }
    }


    //发送验证码
    public function ActionSendcode()
    {
        $mobile = $this->post('mobile'); //注册手机号
        $instid    = $this->post('instid'); //渠道商户
        $type    = $this->post('type'); //短信类型

        $accountService = new AccountService($instid);
        $r = $accountService->sendUserVerify($mobile, $type);
        exit(json_encode($r)); //返回json在h5页面ajax调用
    }

}