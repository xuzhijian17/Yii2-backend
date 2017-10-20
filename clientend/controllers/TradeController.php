<?php
namespace clientend\controllers;

use Yii;
use clientend\lib\Validata;
use clientend\services\TradeService;
/**
 * 交易相关controller
 */
class TradeController extends BaseController
{
    public $layout = "trade";//使用布局trade
    
    public $uid;//uid属性
    
    public function init()
    {
        parent::init();
        $this->isLogin();
        $this->uid = isset($this->user['id'])?$this->user['id']:0;
    }
    public function actionIndex()
    {
        return false;
    }
    /**
     * 购买页展示
     */
    public function actionPurchasePage()
    {
       $fundcode = $this->get('fundcode','');
       if (!Validata::validata_fundcode($fundcode))
       {
           return $this->errPage('参数有误找不到页面');
       }
       $obj = new TradeService($this->uid,$fundcode);
       $res = $obj->purchasePageData();
       if ($res['code'] ==0)
       {
           return $this->render('purchasepage',['data'=>$res['data']]);
       }else {
           return $this->errPage($res['message']);
       }
    }
    /**
     * 请求风险测评
     * @return ['code'=>1,'message'=>'信息']code=1时才显示风险提示
     */
    public function actionRiskmatch()
    {
        $fundcode = $this->get('fundcode','');
        $obj = new TradeService($this->uid,$fundcode);
        $res = $obj->getRisk();
        if ($res['code']==0)
        {
            $data = $res['data']['riskflag']=='01'?['code'=>'0','message'=>'']:['code'=>'1','message'=>$res['data']['riskmsg']];
        }else {
            $data = ['code'=>'-1','message'=>'获取风险测评失败'];
        }
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 购买基金
     */
    public function actionPurchase()
    {
        $post = $this->post();
        if (empty($post['fundcode']) || empty($post['applysum']) || !Validata::validata_fundcode($post['fundcode']) 
            || !is_numeric($post['applysum']) || !is_numeric($post['tradepassword']))
        {
            return $this->handleAjaxResponse(-11);//参数验证失败
        }
        $obj = new TradeService($this->uid,$post['fundcode']);
        $data = $obj->doPurchase($post['applysum']);
        $this->handleAjaxResponse($data);
    }
    /**
     * 交易结果页
     */
    public function actionResultpage()
    {
        $orderno = $this->get('orderno','');
        if (empty($orderno)){
            return $this->errPage('参数有误找不到页面');
        }
        $obj = new TradeService($this->uid);
        $res = $obj->getTradeResult($orderno);
        if ($res['code'] ==0)
        {
            return $this->render('resultpage',['data'=>$res['data']]);
        }else {
            return $this->errPage($res['message']);
        }
    }
    /**
     * 赎回页面展示
     */
    public function actionSellpage()
    {
        $fundcode = $this->get('fundcode','');
        if (!Validata::validata_fundcode($fundcode))
        {
            return $this->errPage('参数有误找不到页面');
        }
        $obj = new TradeService($this->uid,$fundcode);
        $res = $obj->sellPageData();
        if ($res['code'] ==0)
        {
            return $this->render('sellpage',['data'=>$res['data']]);
        }else {
            return $this->errPage($res['message']);
        }
    }
    /**
     * 赎回基金
     */
    public function actionSell()
    {
        $post = $this->post();
        if (empty($post['fundcode']) || empty($post['applyshare']) || !Validata::validata_fundcode($post['fundcode'])
            || !is_numeric($post['applyshare']) || !is_numeric($post['tradepassword']))
        {
            return $this->handleAjaxResponse(-11);//参数验证失败
        }
        $obj = new TradeService($this->uid,$post['fundcode']);
        $data = $obj->doSell($post['applyshare'], $post['tradepassword']);
        $this->handleAjaxResponse($data);
    }
}