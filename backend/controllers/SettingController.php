<?php
namespace backend\controllers;

use Yii;
use backend\models\Setting;
use common\lib\CommFun;

/**
 * Setting controller
 * @author Xuzhijian17
 */
class SettingController extends BaseController
{
    /**
    * 获取商户信息
    * @return 
    */
    public function actionPartner()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Setting();
            if ($model->load($data)) {
                $rs = $model->getPartner();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }
    }

    /**
    * 设置商户秘钥
    * @return 
    */
    public function actionSecrentKey()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new Setting(['scenario'=>'updateSecretKey']);
            if ($model->load($data)) {
                $rs = $model->updateSecretKey();
            }else{
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }
    }

    /**
     * Modify password
     */
    public function actionModifyPwd()
    {
        $data = $this->request();

        $model = new Setting(['scenario'=>'modifyPwd']);
        if ($model->load($data)) {
            $rs = $model->modifyPassword();
        } else {
            $rs = $model->errors;
        }

        return $this->renderJson($rs);
    }
}
