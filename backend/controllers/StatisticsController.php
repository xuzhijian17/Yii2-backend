<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\behavior\AccessMethod;
use backend\models\Statistics;
use backend\models\Position;
use common\lib\CommFun;

/**
 * Statistics controller
 */
class StatisticsController extends BaseController
{
    /**
     * 统计查询
     */
    public function actionIndex()
    {
        $data = $this->request();
        
        
        if (Yii::$app->request->isAjax) {
            $model = new Statistics();
            if ($model->load($data)) {
                $rs = $model->everydayStatistics();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        $instid = Yii::$app->admin->instid;
        
        $instList = Yii::$app->admin->getInstList($instid);

        return $this->render('index',['instList'=>$instList]);
    }

    /**
     * 总量统计
     */
    public function actionTotalStatistics()
    {
        $data = $this->request();
        
        
        if (Yii::$app->request->isAjax) {
            $model = new Statistics();
            if ($model->load($data)) {
                $rs = $model->baseStatistics();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }


    /**
     * 用户持仓（统计）
     */
    public function actionPosition()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new Statistics();
            if ($model->load($data)) {
                $rs = $model->userStatistics();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
        
        $instid = Yii::$app->admin->instid;
        
        $instList = Yii::$app->admin->getInstList($instid);

        return $this->render('position',['instList'=>$instList]);
    }
}
