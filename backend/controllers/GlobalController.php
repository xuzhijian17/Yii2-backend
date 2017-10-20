<?php
namespace backend\controllers;

use backend\models\SystemConfig;
use Yii;
use backend\models\Trade;
use common\lib\CommFun;

/**
 * Global controller
 */
class GlobalController extends BaseController
{
    /**
     * User trade records
     */
    public function actionIndex()
    {
        $data = $this->request();
        $model = new SystemConfig();
        if (Yii::$app->request->isAjax) {


            if ($model->load($data)) {
                $rs = $model->updateConfig($data);
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        $instid = Yii::$app->admin->instid;
        $instList = Yii::$app->admin->getInstList($instid);
        $queryConfig = $model->queryConfig(['id'=>1], $model->getTable());
        return $this->render('index',['instList'=>$instList,'type'=>0, "queryconfig"=>$queryConfig]);
    }
}
