<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\controllers\ApiController;
use common\lib\HundSun;
use frontend\models\Province;
use frontend\models\User;
use common\lib\CommFun;
use frontend\modules\api\models\BankInfo;
use frontend\modules\api\models\UserBank;
use frontend\modules\api\services\AccountServiceApi;
/**
 * 账户类
 */
class AccountController extends ApiController
{
    public $noNeedPass = false; //交易密码非必需用户
    public $post =[];//post提交的数据
	
    public function init()
    {
        parent::init();
        $this->post = $this->post();
        if(CommFun::ckNoPass($this->post['instid']))
        {
            $this->noNeedPass = true;
        }
    }
    
    public function validateParam($param)
    {
        foreach($param as $key=>$val){
            if ($this->noNeedPass && $val=='tradepassword'){
                continue;
            }else {
                if(!isset($this->post[$val])){
                    $this->handleCode('-3');
                }
            }
        }
    }

	//银行卡快捷鉴权验证码发送
    public function actionBanksendauthcode()
	{
		$param = ['bankacco','customername','identityno','mobile','orderno','bankserial'];
		$this->validateParam($param); 
		$post = $this->post;
		AccountServiceApi::CheckIdCard($post['identityno']);//检验是否已开户
		$this->checkBankInfo($post['bankserial']);
		unset($post['instid'], $post['version'], $post['signmsg']);
		$post['orderNo'] = $post['orderno']; unset($post['orderno']);
		$post['capitalmode'] = 'P'; //支付方式
		$post['identitytype'] = '0';//证件类型
		$post['specialflag'] = '1'; //农行资金方式时，传1表示重新发送
		$post['useflag'] = 0; //短信用途
		$post['textCode'] = rand(100000, 999999);// 短信密码
		$hs = new HundSun();
		$hs->mobile = $post['mobile'];
		$res = $hs->apiRequest('B040', $post);
		$this->handleCode($res);
	}

	//银行卡快捷鉴权验证码验证
	public function actionBankverifyauthcode()
	{
		$param = [
			'bankacco',
			'customername',
			'identityno',
			'mobile',
			'orderno',
			'accoreqserial',
			'mobileauthcode',
			'otherserial',
			'bankserial'
		];
		$this->validateParam($param); 
		$post = $this->post;
		//检测订单是否存在
		CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);
		$this->checkBankInfo($post['bankserial']);
		$post['orderNo'] = $post['orderno']; unset($post['orderno']);
		$instid = $post['instid'];
		unset($post['instid'], $post['version'], $post['signmsg']);
		$post['banksessionid'] = $post['mobileauthcode'];//短信密码
		$post['capitalmode'] = 'P';
		$post['identitytype'] = '0'; $hs = new HundSun();

		$hs = new HundSun();
		$hs->mobile = $post['mobile'];
		$res = $hs->apiRequest('B041', $post);
        //生成订单参数
        $idemp_arr = ['Instid'=>$instid,'OrderNo'=>$post['orderNo'],'Oid'=>0,'Type'=>8];
        $this->handleCode($res,$idemp_arr);
	}

	//弱密码检测(暂不用)
	public function actionCheckweakpwd()
	{
		$param = ['identityno','tradepassword'];
		$this->validateParam($param); 
		$post = $this->post();
		$post['idno'] = $post['identityno'];
		unset($post['instid'], $post['version'], $post['signmsg'], $post['identityno']);
		$post['idtype'] = '0'; $hs = new HundSun();
		$res = $hs->apiRequest('C006', $post);
		$this->handleCode($res);
	}

	//开户
	public function actionMeropenacco()
	{
		$param = [
		    'orderno',
			'bankacco',
			'bankname',
			'bankserial',
			'customerappellation',
			'identityno',
			'tradepassword',
			'brachbank',
			'mobile',
		    'yinliancdcard'
		];
		$this->validateParam($param); 
		$post = $this->post;
		//检测订单是否存在
		CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);
		$this->checkBankInfo($post['bankserial']);
		$post['capitalmode'] = 'P';
		$post['identitytype'] = '0';
		$post['invalidate'] = '99991231'; //证件过期 时间
		$post['riskability'] = '3';//风险承受能力
		$post['detailcapitalmode'] = '01';//表示托收，定投需要
		$orderno = $post['orderno'];
		$mobile = $post['mobile'];
		$instid = $post['instid'];
		$post['tradepassword'] = $this->noNeedPass?'123123':$post['tradepassword'];
		unset($post['instid'], $post['version'], $post['signmsg'],$post['mobile'],$post['orderno']);
		$hs = new HundSun();
		$res = $hs->apiRequest('C037', $post);
		if($res['code'] == 'ETS-5BP0000'){
		    $datetime = date("Y-m-d H:i:s");
			if (!empty($post['uid']) && md5($post['uid']) == $post['uidmd5']) { //如果uid存在，则为我们h5过来的只更新用户信息
				$update_user = [
					'BindPhone'=>$mobile,
					'BindTime'=>$datetime,
					'OpenStatus'=>User::OS_OPEN,
					'CardID'=>$post['identityno'],
					'Pass' => $this->noNeedPass?CommFun::AutoEncrypt('123123'):CommFun::AutoEncrypt($post['tradepassword'])
				];
				User::updateAll($update_user, ['id'=>$post['uid']]);
				$res['hcid'] = $post['uid'];
			} else {
				$user = new User();
				$user->Instid = $instid;
				$user->Pass = $this->noNeedPass?CommFun::AutoEncrypt('123123'):CommFun::AutoEncrypt($post['tradepassword']);
				$user->CardID = $post['identityno'];
				$user->BindTime = $datetime;
				$user->SysTime = $datetime;
				$user->OpenStatus = User::OS_OPEN;
				$user->AccountStatus = User::AS_NORMAL;
				$user->BindPhone = $mobile;
				$user->Name = $post['customerappellation'];
				$user->save();
				$res['hcid'] = $user->primaryKey;
			}
			$bank = new UserBank();
			$bank->BankAcco = $post['bankacco']; 
			$bank->Uid = $res['hcid'];
			$bank->BankSerial = $post['bankserial'];
			$bank->TradeAcco = $res['tradeacco'];
			$bank->BindTime = $datetime;
			$bank->CdCard = $post['yinliancdcard'];
			$bank->save();
		}else{
			$res['hcid'] = 0;
		}
		//生成订单参数
		$idemp_arr = ['Instid'=>$instid,'OrderNo'=>$orderno,'Oid'=>$res['hcid'],'Type'=>0];
		$this->handleCode($res,$idemp_arr,['orderno'=>$orderno]);
	}

	//获取联行号
	public function actionGetbrachbank()
	{
		$res['code'] = 'ETS-5BP0000';
		$res['message'] = '成功'; $data = [];
		if($this->post('type') == 1)
			$data = Province::getProvinceList();
		else if($this->post('type') == 2)
			$data = Province::getCityList($this->post('pid'));
		else if($this->post('type') == 3)
			$data = Province::getBankList($this->post('cid'), $this->post('bankserial'));
		$res['data'] = array_values($data);
		$this->handleCode($res);
	}

	//获取风险评测题目及答案
	public function actionGetriskinfo()
	{
		$param = ['hcid'];
		$this->validateParam($param); 
		$post = $this->post;
		unset($post['instid'], $post['version'], $post['signmsg']);
		$hs = new HundSun($post['hcid']); unset($post['hcid']);
		$res = $hs->apiRequest('C004', $post);
		$this->handleCode($res);
	}

	//提交风险评测题目及答案
	public function actionModifyriskinfo()
	{
		$param = ['hcid','qnoandanswer'];
		$this->validateParam($param); 
		$post = $this->post;
		unset($post['instid'], $post['version'], $post['signmsg']);
		$hs = new HundSun($post['hcid']); unset($post['hcid']);
		$res = $hs->apiRequest('C005', $post);
		$this->handleCode($res);
	}

	//设置风险等级，通过等级水平1  安全型,2  保守型,3  稳健型,4  积极型,5  进取型
	public function actionModifyrisklevel()
	{
		$param = ['hcid','riskability'];
		$this->validateParam($param);
		$post = $this->post;
		$hs = new HundSun($post['hcid']);
		$res = $hs->apiRequest('C005', ['riskability'=>$post['riskability']]);
		$this->handleCode($res);
	}

	//读取用户信息
// 	public function actionGetuserinfo()
// 	{
// 		$param = ['hcid'];
// 		$this->validateParam($param); 
// 		$post = $this->post();
// 		unset($post['instid'], $post['version'], $post['signmsg']);
// 		$hs = new HundSun($post['hcid']); unset($post['hcid']);
// 		$res = $hs->apiRequest('C010', $post);
// 		$this->handleCode($res);
// 	}
	

	//修改交易密码
	public function actionModifypwd($value='')
    {
        $pwdModel = new \frontend\modules\api\models\Password();

        $pwdModel->hcid = $this->post['hcid'];
        $pwdModel->newpwd = $this->post['newpwd'];
        $pwdModel->oldpwd = $this->post['oldpwd'];
        $pwdModel->pwdtype = 't';   // t:交易密码;q:查询密码;r:注册密码

        $rs = $pwdModel->setPassword();
        $this->handleCode($rs);
    }
    /**
     * 登录交易平台
     */
    public function actionLogin()
    {
        if ($this->noNeedPass){
            $param = ['instid','signmsg','hcid'];//无密码商户
        }else {
            $param = ['instid','signmsg','password','identityno'];//有密码商户
        }
        $this->validateParam($param);
        $post = $this->post;
        $obj = new AccountServiceApi(0, $post['instid']);
        if ($this->noNeedPass){
            $res = $obj->HandleLogin(null,null,$post['hcid']);
        }else {
            $res = $obj->HandleLogin($post['password'], $post['identityno']);
        }
        $this->handleCode($res);
    }

	//检查该银行是否上线或支持
	public function checkBankInfo($bankserial)
	{
		$bankInfo = BankInfo::getBankQuotaInfo($bankserial);
		if (empty($bankInfo)) { //银行不存在
			$this->handleCode('-205');
		}
		if ($bankInfo['status'] == 0) { //该银行未上线
			$this->handleCode('-206');
		}
	}
}