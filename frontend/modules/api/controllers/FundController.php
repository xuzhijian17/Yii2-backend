<?php
namespace frontend\modules\api\controllers;

use Yii;
use yii\web\User;
use yii\helpers\Json;
use common\lib\CommFun;
use frontend\modules\api\controllers\ApiController;
use frontend\modules\api\models\FundMarket;
use frontend\modules\api\models\FundArchives;

/**
 *基金超市类
 */
class FundController extends ApiController
{
    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * 基金超市
     * @param [string] fund type
     * @return html
     */
    public function actionIndex()
    {
        $fundType = Yii::$app->request->post('fundtype');

        if(!preg_match("/^[0-6]$/",$fundType)){
            $this->handleCode('-9');
        }

        $fundModel = new FundMarket();
        $fundList = $fundModel->fundList($fundType);

        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$fundList],false);
    }

    /**
     * 基金详情
     * @param [string] fund code
     * @return json data
     */
    public function actionDetail($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $fundData = $model->fundDetail($fundCode);

		return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],$fundData,false);
    }


    /**
     * 净值走势K线图
     * @param [string] inner code
     * @return json data
     */
    public function actionNvChart($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');
        $startDay = Yii::$app->request->post('startday');

        if (!$fundCode || !$startDay) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $netValueChart = $model->netValueChart($fundCode,$startDay);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$netValueChart],false);
    }

    /**
     * 同类均值K线图
     * @param [string] inner code
     * @return json data
     */
    public function actionSimilarAvg($value='')
    {
        
    }

    /**
     * 沪深300K线图
     * @param [string] inner code
     * @return json data
     */
    public function actionHs300($value='')
    {
        
    }

    /**
     * 基金费率
     * @param [string] inner code
     * @return json data
     */
    public function actionChargeRate($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');
        $chargeRateType = Yii::$app->request->post('chargeratetype');

        if (!$fundCode || !$chargeRateType) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $chargeRate = $model->chargeRate($fundCode,$chargeRateType);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$chargeRate],false);
    }

    /**
     * 基金费率（新）
     * @param [string] inner code
     * @return json data
     */
    public function actionChargeRateNew($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');
        $chargeRateType = Yii::$app->request->post('chargeratetype');

        if (!$fundCode || !$chargeRateType) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $chargeRate = $model->chargeRateNew($fundCode,$chargeRateType);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$chargeRate],false);
    }

    /**
     * 基金经理
     * @param [string] inner code
     * @return json data
     */
    public function actionManager($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $fundManager = $model->fundManager($fundCode);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$fundManager],false);
    }

    /**
     * 基金档案
     * @param [string] inner code
     * @return json data
     */
    public function actionArchives($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $fundArchives = $model->fundArchives($fundCode);

        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],$fundArchives,false);
    }

    /**
     * 基金投资组合
     * @param [string] inner code
     * @return json data
     */
    public function actionInvestmentPortfolio($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $investmentPortfolio = $model->investmentPortfolio($fundCode);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$investmentPortfolio],false);
    }

    /**
     * 基金（临时）公告
     * @param [string] inner code
     * @return json data
     */
    public function actionBulletin($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');
        $page = Yii::$app->request->post('pageno',1);
        $pageSize = Yii::$app->request->post('applyrecordno',15);
        $type = Yii::$app->request->post('type','1');

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        if ($type == '1') { // 基金临时公告
           $interimBulletin = $model->interimBulletin($fundCode,$page,$pageSize);
        }elseif($type == '2'){  // 基金原文公告
            $interimBulletin = $model->interimAnnouncement($fundCode,$page,$pageSize);
        }else{
            $this->handleCode('-9');
        }

        // 提取totalrecords字段
        $totalrecords = $interimBulletin['totalrecords'];
        unset($interimBulletin['totalrecords']);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$interimBulletin,'totalrecords'=>$totalrecords],false);
    }

    /**
     * 资产负债
     * @param [string] inner code
     * @return json data
     */
    public function actionBalanceSheet($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $balanceSheet = $model->balanceSheet($fundCode);

        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$balanceSheet],false);
    }

    /**
     * 历史净值
     * @param [string] inner code
     * @return json data
     */
    public function actionHistoryNv($value='')
    {
        $fundCode = Yii::$app->request->post('fundcode');
        $startDay = Yii::$app->request->post('startday');
        $endDay = Yii::$app->request->post('endday', date("Y-m-d"));

        if (!$fundCode) {
            $this->handleCode('-9');
        }

        $model = new FundMarket();
        $historyNetValue = $model->historyNetValue($fundCode,$startDay,$endDay);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$historyNetValue],false);
    }
}