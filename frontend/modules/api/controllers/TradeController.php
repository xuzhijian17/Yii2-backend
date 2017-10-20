<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\controllers\ApiController;
use frontend\modules\api\services\TradeServiceApi;
use Yii;
use common\lib\CommFun;
class TradeController extends ApiController
{
    
    public $post = [];//post 接收参数数组
    
    public $noNeedPass = false;//交易密码非必需用户
    
    public function init()
    {
        parent::init();
        $this->post = $this->post();
        if(CommFun::ckNoPass($this->post['instid']))
        {
            $this->noNeedPass = true;
        }
    }
    /**
     * 检验必传参数是否为空
     * @param array $param 必传参数
     */
    public function validateParam($param)
    {
        //检验必传参数是否为空
        foreach($param as $key=>$val){
            if ($this->noNeedPass && $val=='tradepassword'){
                continue;
            }else {
                if(!isset($this->post[$val])){
                    $this->handleCode('-3');
                }
            }
        }
        //验证参数有效
        if (isset($this->post['applysum']))
        {
            if ($this->post['applysum'] <=0 || $this->post['applysum'] >=100000000)
            {
                $this->handleCode('-403');
            }
        }
        if (isset($this->post['orderno']))
        {
            $len_order = strlen($this->post['orderno']);
            if ($len_order <5 || $len_order > 40)
            {
                $this->handleCode('-11');//订单号长度非法[5,40]
            }
        }
    }
    public function actionIndex()
    {
        return true;
    }
    /**
     * 认购、申购接口
     */
    public function actionPurchase()
    {
        $needparam = ['instid','signmsg','hcid','orderno','bankacco','applysum','tradepassword','fundcode'];
        //验证必要参数

        $this->validateParam($needparam);
        $post = $this->post;
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);
        $obj = new TradeServiceApi($post['hcid'], $post['instid'], $post['fundcode']);
        $res = $obj->handlePurchase($post);
        $this->handleCode($res['hd_res'],$res['idemp_arr'],['orderno'=>$post['orderno']]);
    }
    /**
     * 撤单接口
     */
    public function actionWithdraw()
    {
        $needparam = ['instid','signmsg','hcid','orderno','applyserial','tradepassword'];
        //验证必要参数
        $this->validateParam($needparam);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($this->post['instid'], $this->post['orderno']);

        $obj = new TradeServiceApi($this->post['hcid'], $this->post['instid']);
        $res = $obj->handleWithDraw($this->post);
        $this->handleCode($res['hd_res'],$res['idemp_arr'],['orderno'=>$this->post['orderno']]);
    }
    /**
     * 赎回接口
     */
    public function actionSale()
    {
        $needparam = ['instid','signmsg','hcid','orderno','bankacco','applyshare','tradepassword','fundcode'];
        //验证必要参数
        $this->validateParam($needparam);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($this->post['instid'], $this->post['orderno']);
        $obj = new TradeServiceApi($this->post['hcid'], $this->post['instid'],$this->post['fundcode']);
        $res = $obj->handleSale($this->post);
        $this->handleCode($res['hd_res'],$res['idemp_arr'],['orderno'=>$this->post['orderno']]);
    }
    /**
     * 新增定投接口
     */
    public function actionValutrade()
    {
        $needparam = ['instid','signmsg','hcid','orderno','bankacco','tradepassword','fundcode','applysum','cycleunit',
            'jyrq','zzrq','scjyrq','jyzq'];
        //验证必要参数
        $this->validateParam($needparam);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($this->post['instid'], $this->post['orderno']);
        $obj = new TradeServiceApi($this->post['hcid'], $this->post['instid'],$this->post['fundcode']);
        $res = $obj->HandleValutrade($this->post);
        $this->handleCode($res['hd_res'],$res['idemp_arr'],['orderno'=>$this->post['orderno']]);
    }
    /**
     * 修改定投接口
     */
    public function actionValutradechange()
    {
        $needparam = ['instid','signmsg','hcid','tradepassword','xyh','applysum','cycleunit','jyrq','zzrq','jyzq','state'];
        //验证必要参数
        $this->validateParam($needparam);
        $obj = new TradeServiceApi($this->post['hcid'], $this->post['instid']);
        $res = $obj->HandleValutradechange($this->post);
        $this->handleCode($res);
    }
    /**
     * 修改分红方式接口
     */
    public function actionBonus()
    {
        $needparam = ['instid','signmsg','hcid','tradepassword','fundcode','melonmethod','orderno'];
        //验证必要参数
        $this->validateParam($needparam);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($this->post['instid'], $this->post['orderno']);
        $obj = new TradeServiceApi($this->post['hcid'], $this->post['instid'],$this->post['fundcode']);
        $res = $obj->HandleBonus($this->post);
        $this->handleCode($res['hd_res'],$res['idemp_arr']);
    }
}