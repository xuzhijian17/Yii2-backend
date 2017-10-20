<?php
namespace backend\controllers;

use Yii;
use backend\models\BELoginForm;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function init($value='')
    {
        parent::init();
    }

    /**
     * default backend index
     */
    public function actionIndex()
    {
        return $this->redirect(['user/index']);
    }

    /**
     * login
     */
    public function actionLogin()
    {
        $data = $this->request();
        
        if (!Yii::$app->admin->getIsGuest()) {
            return $this->goHome();
        }

        if (Yii::$app->request->isPost) {
            $model = new BELoginForm();
            if ($model->load($data)) {
                return $this->redirect(\yii\helpers\Url::previous(Yii::$app->admin->returnUrlParam));
            } else {
                return $this->renderPartial('login', [
                    'errors' => $model->errors,
                ]);
            }
        }
        
        return $this->renderPartial('login');
    }

    /**
     * Register
     */
    public function actionRegister()
    {
        $data = $this->request();

        $model = new BELoginForm(['scenario'=>'register']);
        if ($model->load($data)) {
            $rs = $model->setPassword();
        } else {
            $rs = $model->errors;
        }

        return $this->renderJson($rs);
    }

    /**
     * logout
     */
    public function actionLogout()
    {
        Yii::$app->session->destroy();

        return $this->goHome();
    }
}
