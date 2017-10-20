<?php
namespace frontend\modules\api\controllers;

use Yii;
use yii\web\User;
use yii\helpers\Json;
use common\lib\CommFun;
use frontend\modules\api\controllers\ApiController;
use frontend\modules\api\models\FundMarketV2;

/**
 *基金超市类
 */
class FundV2Controller extends ApiController
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
     * 自定义基金分类列表
     * @param [string] fund type
     * @return html
     */
    public function actionIndex()
    {
        $merid = Yii::$app->request->post('instid');
        $cid = Yii::$app->request->post('cid',0);
        $tid = Yii::$app->request->post('tid',0);
        $hot = Yii::$app->request->post('hot',0);
        $page = Yii::$app->request->post('pageno',1);
        $pagesize = Yii::$app->request->post('applyrecordno',15);

        $model = new FundMarketV2();
        $fundList = $model->getCFundList($merid, $cid, $tid, $hot, $page, $pagesize);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$fundList],false);
    }

    /**
     * 基金分类
     * @return json
     */
    public function actionCategorys()
    {
        $instid = Yii::$app->request->post('instid');

        $model = new FundMarketV2();
        $data = $model->getFundCats($instid);

        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$data],false);
    }

    /**
     * 主题分类
     * @return json
     */
    public function actionThemes()
    {
        $instid = Yii::$app->request->post('instid');

        $model = new FundMarketV2();
        $data = $model->getFundThemes($instid);
        
        return $this->handleCode(['code'=>'ETS-5BP0000','message'=>''],[],['list'=>$data],false);
    }


}