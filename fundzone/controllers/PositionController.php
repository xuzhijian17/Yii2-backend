<?php
namespace fundzone\controllers;

use common\models\PositionProfitloss;
use fundzone\service\PositionService;
use Yii;
/**
 *交易相关处理类
 */
class PositionController extends BaseController
{
    public $uid;//uid属性

    public function init()
    {
        parent::init();
        $this->isLogin();
        $this->uid = isset($this->user['id'])?$this->user['id']:0;
    }

    //我的资产
    public function actionAssets()
    {
        $positionObj = new PositionService($this->uid);
        $position = $positionObj->userTotalPosition();
        $position_fund = $positionObj->userPositionList();
        return $this->render('assets',['position'=>$position, 'position_fund'=>$position_fund]);
    }

    //每日收益
    public function actionEveryday()
    {
        $profitlossObj = new PositionProfitloss([], $this->user['Instid']);
        $fundcode = $this->get('fundcode');
        $page = $this->get("page", 1);
        $size = 20;
        if (!empty($fundcode)) {
            $count = $profitlossObj->getUserFundCodeEveryDayProfitCount($this->uid, $fundcode);
            $pager = $this->get_pager("/position/everyday/", ['fundcode'=>$fundcode], $count, $page, $size);
            $list = $profitlossObj->getUserFundCodeEveryDayProfit($this->uid, $fundcode, $pager['start'], $pager['size']);
        } else {
            $count = $profitlossObj->getUserEveryDayProfitCount($this->uid);
            $pager = $this->get_pager("/position/everyday/", [], $count, $page, $size);
            $list = $profitlossObj->getUserEveryDayProfit($this->uid, $pager['start'], $pager['size']);
        }
        $page = $page > $pager['page_count'] ? $pager['page_count'] : $page;
        return $this->render('everyday',['everyday_list'=>$list, 'pager'=>$pager, 'page'=>$page]);
    }
}
