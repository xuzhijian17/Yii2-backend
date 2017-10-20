<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\controllers\BaseController;
use frontend\modules\api\services\QueryServiceApi;
use frontend\modules\api\services\TradeServiceApi;
use Yii;

/**
 * B端查询接口
 * 包含查询份额、查询交易申请、查询交易确认信息等
 */
class QueryController extends BaseController
{
    /**
     * 用户基金份额查询
     */
    public function actionSharequery()
    {
        $needparam = ['instid', 'signmsg', 'hcid'];
        $post = $this->post;
        $this->validateParam($needparam);   //验证必要参数

        $obj = new QueryServiceApi($post['hcid'], $post['instid']);
        $res = $obj->handleShareQueryV2($post);

        $this->handleCode($res,[],[],false);
    }

    /**
     * 用户交易申请查询
     * applyrecordno 每页交易记录数
     */
    public function actionTradeapply()
    {
        $needparam = ['instid', 'signmsg', 'hcid', 'applyrecordno'];
        $this->validateParam($needparam);   //验证必要参数

        $post = $this->post;
        $obj = new QueryServiceApi($post['hcid'], $post['instid']);
        $res = $obj->handleTradeApplyQuery($post);

        $this->handleCode($res);
    }

    /**
     *  用户交易确认查询
     */
    public function actionConfirmquery()
    {
        $needparam = ['instid', 'signmsg', 'hcid', 'applyrecordno'];
        $this->validateParam($needparam);   //验证必要参数

        $post = $this->post;
        $obj = new QueryServiceApi($post['hcid'], $post['instid']);
        $res = $obj->handleTradeConfirmQuery($post);

        $this->handleCode($res);
    }

    /**
     * 交易记录查询
     */
    public function actionTraderecord()
    {
        $needparam = ['instid', 'signmsg', 'hcid'];
        $this->validateParam($needparam);   //验证必要参数

        $post = $this->post;
        $obj = new QueryServiceApi($post['hcid'], $post['instid']);
        $res = $obj->handleTradeRecordQuery($post);

        $this->handleCode($res,[],[],false);
    }
    /**
     * 历史分红查询
     */
    public function actionHisbonuslist()
    {
        $needparam = ['instid', 'signmsg', 'hcid','applyrecordno'];
        $this->validateParam($needparam);   //验证必要参数
        $post = $this->post;
        $obj = new QueryServiceApi($post['hcid'], $post['instid']);
        $res = $obj->getHisBonusList($post);
        $this->handleCode($res);
    }
    /**
     * 订单状态查询
     */
    public function actionGetorderstatus()
    {
        $this->validateParam(['orderno']);   //验证必要参数
        $post = $this->post;
        $res = QueryServiceApi::GetOrderStatus($post);
        $this->handleCode($res);
    }
    /**
     * 查询交易限制条件
     */
    public function actionGetlimitinfo()
    {
        $this->validateParam(['fundcode']);   //验证必要参数
        $post = $this->post;
        $res = QueryServiceApi::GetLimitInfo($post['fundcode']);
        $this->handleCode($res);
    }

    /*
     * 基金历史现金分红
     */
    public function actionFunddividend()
    {
        $needparam = ['instid', 'signmsg', 'fundcode'];
        $this->validateParam($needparam);   //验证必要参数

        $post = $this->post;
        $obj = new QueryServiceApi(0, $post['instid'], $post['fundcode']);
        $res = $obj::getFundDividendList($post['fundcode']);
        $this->handleCode($res);
    }
}