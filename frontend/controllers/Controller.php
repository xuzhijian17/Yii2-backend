<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class Controller extends \yii\web\Controller
{
	public $session;
	public $enableCsrfValidation = false;
	public $user = null;
	public function init()
    {
		$this->session = \Yii::$app->session;
    }
	public function config($config = '')
	{
		return \Yii::$app->config[$config];
	}
	
	public function createUrl($url)
	{
		return \Yii::$app->urlManager->createUrl($url);
	}
	
	// public function session()
	// {
		// return Yii::$app->session;
	// }
	
	public function get($param = null,$default = null)
	{
		return \Yii::$app->request->get($param,$default);
	}
	public function post($param = null,$default = null)
	{
		return \Yii::$app->request->post($param,$default);
	}
	public function isPost()
	{
		return \Yii::$app->request->getIsPost();
	}
	public function isGet()
	{
		return \Yii::$app->request->getIsGet();
	}
	public function isAjax()
	{
		return \Yii::$app->request->getIsAjax();
	}
	public function renderJson($data)
	{
		header("Content-type:text/json;charset=utf-8");
		exit(json_encode($data));
	}
	
	public function isLogin()
	{
		if(!isset($this->session['user_login']) || !$this->session['user_login']){
			$referer_url = \Yii::$app->request->getHostInfo().\Yii::$app->request->url;
			setcookie('referer_url', $referer_url, time()+3600);
			$url = Yii::$app->getUrlManager()->createUrl('account/login');
			return $this->redirect($url);
		}
		$this->user = $this->session['user_login'];
	}
	
	public function sendMsg($mobile, $message){
		if( empty($mobile) || empty($message) )
        {
            return false;
        }
		$userName = \Yii::$app->params['msg_account'];
		$userPass = \Yii::$app->params['msg_passwd'];
		$subid = '';
		$url = \Yii::$app->params['msg_url'];
		$message = urlencode($message);
		$params = 'UserName='.$userName.'&UserPass='.$userPass.'&subid='.$subid.'&Mobile='.$mobile.'&Content='.$message;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
		curl_setopt($ch, CURLOPT_TIMEOUT,3);
		$data = curl_exec($ch);
		curl_close ($ch);
		if (substr($data, 0,2) == '00' || substr($data, 0,2) == '03'){
			return true;
		}else {
			return false;
		}
	}
}
