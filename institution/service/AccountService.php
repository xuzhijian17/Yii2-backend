<?php
namespace institution\service;

use Yii;
use institution\service\JavaRestful;
/**
 *账户相关处理逻辑
 */
class AccountService
{
    /**
     * 登录
     * @param string $account 账号
     * @param string $password 密码
     * session user_login定义
     * ['orgCode'=>'机构代码','orgName'=>'机构名称','status'=>'用户状态0:禁用1:激活','userId'=>'用户id','userName'=>'用户名']
     */
    public function DoLogin($account,$password)
    {
        $obj = new JavaRestful('A001', ['userName'=>$account,'pwd'=>base64_encode($password)], 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res'])){
            //保持session
            $session = Yii::$app->session;
            $session['user_login'] = $res['res'];
        }
        return $res;
    }
    /**
     * 注销登录
     */
    public function LoginOut()
    {
        $session = Yii::$app->session;
        $session->remove('user_login');
    }

    /**
     * 修改密码
     */
    public function UpdatePwd($account,$password,$oldPassword)
    {
        $obj = new JavaRestful('A002', ['userName'=>$account,'pwd'=>base64_encode($password),'oldPwd'=>base64_encode($oldPassword)], 0);
        $res = $obj->apiRequest();
        
        if (!$res || empty($res)){
            return false;
        }

        return $res;
    }

    /*
     * 机构开户
     * @param array $args 开户参数
     */
    public function AccountOpen($args=[])
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $args['code'] = $orgCode;
        $obj = new JavaRestful('A003', $args, 0, true);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE){
            return true;
        }
        return false;
    }

    //查询开户信息
    public function SearchOpenList()
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $args = [];
        $args['orgCode'] = $orgCode;
        $args['userName'] = $user_login['userName'];
        $obj = new JavaRestful('A004', $args, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res'])){
            return $res['res'];
        }
        return [];
    }

    //查询是否开过户
    public function SearchIsOpen()
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $args = [];
        $args['orgCode'] = $orgCode;
        $args['userName'] = $user_login['userName'];
        $args['status'] = '开户成功';

        $obj = new JavaRestful('A004', $args, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res'])){
            return true;
        }
        return false;
    }

    //查询开户信息
    public function searchOpenInfo()
    {
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $args = [];
        $args['orgCode'] = $orgCode;
        $obj = new JavaRestful('A005', $args, 1);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res'])){
            return $res['res'];
        }
        return [];
    }
}