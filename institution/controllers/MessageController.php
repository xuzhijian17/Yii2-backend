<?php
namespace institution\controllers;

use Yii;
// use common\lib\CommFun;
use institution\service\JavaRestful;

/**
 *消息中心相关控制器
 */
class MessageController extends BaseController
{

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
     * 消息中心列表
     */
	public function actionIndex($value='')
	{
		$postData = Yii::$app->request->post();

		if (Yii::$app->request->isAjax) {
			$postData = Yii::$app->request->post();

			$obj = new JavaRestful('M001', $postData, 0);
	        $apiData = $obj->apiRequest();

			return json_encode($apiData,JSON_UNESCAPED_UNICODE);
        }

        $user_login = Yii::$app->session['user_login'];
        $orgCode = isset($user_login['orgCode'])?$user_login['orgCode']:''; //'A001'

        return $this->render('index',['orgCode'=>$orgCode]);
	}

    /**
     * 附件
     */
    public function actionAttach($value='')
    {
        $attachId = Yii::$app->request->get('attach_id');

        // 读取PDF
        $query = new \yii\mongodb\file\Query();
        $rows = $query->where(["_id"=>$attachId])->from('fs')->one();

        header("Content-type: application/pdf");
        echo $rows['file']->toString();
    }
}