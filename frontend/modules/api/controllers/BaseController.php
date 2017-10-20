<?php
namespace frontend\modules\api\controllers;

use frontend\modules\api\controllers\ApiController;
use common\lib\CommFun;

class BaseController extends ApiController
{
    public $post = [];  //post接收参数数组
    
    public $noNeedPass = false;//交易密码非必需用户
    /**
     * 初始化方法，加载必要参数
     */
    public function init()
    {
        parent::init();
        $this->post = $this->post();

        if(isset($this->post['instid']) && CommFun::ckNoPass($this->post['instid']) )
        {
            $this->noNeedPass = true;
        }
    }

    /**
     * 获取控制器的类名不包含命名空间
     * @return mixed
     */
    public function getClassRealName()
    {
        $str = substr($this->className(), strrpos($this->className(), '\\'));
        return trim($str, '\\');
    }

    /**
     * 检验必传参数是否为空
     * @param array $param 必传参数
     */
    protected function validateParam ($param)
    {
        foreach($param as $key=>$val){
            if ($this->noNeedPass && $val=='tradepassword'){
                continue;
            }else {
                if(!isset($this->post[$val])){
                    $this->handleCode('-3');
                }
            }
        }
    }

    /*
     * 记录日志到runtime目录下的logs目录
     * @param $data
     */
    protected function write_log($data, $log_type, $method)
    {
        $path = \Yii::$app->getRuntimePath()."/logs/api/".$log_type."_".date("Ymd").".log";
        if (is_object($data) || is_resource($data)) {
            return false;
        }
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        //$log_obj = \Yii::getLogger();
        //$log_obj->log($data, $log_obj::LEVEL_INFO);  //记录到物理文件日志
        //\Yii::info($data);
        $ip = \Yii::$app->getRequest()->userIP;

        $data = date("Y-m-d H:i:s")." [{$ip}] [{$method}] ".$data."\r\n";
        @error_log($data, 3, $path);
    }

    //将数组键值转换成小写
    protected function arrayKeyToLower($list)
    {
        foreach ($list as $key=>$val) {
            $list[$key] = array_change_key_case($val, CASE_LOWER);
        }
        return $list;
    }
}