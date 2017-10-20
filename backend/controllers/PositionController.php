<?php
namespace backend\controllers;

use Yii;
use backend\models\Position;
use backend\models\CastSurely;
use common\lib\CommFun;

/**
 * Position controller
 */
class PositionController extends BaseController
{
    /**
     * Position detail
     */
    public function actionIndex()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Position();
            if ($model->load($data)) {
                $rs = $model->positionDetail();
            } else {
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }

        $uid = isset($data['uid']) ? $uid = $data['uid'] : '';
        $instid = isset($data['instid']) ? $data['instid'] : '';
        $type = isset($data['type']) ? $type = $data['type'] : '';

        return $this->render('index',['uid'=>$uid,'instid'=>$instid,'type'=>$type]);
    }

    /**
     * Daily profit and loss
     */
    public function actionProfitLoss()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Position(['scenario'=>'profitLoss']);
            if ($model->load($data)) {
                $rs = $model->profitLoss();
            } else {
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }

        return $this->render('profit-loss');
    }

    /**
     * Cast surely position
     */
    public function actionCastSurely($value='')
    {
        
    }


    /**
     * Cast surely agreement
     */
    public function actionCastSurelyAgreement($value='')
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new CastSurely(['scenario'=>'CastSurelyAgreement']);
            if ($model->load($data)) {
                $rs = $model->CastSurelyAgreement();
            } else {
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }

        $uid = isset($data['uid']) ? $uid = $data['uid'] : '';
        $instid = isset($data['instid']) ? $data['instid'] : '';
        $type = isset($data['type']) ? $type = $data['type'] : '';

        return $this->render('cast-surely-agreement',['uid'=>$uid,'instid'=>$instid,'type'=>$type]);
    }
}
