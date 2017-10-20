<?php
namespace frontend\controllers;

use Yii;
use frontend\services\TradeService;
use common\lib\HundSun;

/**
 * 交易相关控制器
 *
 */
class TradeController extends Controller
{
    public $layout = "trade";//使用布局trade
    
    public $request;
    
    public $uid;//uid属性
    
    public function init()
    {
        parent::init();
        $this->isLogin();
        $this->request = Yii::$app->request;
        $this->uid = isset($this->user['id'])?$this->user['id']:0;//$this->user['id']
        $this->enableCsrfValidation = false;
    }
    public function actionIndex()
    {
        $obj = new TradeService($this->uid,0,'270002');
        var_dump($obj->getFundInfo());
    }
    /**
     * 购买页展示
     */
    public function actionPurchasePage()
    {
        if($this->request->isGet){
            $fundcode = $this->request->get('code','');
        }elseif ($this->request->isPost){
            $fundcode = $this->request->post('fundcode','');
            $post = $this->request->post();
        }
        $obj = new TradeService($this->uid,0,$fundcode);
        $data = $obj->purchasePageData();
        if (empty($data)){
            echo '该基金处于非交易时期';
        }else {
            $data['fundcode'] = $fundcode;
            return $this->render('purchasepage',['data'=>$data,'param'=>empty($post)?[]:$post]);
        }
    }
    /**
     * 生产订单(点击购买)
     */
    public function actionCreateOrder()
    {
        $fundCode = $this->request->post('fundcode','');//基金编号
        $applyAmount = $this->request->post('applyamount','');//申请金额
        $fname = $this->request->post('fname','');//基金名称
        $bname = $this->request->post('bname','');//银行名称
        $bacco = $this->request->post('bacco','');//卡尾号
        $tradeacco = $this->request->post('tradeacco','');//卡尾号
        $infoArr = ['fname'=>$fname,'bname'=>$bname,'bacco'=>$bacco];
        $obj = new TradeService($this->uid,0,$fundCode);
        $orderno = $obj->createOrder($applyAmount, 0, $infoArr,$tradeacco,0);
        if(!empty($orderno))
        {
//             $this->runAction('order-detail',['no'=>$orderno]);
            echo json_encode(['code'=>'0','data'=>$orderno,'msg'=>'创建订单成功']);
        }else {
            echo json_encode(['code'=>'1','data'=>'','msg'=>'创建订单失败']);
        }
    }
    /**
     * 订单详情
     */
    public function actionOrderDetail($no='')
    {
        $orderno = empty($no)?$this->request->get('orderno',''):$no;
        $obj = new TradeService($this->uid,0);
        $data = $obj->orderDetail($orderno);
        if (empty($data))
        {
            echo '服务器出错啦';//今后渲染页面
        }else {
            return $this->render('orderdetail',['data'=>$data]);
        }
    }
    /**
     * 购买提交
     */
    public function actionBuy()
    {
        $applysum = $this->request->post('applyamount','');//申请金额
        $password = $this->request->post('password','');//密码
        $tradeacco = $this->request->post('tradeacco','');//交易号
        $fundcode = $this->request->post('fundcode','');//基金代码
        $orderno = $this->request->post('orderno','');//订单编号
        $obj = new TradeService($this->uid,0,$fundcode);
        $rs = $obj->doPurchase($applysum, $password, $tradeacco,$orderno);
        if ($rs['code'] == HundSun::SUCC_CODE)
        {
            echo json_encode(['code'=>'0','data'=>'','msg'=>'']);//购买成功
        }else {
            echo json_encode(['code'=>'1','data'=>'','msg'=>empty($rs['message'])?'':$rs['message']]);//失败
        }
    }
    /**
     * 撤单提交
     */
    public function actionWithDraw()
    {
        $orderno = $this->request->post('orderno','');//订单编号
        $password = $this->request->post('password','');//密码
        $obj = new TradeService($this->uid, 0);
        $rs = $obj->doWithDraw($orderno, $password);
        if ($rs['code'] == HundSun::SUCC_CODE)
        {
            echo json_encode(['code'=>'0','data'=>'','msg'=>'']);//购买成功
        }else {
            echo json_encode(['code'=>'1','data'=>'','msg'=>empty($rs['message'])?'':$rs['message']]);//失败
        }
    }
    /**
     * 卖出页面
     */
    public function actionSellPage()
    {
        $fundcode = $this->request->get('code','');//基金代码
        $obj = new TradeService($this->uid,0,$fundcode);
        $data = $obj->sellPageData();
        if (empty($data)){
            echo '此页面无数据';
        }else {
            return $this->render('sellpage',['data'=>$data]);
        }
    }
    /**
     * 卖出提交
     */
    public function actionSell()
    {
        $applysum = $this->request->post('applysum','');//申请份额
        $password = $this->request->post('password','');//密码
        $tradeacco = $this->request->post('tradeacco','');//交易号
        $fundcode = $this->request->post('fundcode','');//基金代码
        $sharetype = $this->request->post('sharetype','');//收费方式
        $fundname = $this->request->post('fundname','');//基金名称
        $obj = new TradeService($this->uid,0,$fundcode);
        $rs = $obj->dosale($applysum, $tradeacco, $sharetype,$password,$fundname);
        if ($rs['code'] == HundSun::SUCC_CODE)
        {
            echo json_encode(['code'=>'0','data'=>$rs['data'],'msg'=>'']);//赎回成功
        }else {
            echo json_encode(['code'=>'1','data'=>'','msg'=>empty($rs['message'])?'':$rs['message']]);//失败
        }
    }
    /**
     * 定投页展示
     */
    public function actionValuavgrPage()
    {
        if($this->request->isGet){
            $fundcode = $this->request->get('code','');
        }elseif ($this->request->isPost){
            $fundcode = $this->request->post('fundcode','');
            $post = $this->request->post();
        }else {
            echo '请求错误';//跳转错误页
            return false;
        }
        $obj = new TradeService($this->uid,0,$fundcode);
        $data = $obj->valuavgrPageData();
        $data['fundcode'] = $fundcode;
        return $this->render('valuavgrpage',['data'=>$data,'param'=>empty($post)?[]:$post]);
    }
    /**
     * 新增定投
     */
    public function actionBuyValua()
    {
        $fundcode = $this->request->post('fundcode','');
        $password = $this->request->post('password','');
        $applysum = $this->request->post('applysum','');
        $cycleunit = $this->request->post('cycleunit','0');//周期单位
        $tradeacco = $this->request->post('tradeacco','');//交易账号
        $bankacco = $this->request->post('bankacco','');//银行卡号
        $jyrq = $this->request->post('jyrq','');//交易日期，后两位
        $obj = new TradeService($this->uid,0,$fundcode);
        $rs = $obj->doValuavgr($password, $applysum, $cycleunit, $tradeacco, $bankacco,$jyrq);
        if ($rs['code'] == HundSun::SUCC_CODE)
        {
            echo json_encode(['code'=>'0','data'=>'','msg'=>'']);//定投成功
        }else {
            echo json_encode(['code'=>'1','data'=>'','msg'=>empty($rs['message'])?'':$rs['message']]);//失败
        }
    }
    /**
     * 定投协议列表
     */
    public function actionValuavgrList()
    {
        $obj = new TradeService($this->uid,0);
        $rs = $obj->valuavgrList();
        return $this->render('valuavgrlist',['data'=>$rs]);
    }
    /**
     * 定投详情
     */
    public function actionValuavgrDetail()
    {
        $xyh = $this->request->get('xyh','');
        $obj = new TradeService($this->uid,0);
        $rs = $obj->valuavgrDetail($xyh);
        if (empty($rs))
        {
            echo '页面出错啦';
        }else {
            return $this->render('valuavgrdetail',['data'=>$rs]);
        }
    }
    /**
     * 修改定投
     */
    public function actionValuavgrChange() {
        $xyh = $this->request->post('xyh','');//协议号
        $jyrq = $this->request->post('jyrq','');//交易日期
        $cycleunit = $this->request->post('cycleunit','');//周期单位
        $jyzq = $this->request->post('jyzq','');//交易周期
        $tradeacco = $this->request->post('tradeacco','');//交易账号
        $zzrq = $this->request->post('zzrq','');//终止日期
        $state = $this->request->post('state','');//协议状态
        $applysum = $this->request->post('applysum','');//申购金额
        $obj = new TradeService($this->uid, 0);
        $rs = $obj->valuavgrChange(['xyh'=>$xyh,'jyrq'=>$jyrq,'cycleunit'=>$cycleunit,'jyzq'=>$jyzq,
            'tradeacco'=>$tradeacco,'zzrq'=>$zzrq,'state'=>$state,'applysum'=>$applysum]);
        if ($rs['code'] == HundSun::SUCC_CODE)
        {
            echo json_encode(['code'=>'0','data'=>'','msg'=>'']);//成功
        }else {
            echo json_encode(['code'=>'1','data'=>'','msg'=>$rs['message']]);//失败
        }
    }
}