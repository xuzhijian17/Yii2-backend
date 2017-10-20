<?php
namespace backend\controllers;

use Yii;
use backend\models\MoneyFund;
use backend\models\Position;
use common\lib\CommFun;

/**
 * Money Fund controller
 */
class MoneyFundController extends BaseController
{
    /**
     * index
     */
    public function actionIndex()
    {
        $data = $this->request();

        $instid = isset($data['instid']) ? $data['instid'] : '';
        $instList = Yii::$app->admin->getInstList($instid);

        return $this->render('//trade/index',['instList'=>$instList,'type'=>1]);
    }
}
