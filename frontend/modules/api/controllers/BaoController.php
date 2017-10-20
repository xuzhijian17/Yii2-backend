<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\controllers\BaseController;
use frontend\modules\api\services\TradeServiceApi;
use Yii;
use common\lib\CommFun;

/**
 * 基金汇现金宝类基金对外接口
 *
 * Class BaoController
 * @package frontend\modules\api\controllers
 */
class BaoController extends BaseController
{
    public $single_sum = 50000; //快速赎回单笔限额5万
    public $today_num = 5; //每日快速赎回次数
    public $today_total_sum = 100000; //每日快速总额度

    /**
     * 宝类基金申购
     */
//     public function actionPurchase()
//     {
//         $needparam = [
//                     'instid', 'signmsg', 'hcid', 'orderno', 'bankacco', 'applysum',
//                     'tradepassword','fundcode'
//                     ];
//         $post = $this->post;
//         $this->validateParam($needparam);   //验证必要参数
//         $post['busintype'] = '01';

//         $obj = new TradeServiceApi($post['hcid'], $post['instid'], $post['fundcode']);
//         $res = $obj->handlePurchase($post);
//         $this->handleCode($res, ['orderno' => $post['orderno']]);
//     }

    /**
     * 撤单接口
     */
//     public function actionWithdraw()
//     {
//         $needparam = ['instid', 'signmsg', 'hcid', 'orderno', 'applyserial', 'tradepassword'];
//         $post = $this->post;
//         $this->validateParam($needparam);   //验证必要参数

//         $obj = new TradeServiceApi($post['hcid'], $post['instid']);
//         $res = $obj->handleWithDraw($post);
//         $this->handleCode($res, ['orderno' => $post['orderno']]);
//     }

    /**
     * 宝类T+0赎回
     */
    public function actionSale()
    {
        $needparam = [
                    'instid', 'signmsg', 'hcid', 'orderno', 'bankacco', 'applyshare',
                    'tradepassword', 'fundcode'
                    ];
        $post = $this->post;
        $this->validateParam($needparam);   //验证必要参数
        $post['busintype'] = '01';
        //检测订单是否存在
        CommFun::validateIdempotenceOrder($post['instid'], $post['orderno']);
        $obj = new TradeServiceApi($post['hcid'], $post['instid'], $post['fundcode']);
        $res = $obj->handleSale($post);
        $this->handleCode($res['hd_res'],$res['idemp_arr'], ['orderno' => $post['orderno']]);
    }
}