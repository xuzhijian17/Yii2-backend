<?php

namespace frontend\modules\api\controllers;

use Yii;
use common\lib\CommFun;

/**
 * api module下controller基类
 *
 */
class ApiController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
	public $post=[] ; //post数组


    public function init()
    {
        $this->post =  Yii::$app->request->post();
        Yii::info("ip:{$_SERVER['REMOTE_ADDR']} | url:{$_SERVER['REQUEST_URI']} \n request:".var_export($this->post,true),'api');//记录日志
        CommFun::clean_xss($this->post);
        // 验证公共必传参数
        if (!isset($this->post['instid']) || empty($this->post['signmsg']) || $this->post['instid']==='') {
            $this->handleCode('-3');
        }
        if (!CommFun::validate($this->post))
        {
            $this->handleCode('-2');
        }
    }
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        return true;
    }


	public function get($param = null,$default = null)
	{
		return Yii::$app->request->get($param,$default);
	}

    public function post($param = null,$default = null)
    {
        return $this->post;
    }
	
    public function isPost()
    {
        return Yii::$app->request->getIsPost();
    }
    /**
     * 验签方法
     * @param array $param 参数数组
     * @return boolean true成功/false失败
     */
    public function validate($param)
    {
        return true;
    }
    /**
     * 返回格式统一处理(新定义返回码在配置文件中声明codeinfo)
     * @param mixed $code (string)返回码 (array)恒生接口返回数组
     * @param array ['Instid'=>'商户号','OrderNo'=>'订单号','Oid'=>'业务id','Type'=>'订单类型'] idempotence_order字段数据
     * @param array $param 自定义数组键值对
     * @param int $w false不写日志/true写日志
     * @return array ['code'=>'返回码','message'=>'信息'](注:当code='-1000',message='恒生接口返回'/code='负数',message='配置自定义',code=0成功)
     */
    public function handleCode($code,$idemp=[],$param=[],$w=TRUE)
    {
        CommFun::handleCode($code,$idemp,$param,$w);
    }
    

}
