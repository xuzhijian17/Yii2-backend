<?php
namespace institution\controllers;

use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    public $layout = "main";//使用布局main
    public $enableCsrfValidation = false;
    public $request; //request属性获取组件 Yii::$app->request
    public $user; //user属性获取$session['user_login']
    
    
    public function init()
    {
        $this->request = Yii::$app->request;
        $requestUrl = Yii::$app->request->getPathInfo();
        $noLoginArr = ['account/login','account/dologin',''];
        if (!in_array($requestUrl, $noLoginArr))
        {
            $this->isLogin();
        }
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
     * 注:session['user_login']=>['userId'=>'用户id','userName'=>'用户名','orgCode'=>'机构代码','orgName'=>'机构名称',
     * 'status'=>'用户状态 0:禁用1激活']
     */
    public function isLogin()
    {
        $session = Yii::$app->session;
        if(empty($session['user_login'])){
            $url = Yii::$app->getUrlManager()->createUrl('account/login');
            return $this->redirect($url);
        }else {
            $this->user = $session['user_login'];
        }
    }
}