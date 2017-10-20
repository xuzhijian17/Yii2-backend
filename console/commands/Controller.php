<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace console\commands;

use Yii;
use common\lib\CommFun;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author ddz <daidazhi@haotougu.com>
 * @since 2.0
 * 控制器基类
 */
class Controller extends \yii\console\Controller
{
    //const FILE_DIR = "/data/release/fund/frontend/runtime/logs/command/";
	
    public function init(){
	
	}
	
	/**
	 *  记录控制台log日志(废弃不用，今后使用commandLog)
	 */
	public static function writeLog($filepath,$content='',$level="INFO")
	{

		 $log_path_config = Yii::$app->params['logPath'];
				
		 if(array_key_exists('command',$log_path_config) && file_exists($log_path_config['command'])){
			$log_path = $log_path_config['command'];
		 }else{
			return true;
		 }
		
		 $path = $log_path.$filepath.".log";
		 $fp = fopen($path,'a+');
		 
		 $write_content = "【".$level."】"." ".date("Y-m-d H:i:s")." ".$content."\n";
		 
		 fwrite($fp,$write_content);
		 
		 fclose($fp);
		 
		 return true;
	}
	/**
	 * 记录command日志
	 * @param string $msg 记录内容
	 * @param int $level 0:info1;1:error
	 * @param string $fName 日志文件名(命名规则:'function_'.date('Ymd').'.log')
	 */
	public static function commandLog($msg='', $level=0, $fName = '')
	{
	    $logPath=Yii::$app->params['logPath'];
	    $fdir = isset($logPath['command'])?$logPath['command']:'/data/log/command/';
	    if(1 == $level){
	        $fdir .= 'error/';
	    }else if(0 == $level){
	        $fdir .= 'info/';
	    }else {
	        return false;
	    }
	    if(!file_exists($fdir))
	        mkdir($fdir,0777,true);
	    $fName = $fdir.$fName;
	    $msg = date('Y-m-d H:i:s').' '.$msg;
	    $msg .= "\r\n";
	    file_put_contents($fName,$msg,FILE_APPEND);
	}
	/**
	 * 记录各个计划任务最终执行情况
	 * @param array ['Instid'=>'商户id','CommandName'=>'控制器/方法名','CommandStatus'=>'0:未执行1:已执行','Info'=>'其他自定义信息']
	 */
	public function noteCommand($param)
	{
	    $db_local = Yii::$app->db_local;
	    $time = date('Y-m-d H:i:s');
	    $partner = CommFun::getPartnerInfo($param['Instid']);
	    $instName = empty($partner['InstName'])?'':$partner['InstName'];
        if(isset($param['CommandName'])&&!empty($param['CommandName']))
            $commName = $param['CommandName'];
        else
            $commName = $this->id.'/'.$this->action->id; //controllerId/actionId
	    $sql = "INSERT INTO `fund_hzf_commands` (`Instid`,`CommandName`,`LastDealTime`,`InstName`,`CommandStatus`,`Info`) 
	        VALUES ('{$param['Instid']}','{$commName}','{$time}','{$instName}','{$param['CommandStatus']}','{$param['Info']}')";
	    $db_local->createCommand($sql)->execute();
	}
    /*
     *  get channels id
     *  [0,1,2]
     * */
    public static function getCids()
    {
        $rArr = [];
	    $db_local = Yii::$app->db_local;
        $sql = 'select Instid from partner';
        $list = $db_local->createCommand($sql)->queryAll();
        if($list)
        {
            foreach($list as $v) {
                $rArr[] = $v['Instid'];
            }
        }
        return $rArr;
    }
	
}
