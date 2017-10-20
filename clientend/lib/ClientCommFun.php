<?php
namespace clientend\lib;

use Yii;
use common\lib\CommFun;
/**
 *公用方法类(继承使用common\lib\CommFun)
 */
class ClientCommFun extends CommFun
{
    /**
     * 返回值格式化处理
     * @param mixed $code (string)返回码 (array)自定义
     * @param array ['code'=>'返回码','message'=>'信息','data'=>'数据']
     */
    public static function clientHandleCode($code)
    {
        if (is_numeric($code))
        {
            $codeinfo = Yii::$app->params['codeinfo'];
            $data = isset($codeinfo[$code]) ? ['code'=>(string)$code,'message'=>$codeinfo[$code]] : ['code'=>'-1','message'=>$codeinfo['-1']];
        }else if (is_array($code)){
            $data = $code;
        }else {
            $data = ['code'=>'-1999','message'=>'数据格式不正确','data'=>''];
        }
        return $data;
    }
    
}