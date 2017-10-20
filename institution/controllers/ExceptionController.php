<?php
namespace institution\controllers;

use Yii;
use yii\web\Controller;

class ExceptionController extends Controller
{
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            Yii::error($exception,__METHOD__);
            return $this->render('error', ['exception' => $exception]);
        }
    }
}