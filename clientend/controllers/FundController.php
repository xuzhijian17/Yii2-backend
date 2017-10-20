<?php
namespace clientend\controllers;

use Yii;
use clientend\models\Fund;

/**
 *基金页面相关controller
 */
class FundController extends BaseController
{
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
    * 首页
    */
    public function actionIndex($value='')
    {
        $recommend = Fund::getRecommend();
        $recommendTheme = Fund::getRecommendTheme();

    	return $this->renderPartial('index',['recommend'=>$recommend?:[],'recommendTheme'=>$recommendTheme?:[]]);
    }

    /**
    * 热销基金列表页
    */
    public function actionHotList($value='')
    {
        $recommend = Fund::getRecommend();

        return $this->renderPartial('hot-list',['recommend'=>$recommend?:[]]);
    }

    /**
    * 主题基金列表页
    */
    public function actionThemeList($value='')
    {
        $recommendTheme = Fund::getRecommendTheme();

        return $this->renderPartial('theme-list',['recommendTheme'=>$recommendTheme?:[]]);
    }

    /**
    * 主题详情页
    */
    public function actionThemeDetail($value='')
    {
        $tid = Yii::$app->request->get('tid');

        $themeData = Fund::getThemeDetail($tid);

        var_dump($themeData);
    }

    /**
    * 基金详情页
    */
    public function actionFundDetail($value='')
    {
        $fundCode = Yii::$app->request->get('fundCode','000496');

        $detailData = Fund::getFundDetail($fundCode);
        $netValueData = Fund::getNetValue($fundCode);
        $historyNetValue = Fund::getHistoryNetValue($fundCode);

        var_dump($netValueData);
    }

    /**
    * 基金超市页
    */
    public function actionFundMarket($value='')
    {
        $fundType = Yii::$app->request->get('fund_type','');
        $page = Yii::$app->request->get('page',1);

        $rs = Fund::fundList();
        var_dump($rs);
        
    }
}