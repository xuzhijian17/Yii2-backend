<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Json;
use backend\behavior\AccessMethod;
use common\lib\CommFun;

/**
 * Base controller
 * @author Xuzhijian17
 */
class BaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init($value='')
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'AccessMethod' => [
                'class' => AccessMethod::className(),
                'except' => ['login','error'],   // Everyone can access action
                'superAdminAction' => ['freeze','remove','authorization','register']   // Just the super admin can access action
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * 1.All non-fatal PHP errors (e.g. warnings, notices) are converted into catchable exceptions. 
     * 2.Exceptions and fatal PHP errors are displayed with detailed call stack information and source code lines in debug  * mode. 
     * 3.Support using a dedicated controller action to display errors. 
     * 4.Support different error response formats. 
     */
    /*public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('//site/error', ['uuid' => md5(uniqid(mt_rand(), true)), 'exception' => $exception]);
        }
    }*/
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        
        return true;
    }


    /**
    * The function is encapsulation Yii request component `post()` and `get()` function.
    *
    * @param [mixed] $name 
    * @param [mixed] $defaultValue
    * @param [mixed] $type request method type
    *
    * @return [array|exit] if the request data is null or empty, then exit json 101 error code
    */
    public function request($name = null, $defaultValue = null, $type = '')
    {
        if ($type == 'post') {
            $requestData = Yii::$app->request->post($name,$defaultValue);
        }elseif($type == 'get'){
            $requestData = Yii::$app->request->get($name,$defaultValue);
        }else{
            $requestData = Yii::$app->request->get($name,$defaultValue) ?: Yii::$app->request->post($name,$defaultValue);
        }
        
        if ($requestData === null) {
            exit(json_encode(CommFun::renderFormat('101',[],['message'=>':'.$name,'rewrite'=>false]),JSON_UNESCAPED_UNICODE));
        }

        return $requestData;
    }

    /**
    * Render json format data
    *
    * @param [mixed] $data Unified format processing
    *
    * @return json data
    */
    public function renderJson($data)
    {
        if (isset($data['error']) && isset($data['message']) && isset($data['list'])) {
            $rs = $data;
        }elseif (is_array($data)) {
            $rs = CommFun::renderFormat('0',$data);
        }elseif ($data === 1 || (is_bool($data) && $data)) {
            $rs = CommFun::renderFormat('0');
        }elseif ($data === 0) {
            $rs = CommFun::renderFormat('104');
        }else{
            $rs = CommFun::renderFormat('-1');
            Yii::error($data);
        }
        
        return Json::encode($rs);
    }
}
