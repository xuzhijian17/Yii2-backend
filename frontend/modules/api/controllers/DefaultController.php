<?php

namespace frontend\modules\api\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
		//echo "test";
		//var_dump($_SERVER);
        return $this->render('index');
    }
}
