<?php
namespace backend\controllers;

use Yii;
use backend\models\User;
use common\lib\CommFun;

/**
 * User controller
 */
class UserController extends BaseController
{
    /**
     * user index for user list
     *
     * @return html|json
     */
    public function actionIndex()
    {
        $data = $this->request();
        
        if (Yii::$app->request->isAjax) {
            $model = new User();
            if ($model->load($data)) {
                $rs = $model->userList();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
        
        $instid = Yii::$app->admin->instid;
        // Get register number
        if (Yii::$app->admin->isSuperAdmin) {
            $regNum = User::getRegisterNum($instid);
        }else{
            $regNum = 0;
        }
        $bindNum = User::getBindBankNum($instid);   // Get bind number
        
        $instList = Yii::$app->admin->getInstList($instid);

        return $this->render('index', ['regNum'=>$regNum, 'bindNum'=>$bindNum, 'instList'=>$instList]);
    }

    /**
     * User bind card number
     *
     * @return html|json
     */
    public function actionBindNum($value='')
    {
        $instid = $this->request('instid');

        $rs = User::getBindBankNum($instid);   // Get bind number
        
        return $this->renderJson(['bindNum'=>$rs]);
    }

    /**
     * User detail
     *
     * @return html|json
     */
    public function actionDetail()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new User(['scenario'=>'userDetail']);
            if ($model->load($data)) {
                $rs = $model->userDetail();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        $uid = isset($data['uid']) ? $uid = $data['uid'] : '';
        $instid = isset($data['instid']) ? $data['instid'] : '';

        return $this->render('detail',['uid'=>$uid,'instid'=>$instid]);
    }

    /**
     * User bank info
     *
     * @return html|json
     */
    public function actionUserBank()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new User(['scenario'=>'userBank']);
            if ($model->load($data)) {
                $rs = $model->userBank();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }

        $uid = isset($data['uid']) ? $uid = $data['uid'] : '';
        $instid = isset($data['instid']) ? $data['instid'] : '';

        return $this->render('user-bank',['uid'=>$uid,'instid'=>$instid]);
    }

    /**
     * User change bank records
     *
     * @return html|json
     */
    public function actionChangeBank()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new User(['scenario'=>'userBank']);
            if ($model->load($data)) {
                $rs = $model->userChangeBank();
            } else {
                $rs = $model->errors;
            }

            return $this->renderJson($rs);
        }

        $uid = isset($data['uid']) ? $uid = $data['uid'] : '';
        $instid = isset($data['instid']) ? $data['instid'] : '';

        return $this->render('change-bank',['uid'=>$uid,'instid'=>$instid]);
    }

    /**
     * Freeze user
     *
     * @return json
     */
    public function actionFreeze()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new User(['scenario'=>'modifyAccountStatus']);
            if ($model->load($data)) {
                $rs = $model->modifyAccountStatus();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * Delete user
     *
     * @return json
     */
    public function actionRemove()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new User(['scenario'=>'modifyOpenStatus']);
            if ($model->load($data)) {
                $rs = $model->modifyOpenStatus();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }

    /**
     * Change user bank
     *
     * @return json
     */
    public function actionAuthorization()
    {
        $data = $this->request();

        if (Yii::$app->request->isAjax) {
            $model = new User(['scenario'=>'modifyAuthorization']);
            if ($model->load($data)) {
                $rs = $model->modifyAuthorization();
            } else {
                $rs = $model->errors;
            }
            
            return $this->renderJson($rs);
        }
    }
}
