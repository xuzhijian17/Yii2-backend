<?php
namespace fundzone\controllers;

use Yii;
use fundzone\service\FundzoneTradeService;
use clientend\lib\Validata;

/**
 *交易相关处理类
 */
class TradeController extends BaseController
{
    public $layout = "main";//使用布局main
    
    public $uid;//uid属性
    
    public function init()
    {
        parent::init();
        $this->isLogin();
        $this->uid = isset($this->user['id'])?$this->user['id']:0;
    }
    public function actionIndex()
    {
        return $this->errPage('啦啦啦啦啦');
    }
    /**
     * 购买页展示
     */
    public function actionPurchasePage()
    {
        if (isset($this->user['Instid']))
        {
            $fundcode = $this->get('fundcode','');
            if ($this->user['Instid']==1000)//企业
            {
                return $this->runAction('company-purchase-page',['fundcode'=>$fundcode]);
            }else {
                //个人
                return $this->runAction('company-purchase-page',['fundcode'=>$fundcode]);
            }
        }else {
            $this->isLogin();
        }
    }
    /**
     * 赎回页展示
     */
    public function actionSellPage()
    {
        if (isset($this->user['Instid']))
        {
            $fundcode = $this->get('fundcode','');
            if ($this->user['Instid']==1000)//企业
            {
                return $this->runAction('company-sell-page',['fundcode'=>$fundcode]);
            }else {
                //个人
                return $this->runAction('company-sell-page',['fundcode'=>$fundcode]);
            }
        }else {
            $this->isLogin();
        }
    }
    /**
     * 购买页展示
     */
    public function actionCompanyPurchasePage($fundcode)
    {
        if (!Validata::validata_fundcode($fundcode))
        {
            return $this->errPage('参数有误找不到页面');
        }
        $obj = new FundzoneTradeService($this->uid,$fundcode);
        $res = $obj->companyPurchasePageData();
        if ($res['code']==0)
        {
            $token = $obj->getToken(0);
            if (empty($token)){
                return $this->errPage('无法获取用户登录信息');
            }
            return $this->render('companypurchasepage',['data'=>$res['data'],'token'=>$token]);
        }else {
            return $this->errPage($res['message']);
        }
    }
    /**
     * 购买基金
     */
    public function actionCompanyPurchase()
    {
        $post = $this->post();
        if (empty($post['fundcode']) || empty($post['applysum']) || !Validata::validata_fundcode($post['fundcode'])
            || !is_numeric($post['applysum']) || !is_numeric($post['tradepassword']) || empty($post['token']))
        {
            echo json_encode(['code'=>'-101','message'=>'提交参数不正确']);
            return false;
        }
        $obj = new FundzoneTradeService($this->uid,$post['fundcode']);
        if ($obj->isTokenValid(0, $post['token']))
        {
            $res = $obj->companyDoPurchase($post['applysum'], $post['tradepassword']);
            if ($res['code']==-8)
            {
                $obj->setToken(0, $post['token']);
            }
            echo json_encode($res,JSON_UNESCAPED_UNICODE);
        }else {
            echo json_encode(['code'=>'-101','message'=>'页面已过期']);
            return false;
        }
    }
    /**
     * 赎回页面
     */
    public function actionCompanySellPage($fundcode)
    {
        if (!Validata::validata_fundcode($fundcode))
        {
            return $this->errPage('参数有误找不到页面');
        }
        $obj = new FundzoneTradeService($this->uid,$fundcode);
        $res = $obj->companySellPageData();
        if ($res['code'] ==0)
        {
            $token = $obj->getToken(1);
            if (empty($token)){
                return $this->errPage('无法获取用户登录信息');
            }
            return $this->render('companysellpage',['data'=>$res['data'],'token'=>$token]);
        }else {
            return $this->errPage($res['message']);
        }
    }
    /**
     * 赎回基金
     */
    public function actionCompanySell()
    {
        $post = $this->post();
        if (empty($post['fundcode']) || empty($post['applyshare']) || !Validata::validata_fundcode($post['fundcode'])
            || !is_numeric($post['applyshare']) || !is_numeric($post['tradepassword']) || empty($post['token']))
        {
            echo json_encode(['code'=>'-101','message'=>'提交参数不正确']);
            return false;
        }
        $obj = new FundzoneTradeService($this->uid,$post['fundcode']);
        if ($obj->isTokenValid(1, $post['token']))
        {
            $res = $obj->companyDoSell($post['applyshare'], $post['tradepassword'],$post['mintredeem']);
            if ($res['code']==-8)
            {
                $obj->setToken(0, $post['token']);
            }
            echo json_encode($res,JSON_UNESCAPED_UNICODE);
        }else {
            echo json_encode(['code'=>'-101','message'=>'页面已过期']);
            return false;
        }
    }
    /**
     * 撤单
     */
    public function actionWithdraw()
    {
        $applyserial = $this->post('applyserial','');
        if (empty($applyserial))
        {
            echo json_decode(['code'=>'-11','message'=>'参数验证失败']);
            return false;
        }
        $obj = new FundzoneTradeService($this->uid);
        $res = $obj->WithDraw($applyserial);
        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }
}
