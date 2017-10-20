<?php
namespace frontend\controllers;

use Yii;
use common\lib\HundSun;
use frontend\models\FundQuery;
use yii\web\Controller;
use yii\helpers\Json;
use frontend\models\FundMarket;
use common\lib\CommFun;

/**
 * Site controller
 */
class FundMarketController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        return true;
    }

    /**
     * Displays homepage.
     *
     * @return html
     */
    public function actionIndex()
    {
        $stockFundData = FundMarket::fundList('stock');

        return $this->renderPartial('index',['stockFundData'=>$stockFundData]);
    }

    /**
     * Fund list data.
     *
     * @return json data
     */
    public function actionFundList($value='')
    {
        $fundType = Yii::$app->request->get('fund_type','');
        $yieldType = Yii::$app->request->get('yield_type','NVDailyGrowthRate');
        $page = Yii::$app->request->get('page',1);

        $fundData = FundMarket::fundList($fundType, $yieldType, $page);

        return Json::encode(CommFun::renderFormat(0,$fundData));
    }

    /**
     * Fund search result data.
     *
     * @return json data
     */
    public function actionSearch($value='')
    {
        $s = Yii::$app->request->get('s');
        // Yii::$app->request->queryParams
        
        if (!$s) {
            return [];
        }

        $rs = FundMarket::search($s);

        return Json::encode(CommFun::renderFormat(0,$rs));
    }
}
