<?php
namespace fundzone\controllers;

use fundzone\models\CompanyUser;
use Yii;
use fundzone\models\UserLogin;

/**
 * 企业账户
 * Class CompanyController
 * @package fundzone\controllers
 */

class CompanyController extends BaseController
{
    public function init()
    {
        parent::init();
    }

    //交易记录列表
    public function actionLogin()
    {
        $session = Yii::$app->session;
        if (!empty($session['user_login'])) { //如果用户登陆，则跳转到首页或账户中心
            return $this->redirect("/");
        }
        $referrer = $this->request->getReferrer();
        $referrer_url = "/";
        if (!empty($referrer)) {
            $selfHost = parse_url($this->request->getHostInfo());
            $refer_host = parse_url($referrer);
            if (($selfHost['host'] != $refer_host['host']) || $refer_host['path'] == '/company/login/') {
                $referrer_url = "/";
            } else {
                $referrer_url = $referrer;
            }
        }
        $session['login_token'] = md5(time()."1!@#$");
        $session['token_time'] = time();
        return $this->render('login', ['login_token'=>$session['login_token'], 'refer_url'=>$referrer_url]);
    }

    //登陆校验
    public function actionLoginverify()
    {
        $session = Yii::$app->session;
        $return = ['code'=>-1, 'message'=>'请求有误!', 'type'=>0];
        if (empty($session['login_token']) || empty($session['login_token']) || !$this->isAjax()) {
            exit(json_encode($return));
        }
        $tradeacco = $this->post('user_name');
        $password = $this->post('password');
        $login_token = $this->post('login_token');

        if (empty($tradeacco) || empty($password) || empty($login_token)) {
            $return['message'] = '必填项不能为空!';
            exit(json_encode($return));
        }
        if ($login_token != $session['login_token']) {
            $return['message'] = '非法请求!';
            exit(json_encode($return));
        }

        if (time() > $session['token_time']+120 ) { //登陆时间超过120秒，就登陆超时
            $return['message'] = '非法请求!';
            exit(json_encode($return));
        }

        $companyUser = new CompanyUser();
        $attach = $companyUser->getCompanyAttachByTradeacco($tradeacco);
        if (empty($attach)) {
            $return['message'] = '该账户不存在!';
            exit(json_encode($return));
        }
        $user_info = $companyUser->getUserInfoByUid($attach['Uid']);
        if (empty($attach)) {
            $return['message'] = '该账户不存在!';
            exit(json_encode($return));
        }

        $password = md5($password);
        if ( $password != $user_info['LoginPass']) {
            $return['type'] = 1;
            $return['message'] = '该账户密码不正确!';
            exit(json_encode($return));
        }

        $return['code'] = 0;
        $return['message'] = '登陆成功!';
        $return['first_login'] = 0; //非首次登陆
        unset($session['login_token'],$session['token_time'], $user_info['Pass'], $user_info['LoginPass']);
        $session['user_login'] = $user_info;
        $session['company_attach'] = $attach;
        if (empty($attach['FirstLogin'])) {
            $session['first_login'] = true;
            $return['first_login'] = 1; //首次登陆 
            $companyUser->updateCompanyAttachByUid($user_info['id'], ['FirstLogin'=>time()]);
        }
        $identity = UserLogin::findIdentity($user_info['id']);
        Yii::$app->user->login($identity);//使用用户组件实现认证状态
        exit(json_encode($return));
    }

}
