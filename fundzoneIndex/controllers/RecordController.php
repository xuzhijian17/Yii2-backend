<?php
namespace fundzone\controllers;

use common\models\PositionProfitloss;
use fundzone\models\TradeOrderZone;
use fundzone\service\PositionService;
use Yii;
/**
 *交易相关处理类
 */
class RecordController extends BaseController
{
    public $uid;//uid属性
    public $instid;

    public function init()
    {
        parent::init();
        $this->isLogin();
        $this->uid = isset($this->user['id'])?$this->user['id']:0;
        $this->instid = $this->user['Instid'];
    }

    //交易记录列表
    public function actionList()
    {
        $tradeOrder = new TradeOrderZone([], $this->instid);
        $where = "Uid='{$this->uid}'";
        $page = $this->get("page", 1);
        $size = 20;
        $count = $tradeOrder->getUserFundCodeEveryDayProfitCount($where);
        $pager = $this->get_pager("/record/list/", [], $count, $page, $size);
        $limit = "LIMIT {$pager['start']},{$pager['size']}";
        $list = $tradeOrder->getCompanyUserRecord($where, "all", "ApplyTime DESC", $limit);
        $page = $page > $pager['page_count'] ? $pager['page_count'] : $page;
        return $this->render('list',['trade_list'=>$list, 'pager'=>$pager, 'page'=>$page]);
    }

    //交易记录详情
    public function actionDetail()
    {
        $id = $this->get('id');

        if (empty($id)) {
            return $this->errPage('参数有误找不到页面');
        }

        $tradeOrder = new TradeOrderZone([], $this->instid);

        $where = "Uid='{$this->uid}' AND id='{$id}'";
        $record = $tradeOrder->getCompanyUserRecord($where, "one");
        if (empty($record)) {
            return $this->errPage('不存在的交易记录');
        }
        return $this->render('detail',['detail'=>$record]);
    }
}
