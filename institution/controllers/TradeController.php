<?php
namespace institution\controllers;

use Yii;
use institution\service\TradeService;
use institution\lib\InstCommFun;
/**
 *交易相关控制器
 */
class TradeController extends BaseController
{
    public function init()
    {
        parent::init();
    }
    /**
     * 下单页面展示
     */
    public function actionOrderpage()
    {
        $obj = new TradeService($this->user);
        $data = $obj->OrderData();
        return $this->render('tradepage',['data'=>$data]);
    }
    /**
     * 已下单分页数据
     */
    public function actionGetPagedata()
    {
        $obj = new TradeService($this->user);
        $page = $this->post('page',1);
        $data = $obj->CommittedOrder($page);
        return $this->renderAjax('orderlist',['data'=>$data['list']]);
    }
    /**
     * 删除未下单 
     */
    public function actionDelOrder()
    {
        $obj = new TradeService($this->user);
        $ids = $this->post('ids',null);
        if (empty($ids)){
            echo json_encode(['code'=>-101,'desc'=>'要删除单据不能为空'],JSON_UNESCAPED_UNICODE);
            return false;
        }
        $data = $obj->DelOrder($ids);
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 执行指令
     */
    public function actionExcOrder()
    {
        $obj = new TradeService($this->user);
        $ids = $this->post('ids',null);
        if (empty($ids)){
            echo json_encode(['code'=>-101,'desc'=>'要执行单据不能为空'],JSON_UNESCAPED_UNICODE);
            return false;
        }
        $data = $obj->ExcOrder($ids);
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 获取产品列表(交易账号)
     */
    public function actionGetProduct()
    {
        $obj = new TradeService($this->user);
        $data = $obj->SearchProduct();
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 人工下单
     */
    public function actionSaveForm()
    {
        $post = $this->post();
        $obj = new TradeService($this->user);
        $data = $obj->OrderSave($post);
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 分交易账号持仓查询
     */
    public function actionPosition()
    {
        $post = $this->post();
        $obj = new TradeService($this->user);
        $data = $obj->SearchPosition($post['tradeacco']);
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 上传下单指令&执行
     */
    public function actionUploadExecute()
    {
        $obj = new TradeService($this->user);
        $res = $obj->UploadExecute($_FILES['excel']);
        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 查询基金信息请求（自动完成）
     */
    public function actionSearchFund()
    {
        $needle = $this->get('term');//请求字符串
        $res = InstCommFun::autocComplete($needle);
        $fund = array_map(function ($e){
            return ['label'=>$e['FundName'].' '.$e['FundCode'],'value'=>$e['FundCode'],'name'=>$e['FundName']];
        }, $res);
        echo json_encode($fund,JSON_UNESCAPED_UNICODE);
    }
}