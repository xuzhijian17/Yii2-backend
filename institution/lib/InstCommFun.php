<?php
/**
 * Created by PhpStorm.
 * User: hcsj_employee
 * Date: 2016/8/18
 * Time: 19:31
 */

namespace institution\lib;
use common\lib\CommFun;
use Yii;

class InstCommFun extends CommFun
{
    //将api返回的字符串数字，重新格式化
    public static function number_format($number, $is_plus=0)
    {
        $number = str_replace(',', '', $number);
        $number_s = number_format($number, 2, '.', ',');
        if ($is_plus && $number>0) {
            $number_s = "+".$number_s;
        }
        return  $number_s;
    }

    public static function money_format($number, $decimals=0, $dec_point='.', $thousands_sep=',')
    {
        $number = is_numeric($number) ? $number : 0;
        $val =  number_format($number, $decimals, $dec_point, $thousands_sep);
        return $val;
    }
    
    /**
     * 记录日志
     * @param string $msg 记录内容
     * @param int $level 0:info1;1:error
     * @param string $fName 日志文件名(命名规则:'function_'.date('Ymd').'.log')
     */
    public static function wLog($msg='', $level=0, $fName = '')
    {
        $logPath=Yii::$app->params['logPath'];
        $fdir = isset($logPath['institution'])?$logPath['institution']:'/data/log/institution/';
        if(1 == $level){
            $fdir .= 'error/';
        }else if(0 == $level){
            $fdir .= 'info/';
        }else {
            return false;
        }
        if(!file_exists($fdir)){
            mkdir($fdir,0777,true);
        }
        $fName = empty($fName)?$fdir.date('Ymd').'.log':$fdir.$fName;
        $msg = date('Y-m-d H:i:s').' '.$msg;
        $msg .= "\n";
        file_put_contents($fName,$msg,FILE_APPEND);
    }
    /**
     * 基金查询自动完成
     * @param string $needle 传入搜索字符串
     * @return array ['基金代码'=>['FundCode'=>'基金代码','FundName'=>'基金名称','ChiSpelling'=>'简写首字母']]
     */
    public static function autocComplete($needle)
    {
        $retArr = []; //符合条件的数组返回
        $fundlist = Yii::$app->redis->hvals('fund_info');
        if (!empty($fundlist))
        {
            foreach ($fundlist as $value)
            {
                //循环所有基金，将符合条件的组合返回
                $info = json_decode($value,true);
                if (!empty($info)){
                    foreach ($info as $key=>$val)
                    {
                        if (in_array($key, ['FundCode','FundName','ChiSpelling']) && stripos($val, $needle) ===0)
                        {
                            //发现符合条件基金
                            $retArr[$info['FundCode']] = ['FundCode'=>$info['FundCode'],'FundName'=>$info['FundName'],'ChiSpelling'=>$info['ChiSpelling']];
                        }
                    }
                }
            }
        }
        return $retArr;
    }

    //
    public static function getApplyTypeColor($busname)
    {
        if (strpos($busname, "认购") !== false) {
            return 'colRG';
        } else if (strpos($busname, "申购") !== false) {
            return 'colSG';
        } else if (strpos($busname, "赎回") !== false) {
            return 'colSH';
        } else if (strpos($busname, "转换") !== false) {
            return 'colZH';
        } else if (strpos($busname, "撤销") !== false) {
            return 'colCD';
        } else if (strpos($busname, "分红") !== false) {
            return 'colXG';
        }
        return "";
    }

    public static function getConfirmTypeColor($busname)
    {
            if (strpos($busname, "认购") !== false) {
                return 'colRG';
            } else if (strpos($busname, "申购") !== false) {
                return 'colSG';
            } else if (strpos($busname, "赎回") !== false) {
                return 'colSH';
            }else if (strpos($busname, "转换") !== false) {
                return 'colZH';
            } else if (strpos($busname, "强制") !== false) {
                return 'colCD';
            } else if (strpos($busname, "分红") !== false) {
                return 'colXG';
            }
        return "";
    }
}