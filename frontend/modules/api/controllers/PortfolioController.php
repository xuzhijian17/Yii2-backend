<?php
namespace frontend\modules\api\controllers;
use common\lib\CommFun;
use common\lib\FileInterfaceOrder;
use frontend\modules\api\controllers\ApiController;
use frontend\modules\api\models\PortfolioTrade;
use frontend\modules\api\services\PortfolioServiceApi;
use frontend\modules\api\models\PortfolioConfig;
use Yii;

/**
 * 组合接口类
 *
 * Class PortfolioController
 * @package frontend\modules\api\controllers
 */
class PortfolioController extends BaseController
{
    public function actionTest()
    {
        CommFun::checkPartnerTableExists(1);
        exit;
        $portfolioTrade = new PortfolioTrade(1, []);
        $rs = $portfolioTrade->getRecordsCount();
        print_r($rs);
        exit;
        $fileOrder = new FileInterfaceOrder(1);
        $a = $fileOrder->executeExportNav();
        var_dump($a);
        exit;
        $portfolioTrade = new PortfolioTrade(1);
        $a = [["fundcode"=>"000331", "fundname"=>"中加货币", "ratio"=>20], ["fundcode"=>"400001", "fundname"=>"新沃基金", "ratio"=>30]];
        echo json_encode($a, JSON_UNESCAPED_UNICODE);
    }

    /*
     * 获取可购买的组合列表
     */
    public function actionPortfoliolist()
    {
        $needparam = ['instid', 'signmsg'];
        $this->validateParam($needparam);   //验证必要参数
        $post = $this->post;
        CommFun::checkPartnerTableExists($post['instid']);
        $list = PortfolioConfig::getPortfolioList();
        $list = $this->arrayKeyToLower($list);
        $return = ['code'=>'ETS-5BP0000', 'message'=>'get success', 'list'=>empty($list) ? [] : $list];
        $this->handleCode($return);
    }

    /**
     * 组合申购接口
     */
    public function actionPurchase()
    {
        $needparam = ['instid','signmsg','hcid','bankacco','applysum','tradepassword','portfolioid','orderno'];
        $this->validateParam($needparam);   //验证必要参数
        $post = $this->post;
        //检查商户是否存在
        CommFun::checkPartnerTableExists($post['instid']);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);
        $obj = new PortfolioServiceApi($post['hcid'], $post['instid'], $post['portfolioid']);
        $res = $obj->handlePortfolioPurchase($post);
        $this->handleCode($res['hd_res'],$res['idemp_arr']);
    }

    /**
     * 赎回接口
     */
    public function actionSale()
    {
        $needparam = ['instid','signmsg','hcid','orderno','bankacco','applyshare','tradepassword','portfolioid', 'ratio'];
        $this->validateParam($needparam); //验证必要参数
        $post = $this->post;
        //检查商户是否存在
        CommFun::checkPartnerTableExists($post['instid']);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);
        $obj = new PortfolioServiceApi($post['hcid'], $post['instid'], $post['portfolioid']);
        $res = $obj->handlePortfolioSale($this->post);
        $this->handleCode($res['hd_res'],$res['idemp_arr']);
    }

    /**
     * 撤单接口
     */
    public function actionWithdraw()
    {
        $needparam = ['instid','signmsg','hcid','orderno','portfoliotradeid','portfolioid','tradepassword'];
        $this->validateParam($needparam); ////验证必要参数
        $post = $this->post;

        //检查商户是否存在
        CommFun::checkPartnerTableExists($post['instid']);
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);

        $obj = new PortfolioServiceApi($post['hcid'], $post['instid'], $post['portfolioid']);
        $res = $obj->handlePortfolioWithDraw($post);

        $this->handleCode($res['hd_res'],$res['idemp_arr']);
    }

    //组合交易记录查询
    public function actionTraderecord()
    {
        $needparam = ['instid', 'signmsg', 'hcid'];
        $this->validateParam($needparam);   //验证必要参数

        $post = $this->post;
        //检查商户是否存在
        CommFun::checkPartnerTableExists($post['instid']);
        
        $obj = new PortfolioServiceApi($post['hcid'], $post['instid']);
        $res = $obj->handleTradeRecordQuery($post);

        $this->handleCode($res,[],[],false);
    }
}