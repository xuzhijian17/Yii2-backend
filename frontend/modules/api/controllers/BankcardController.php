<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\controllers\ApiController;
use frontend\modules\api\models\BankInfo;
use frontend\modules\api\services\AccountServiceApi;
use frontend\modules\api\services\BankCardServiceApi;

/**
 * 银行卡接口
 * 功能包含：换卡、修改银行卡资料、查询银行交易限额等银行卡相关信息
 */
class BankcardController extends BaseController
{
	/*
	 * 更换银行卡类
	 * 功能说明：换卡接口必须走完账户类里面的鉴权流程，即
	 * actionBanksendauthcode方法和actionBankverifyauthcode方法才可以去调换卡接口
	 * 要不然没有办法申赎
	 */
	public function actionChangebankcard()
	{
		$needparam = ['instid', 'signmsg', 'hcid', 'old_bankacco', 'bankacco', 'bankserial', 'branchbank'];
		$this->validateParam($needparam);   //验证必要参数
		$post = $this->post;
		$service_obj = new AccountServiceApi($post['hcid'], $post['instid']);
		$res = $service_obj->changeBankCard($post);
		$this->handleCode($res);
	}

	/*
	 * 查询银行卡单日、单笔交易限额列表(暂不用)
	 */
// 	public function actionTradequotalist()
// 	{
// 		$bank_quota = BankInfo::getBankQuotaList();
// 		$return = ['code'=>'ETS-5BP0000', 'message'=>'get success'];
// 		$this->handleCode($return, ['list'=>$bank_quota]);
// 	}
	/*
	 * 查询银行限额列表(可传参银行编号)
	 */
	public function actionTradequotainfo()
	{
		$needparam = ['instid', 'signmsg'];
		$this->validateParam($needparam);   //验证必要参数

		$post = $this->post;
        if (!empty($post['bankserial'])) {
            $bankinfo = BankInfo::getBankQuotaInfo($post['bankserial']);
            $bank_quota = empty($bankinfo)?null:[$bankinfo];
        } else {
            $bank_quota = BankInfo::getBankQuotaList();
        }
		$return = ['code'=>'ETS-5BP0000', 'message'=>'get success','list'=>empty($bank_quota)?[]:array_values($bank_quota)];
		$this->handleCode($return);
	}
	/**
	 * 鉴权迁移功能(易宝支付用)
	 */
	public function actionAuthtransfer()
	{
	    $needparam = ['instid','signmsg','customername','bankacco','bankno','branchbank','identityno'];
	    $this->validateParam($needparam);   //验证必要参数
	    $post = $this->post;
	    $obj = new BankCardServiceApi;
	    $res = $obj->HandleAuthTransfer($post);
	    $this->handleCode($res);
	}

}