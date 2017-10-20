<?php
namespace clientend\controllers;

use Yii;
use yii\web\Controller;
use clientend\lib\ClientCommFun;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;
    public $request; //request属性获取组件 Yii::$app->request
    public $user; //user属性获取$session['user_login']
    
    public function init()
    {
        $this->request = Yii::$app->request;
    }
    public function get($param = null,$default = null)
    {
        return $this->request->get($param,$default);
    }
    public function post($param = null,$default = null)
    {
        return $this->request->post($param,$default);
    }
    public function isPost()
    {
        return $this->request->getIsPost();
    }
    public function isGet()
    {
        return $this->request->getIsGet();
    }
    public function isAjax()
    {
        return $this->request->getIsAjax();
    }
    /**
     * 判断是否登录1.未登陆跳转/2已登陆获取session
     * 注:session['user_login']=>['id'=>'用户id','CardID'=>'身份证号','Name'=>'姓名','Instid'=>'商户号',
     * 'AccountStatus'=>'账户状态','OpenStatus'=>'开户状态']
     */
    public function isLogin()
    {
        $session = Yii::$app->session;
        if(empty($session['user_login'])){
            $referer_url = $this->request->getHostInfo().$this->request->url;
            setcookie('referer_url', $referer_url, time()+600);//登陆后跳转url
            $url = Yii::$app->getUrlManager()->createUrl('account/login');
            return $this->redirect($url);
        }else {
            $this->user = $session['user_login'];
        }
    }
    /**
     * 跳转错误页面
     */
    public function errPage($msg)
    {
        return $this->renderPartial('error',['data'=>$msg]);
    }
    /**
     * 处理返回结果
     * @param mixed $data 返回数据
     */
    public function handleAjaxResponse($data)
    {
        $res = ClientCommFun::clientHandleCode($data);
        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }
}