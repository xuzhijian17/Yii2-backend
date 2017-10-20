<?php
namespace frontend\controllers;
use Yii;
use common\lib\HundSun;
use common\lib\CommFun;
use frontend\models\User;
use frontend\models\Province;
use yii\helpers\Url;
/**
 * Site controller
 */
class AccountController extends Controller
{
	public $layout = false;

	public function actionLogin()
	{
		if($this->isAjax() && $this->isPost()){
			$text = $this->post('text');
			$passwd = $this->post('password');
			if(!$text || !in_array(strlen($text), [11, 16]) || !$passwd)
				$this->renderJson(['error'=>0, 'msg'=>'账号或者密码不正确']);
			if(strlen($text) == 11){
				$where['RegPhone'] = $text;
			}
			if(strlen($text) == 16){
				$where['CardID'] = $text;
			}
			//$where['Pass'] = CommFun::AutoEncrypt($passwd);
			//print_r($where);
			$user = User::find()->where($where)->one();
			if($user && CommFun::AutoEncrypt($user['Pass'], 'D') == $passwd){
				$this->session['user_login'] = $user;
				$referer_url = isset($_COOKIE['referer_url']) && $_COOKIE['referer_url'] ?
								$_COOKIE['referer_url'] : Url::to(['fund-market/index']); 
				setcookie('referer_url', null, time()-3600);
				$this->renderJson(['error'=>1, 'url'=>$referer_url]);
			}
			$this->renderJson(['error'=>0, 'msg'=>'账号或者密码错误']);
		}
		return $this->render('login');
	}
    public function actionOpen()
    {
		if($this->isPost() && $this->isAjax()){
			$open_account = (!isset($this->session['open_account']) 
					|| empty($this->session['open_account'])) ? [] : $this->session['open_account'];
			$step = $this->post('step');
			if($step == 1){
				$open_account = ['phone' => $this->post('phone'), 'name' => $this->post('name'), 
								'cardid' => $this->post('cardid')] + $open_account;
				$this->session['open_account'] = $open_account;
				$this->renderJson(['error'=>1, 'msg'=>'']);
			}
			if($step == 2){
				if(!$this->post('passwd') || $this->post('passwd') != $this->post('passwd1'))
					$this->renderJson(['error'=>0, 'msg'=>'两次密码不一致']);
				$open_account = ['tradepassword' => $this->post('passwd')] + $open_account;
				$this->session['open_account'] = $open_account;
				$res = $this->checkweakpwd($this->session['open_account']);
				$this->log($res);
				if($res['error'] == 0) $this->renderJson(['error'=>0, 'msg'=>$res['message']]);
				$this->renderJson(['error'=>1, 'msg'=>'']);
			}
			if($step == 3){
				$open_account = ['bankno' => $this->post('bankno'), 'bankcard'=>$this->post('bankcard'), 
						'branchbank'=>$this->post('branchbank'), 'bankphone'=>$this->post('bankphone'),
						'bankname'=>$this->post('bankname')] + $open_account;
				$this->session['open_account'] = $open_account;
				$res = $this->banksendauthcode($this->session['open_account']);
				$this->log($res);
				if($res['error'] == 1){
					$this->session['open_account'] = array_merge($this->session['open_account'],[
								'accoreqserial' => $res['accoreqserial'], 'otherserial' => $res['otherserial']]);
				}else{
					$this->renderJson(['error'=>0, 'msg'=>$res['message']]);
				}
				$this->renderJson(['error'=>1, 'msg'=>$res['message']]);
			}
			
			if($step == 4){
				$code = $this->post('code', 0);
				$code == 0 && $this->renderJson(['error'=>0, 'msg'=>"验证码不能为空"]);
				$res = $this->bankverifyauthcode(array_merge($this->session['open_account'],['code'=>$code]));
				$this->log($res);
				if($res['error'] != 1){
					$this->renderJson(['error'=>0, 'msg'=>$res['message']]);
				}
				$res = $this->meropenacco($this->session['open_account']);
				$this->log($res);
				if($res['error'] != 1){
					$this->renderJson(['error'=>0, 'msg'=>$res['message']]);
				}
				$this->renderJson(['error'=>1, 'msg'=>$res['message']]);
			}
			
			if($step == 5){
				$res = $this->banksendauthcode($this->session['open_account']);
				$this->log($res);
				if($res['error'] == 1){
					$this->session['open_account'] = array_merge($this->session['open_account'],[
								'accoreqserial' => $res['accoreqserial'], 'otherserial' => $res['otherserial']]);
				}else{
					$this->renderJson(['error'=>0, 'msg'=>$res['message']]);
				}
				$this->renderJson(['error'=>1, 'msg'=>$res['message']]);
			}
		}
		if($this->isGet()) {
			$this->session['open_account'] = [];
			return $this->render('open');
		}
    }
	
	public function actionGetbank()
    {
		$bank = [
			'002'=>['name'=>'工商银行', 'logo'=>Yii::getAlias('@web').'/images/bank_gongshang.png'],
			'003'=>['name'=>'农业银行', 'logo'=>Yii::getAlias('@web').'/images/bank_nongye.png'],
			'004'=>['name'=>'中国银行', 'logo'=>Yii::getAlias('@web').'/images/bank_zhongguo.png'],
			'005'=>['name'=>'建设银行', 'logo'=>Yii::getAlias('@web').'/images/bank_jianshe.png'],
			
			'006'=>['name'=>'交通银行', 'logo'=>Yii::getAlias('@web').'/images/bank_jiaotong.png'],
			'007'=>['name'=>'招商银行', 'logo'=>Yii::getAlias('@web').'/images/bank_zhaoshang.png'],
			'009'=>['name'=>'光大银行', 'logo'=>Yii::getAlias('@web').'/images/bank_guangda.png'],
			'010'=>['name'=>'浦发银行', 'logo'=>Yii::getAlias('@web').'/images/bank_pufa.png'],
			
			'011'=>['name'=>'兴业银行', 'logo'=>Yii::getAlias('@web').'/images/bank_xingye.png'],
			'012'=>['name'=>'华夏银行', 'logo'=>Yii::getAlias('@web').'/images/bank_huaxia.png'],
			//'014'=>['name'=>'民生银行', 'logo'=>Yii::getAlias('@web').'/images/bank_minsheng.png'],
			'920'=>['name'=>'平安银行', 'logo'=>Yii::getAlias('@web').'/images/bank_pingan.png'],
		];
		$this->renderJson(['list'=>$bank]);
	}
	
	public function actionGetprovince()
    {
		if($this->get('type') == 1)
			$data = Province::getProvinceList();
		else if($this->get('type') == 2)
			$data = Province::getCityList($this->get('pid'));
		else if($this->get('type') == 3)
			$data = Province::getBankList($this->get('cid'), $this->get('bankno'));
		//var_dump($data);
		$this->renderJson(['list'=>$data]);
	}
	public function actionSendmsg()
    {
		$type = $this->get('type', 0);
		if($type){
			$code = $this->get('code');
			if($code == $this->session['phone_code'])
				$this->renderJson(['error'=>1, 'msg'=>'']);
			else
				$this->renderJson(['error'=>0, 'msg'=>'']);
		}else{
			$phone = $this->get('phone');
			if(!$phone || strlen($phone) != 11 || 
				in_array(substr($phone, 0, 2), ['12', '13']))
				$this->renderJson(['error'=>0, 'msg'=>'手机格式错误']);
			$user = User::find()->where(['RegPhone' => $phone])->one();
			if($user){
				$this->renderJson(['error'=>0, 'msg'=>'手机号已注册']);
			}
			$code = '111111';//rand(100000, 999999);
			$this->session['phone_code'] = $code;
			$msg = "【好投顾】您的验证码是".$code;
			//if($this->sendMsg($phone, $msg))
			$this->renderJson(['error'=>1, 'msg'=>'']);
			$this->renderJson(['error'=>0, 'msg'=>'']);
		}
    }
	
	//C006校验弱密码
	public function checkweakpwd($account){
		$param = [
			'idtype' => '0', //证件类型
			'idno' => $account['cardid'],//$open_account['identityno'], //证件号码
			'tradepassword' => $account['tradepassword'],
		];
		$this->log($param);
		$hs = new HundSun();
		$res = $hs->apiRequest('C006', $param);
		if($res['code'] == 'ETS-5BP0000'){
			$res['error'] = 1;
		}else{
			$res['error'] = 0;
		}
		return $res;
	}
	
	//B040银行卡快捷鉴权验证码发送
	public function banksendauthcode($account){
		$param = [
			'bankacco' => $account['bankcard'],//银行账号
			'bankserial' => $account['bankno'],
			'customername' => $account['name'],
			'capitalmode' => 'P',
			'identitytype' => '0', //证件类型
			'identityno' => $account['cardid'], //证件号码
			'mobile' => $account['bankphone'],
			'orderNo' => date("YmdHis").rand(10000, 99999),
			'specialflag' => 1,
			'textCode' => rand(100000, 999999),
			'useflag' => 0,
		];
		$this->log($param);
		$hs = new HundSun();
		$res = $hs->apiRequest('B040', $param);
		if($res['code'] == 'ETS-5BP0000'){
			$res['error'] = 1;
		}else{
			$res['error'] = 0;
		}
		// var_dump($res);
		return $res;
	}
	
	//B041银行卡快捷鉴权验证码验证
	public function bankverifyauthcode($account){
		$param = [
			'accoreqserial' => $account['accoreqserial'],
			'capitalmode' => 'P',
			'mobile' => $account['bankphone'],
			'mobileauthcode' => $account['code'],
			'bankacco' => $account['bankcard'], //银行账号
			'bankserial' => $account['bankno'],
			'customername' => $account['name'],
			'identitytype' => '0', //证件类型
			'identityno' => $account['cardid'],//$open_account['identityno'], //证件号码
			'otherserial' => $account['otherserial'],
		];
		$this->log($param);
		$hs = new HundSun();
		$res = $hs->apiRequest('B041', $param);
		$res['message'] = $res['message']?:'异常错误';
		if($res['code'] == 'ETS-5BP0000'){
			$res['error'] = 1;
		}else{
			$res['error'] = 0;
		}
		return $res;
	}
	
	//C037开户
	public function meropenacco($account){
		$param = [
			'bankacco' => $account['bankcard'], //$open_account['bankacco'], //银行账号
			'bankname' => $account['bankname'],
			'bankserial' => $account['bankno'],//银行代码
			'capitalmode' => 'P',
			'customerappellation' => $account['name'],//$open_account['customername'], //客户姓名
			'identitytype' => '0', //证件类型
			'identityno' => $account['cardid'],//$open_account['identityno'], //证件号码
			'tradepassword' => $account['tradepassword'],
			'brachbank' => $account['branchbank'],
			'tacode' => '27',
			//'birthday' => '19880730',
			'invalidate' => '99991230',
			'riskability' => 0
		];
		$this->log($param);
		$hs = new HundSun();
		$res = $hs->apiRequest('C037', $param);
		if($res['code'] == 'ETS-5BP0000'){
			$res['error'] = 1;
			$user = new User();
			$user->RegPhone = $account['phone']; $user->BindPhone = $account['bankphone']; 
			$user->Pass = CommFun::AutoEncrypt($param['tradepassword']); 
			$user->CardID = $param['identityno']; $user->save();
		}else{
			$res['error'] = 0;
		}
		return $res;
	}
	
	//获取评测试题
	public function actionQueryrisk()
    {
		//$this->session['user_login'] = null;
		$this->isLogin();
		$hs = new HundSun($this->user['id']);
		$res = $hs->apiRequest('C004', []);
		if($this->isAjax() && $this->isPost()){
			$answer = [];
			for($i = 0; $i < count($res['risklist']); $i++){
				$post = $this->post('answer'.$i);
				//var_dump($post);
				if(!isset($post[1]) || !$post[1])
					$this->renderJson(['error'=>0, 'msg'=>'试题未做完']);
				$answer[$post[0]]=$post[1];
			}
			$param = [
				'qnoandanswer' => base64_encode(json_encode($answer))
			];
			$res = $hs->apiRequest('C005', $param);
			$this->log($answer);
			if($res['code'] == 'ETS-5BP0000')
				$this->renderJson(['error'=>1, 'msg'=>'ok']);
			else
				$this->renderJson(['error'=>0, 'msg'=>'处理失败']);
		}
		return $this->render('queryrisk',['data'=>$res['risklist']]);
    }
	
	public function actionRiskshow()
    {
		$this->isLogin();
		$hs = new HundSun($this->user['id']);
		$res = $hs->apiRequest('C010', []);
		$data = ['', '保守型', '稳健型', '平衡型', '成长型', '进取型'];
		$falt = $res['riskFlag'] ? : 1;
		return $this->render('riskshow', ['data'=>['key'=>$falt, 'data'=>$data[$falt]]]);
    }
	
	private function log($data)
	{
		error_log(print_r($data, 1), 3, Yii::$app->getRuntimePath()."/logs/account.log");
	}
	
	//B040银行卡快捷鉴权验证码发送
	public function actionBanksendauthcode(){
		$param = [
			'bankacco' => '6217000010000730724',//$account['bankcard'],//银行账号
			'bankserial' => '005',//$account['bankno'],
			'customername' => '高彦回',//$account['bankname'],
			'capitalmode' => 'P',
			'identitytype' => '0', //证件类型
			'identityno' => '411024198807301619',//$account['cardid'], //证件号码
			'mobile' => '15010240122',//$account['bankphone'],
			'orderNo' => date("YmdHis").rand(10000, 99999),
			'specialflag' => 1,
			'textCode' => rand(100000, 999999),
			'useflag' => 0,
		];
		var_dump($param);
		$hs = new HundSun();
		$res = $hs->apiRequest('B040', $param);
		if($res['code'] == 'ETS-5BP0000'){
			$res['error'] = 1;
		}else{
			$res['error'] = 0;
		}
		var_dump($res);
		//return $res;
	}
	
	//B041银行卡快捷鉴权验证码验证
	public function actionBankverifyauthcode(){
		$param = [
			'accoreqserial' => '201604139',//$account['accoreqserial'],
			'capitalmode' => 'P',
			'mobile' => '15010240122',//$account['bankphone'],
			'mobileauthcode' => '111111',
			'banksessionid' => '111111',
			'bankacco' => '6217000010000730724',//$account['bankcard'], //银行账号
			'bankserial' => '005',//$account['bankno'],
			'customername' => '高彦回',//$account['bankname'],
			'identitytype' => '0', //证件类型
			'identityno' => '411024198807301619',//$account['cardid'],//$open_account['identityno'], //证件号码
			'otherserial' => 'e4b73500ccf844bab84c7e4d8ed87959',//$account['otherserial'],
			//'userFlag' => 0
		];
		//error_log(print_r($param, 1), 3, Yii::$app->getRuntimePath()."/logs/http.log");
		$hs = new HundSun();
		$res = $hs->apiRequest('B041', $param);
		if($res['code'] == 'ETS-5BP0000'){
			$res['error'] = 1;
		}else{
			$res['error'] = 0;
		}
		var_dump($res);
		//return $res;
	}
	//C037开户
	public function actionMeropenacco(){
		$param = [
			'bankacco' => '6217000010000730724', //$open_account['bankacco'], //银行账号
			'bankname' => '中国建设银行中关村支行',
			'bankserial' => '005',//银行代码
			'capitalmode' => 'P',
			'customerappellation' => '高彦回',//$open_account['customername'], //客户姓名
			'identitytype' => '0', //证件类型
			'identityno' => '411024198807301619',//$open_account['identityno'], //证件号码
			'tradepassword' => '037088',
			'brachbank' => '102536300085',
			//'birthday' => '19880730',
			'invalidate' => '99991231',
			'riskability' => 0
		];
		$hs = new HundSun();
		$res = $hs->apiRequest('C037', $param);
		// $user = new User();
		// $user->RegPhone = $param['identityno']; $user->BindPhone = '15010240122'; 
		// $user->Pass = CommFun::AutoEncrypt($param['tradepassword']); 
		// $user->CardID = $param['identityno']; $user->save();
		// if($res['code'] == 'ETS-5BP0000'){
			// $res['error'] = 1;
		// }else{
			// $res['error'] = 0;
		// }
		print_r($res);
	}
	//易宝渠道无密验卡B011
	public function actionYeepaynopwdsign()
    {
		$param = [
			'bankacco' => '6217000010000730724', //$open_account['bankacco'], //银行账号
			'bankserial' => '005',//银行代码
			'custname' => '高彦回',//$open_account['customername'], //客户姓名
			'identitytype' => '0', //证件类型
			'identityno' => '411024198807301619',//$open_account['identityno'], //证件号码
		];
		$hs = new HundSun();
		$res = $hs->apiRequest('B011', $param);
		var_dump($res);
    }
	
	//小额打款B012
	public function actionYeepayremit()
    {
		$param = [
			'bankacco' => '6217000010000730724', //$open_account['bankacco'], //银行账号
			'bankserial' => '005',//银行代码
			'custname' => '高彦回',//$open_account['customername'], //客户姓名
			// 'identitytype' => '0', //证件类型
			// 'identityno' => '411024198807301619',//$open_account['identityno'], //证件号码
		];
		$hs = new HundSun();
		$res = $hs->apiRequest('B012', $param);
		var_dump($res);
    }
	
	//提交评测试题
	public function actionModifyrisk ()
    {
		$hs = new HundSun(3);
		// $qanswer = '{"00001:"005", "00002:"005", "00003:"005",
			// "00004:"005", "00005:"005", "00006:"005",
			// "00007:"005", "00008:"005", "00009:"005",
			// "00010:"005", "00011:"005", "00012:"005"}';
		// $qanswer = [
				// ["qanswer"=>"005", "qno"=>"00001"],
				// ["qanswer"=>"005", "qno"=>"00002"],
				// ["qanswer"=>"005", "qno"=>"00003"],
				// ["qanswer"=>"005", "qno"=>"00004"],
				// ["qanswer"=>"005", "qno"=>"00005"],
				// ["qanswer"=>"005", "qno"=>"00006"],
				// ["qanswer"=>"005", "qno"=>"00007"],
				// ["qanswer"=>"005", "qno"=>"00008"],
				// ["qanswer"=>"005", "qno"=>"00009"],
				// ["qanswer"=>"005", "qno"=>"00010"],
				// ["qanswer"=>"005", "qno"=>"00011"],
				// ["qanswer"=>"005", "qno"=>"00012"],
			// ];
		$qanswer = [
				"00001"=>"005", "00002"=>"005", "00003"=>"005",
				"00004"=>"005", "00005"=>"005", "00006"=>"005",
				"00007"=>"005", "00008"=>"005", "00009"=>"005",
				"00010"=>"005", "00011"=>"005", "00012"=>"005",
			];
		var_dump(json_encode($qanswer));
		$param = [
			'qnoandanswer' => base64_encode(json_encode($qanswer))
		];
		$res = $hs->apiRequest('C005', $param);
		print_r($res);
    }
	//修改密码
	public function actionModifypwd(){
		$param = [
			'newpwd' => '037088',
			'oldpwd' => '037088',
			'pwdtype' => 't',
		];
		$hs = new HundSun(3);
		$res = $hs->apiRequest('C012', $param);
		var_dump($res);
	}
	//重置交易密码
	public function actionResetpwd(){
		$param = [
			'custname' => '高彦回',
			'idno' => '411024198807301619',
			'idtype' => 0,
			'newpwd' => '037088',
			'way' => 3,
		];
		$hs = new HundSun(3);
		$res = $hs->apiRequest('C029', $param);
		var_dump($res);
	}
	public function actionTest()
    {
		// $param = [
			// 'applyrecordno' => 10, //$open_account['bankacco'], //银行账号
		// ];
		$hs = new HundSun(4);
		$res = $hs->apiRequest('C010', []);
		print_r($res);
    }
}
