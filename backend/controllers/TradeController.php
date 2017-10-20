<?php
namespace backend\controllers;

use Yii;
use backend\models\Trade;
use common\lib\CommFun;

/**
 * Trade controller
 */
class TradeController extends BaseController
{
    /**
     * User trade records
     */
    public function actionIndex()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Trade();
            if ($model->load($data)) {
                $rs = $model->tradeList();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        $instid = Yii::$app->admin->instid;
        $instList = Yii::$app->admin->getInstList($instid);

        return $this->render('index',['instList'=>$instList,'type'=>0]);
    }
}
