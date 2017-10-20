<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\lib;

use Yii;
use yii\base\Exception;
use yii\base\ErrorException;
use yii\base\UserException;
use yii\web\HttpException;
/**
 * ErrorHandler handles uncaught PHP errors and exceptions.
 *
 * ErrorHandler is configured as an application component in [[\yii\base\Application]] by default.
 * You can access that instance via `Yii::$app->errorHandler`.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class ErrorHandler extends \yii\base\ErrorHandler
{
    /**
     * Renders an exception using ansi format for console output.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        $error_arr = $this->convertExceptionToArray($exception);
        $log_path = Yii::$app->params['logPath'];
        if(array_key_exists('error',$log_path) && file_exists($log_path['error'])){
            $log_500_path = $log_path['error']."500_".date("Y-m-d").".log";
        }else{
            $log_500_path = Yii::$app->basePath."/runtime/logs/500_".date("Y-m-d").".log";
        }
        $msg   = isset($error_arr['message']) ? $error_arr['message'] : '';
        $fname = isset($error_arr['file']) ? $error_arr['file'] : '';
        $line  = isset($error_arr['line']) ? $error_arr['line'] : '';;
        $type  = isset($error_arr['type']) ? $error_arr['type'] : '';;
        $randCode = $this->selfRand(); 
        $errorinfo = array( 'ckcd'=>$randCode, 
                            'type'  => $type, 
                            'error' => $msg, 
                            'line'  => $line,
                            'file'  => $fname,
                            'requUrl'  => isset(Yii::$app->request->url) ? Yii::$app->request->url : '',
                            );
        $error_str = "【ERROR】".date("Y-m-d H:i:s")." ".json_encode($errorinfo);
        $fp = fopen($log_500_path,'a+');
        fwrite($fp,$error_str."\n");
        fclose($fp);
        
        $returnarr = array('code'=>500, 'message'=>'Request error,try again later.', 'ckcd'=>$randCode);
        echo json_encode($returnarr);
        exit;
    }

    /*
     * 产生一个随机数
     * */
    private function selfRand()
    {
        list($usec, $sec) = explode(' ', microtime());
        $hh =  (float) $sec + ((float) $usec * 100000);
        mt_srand($hh);
        $randval = mt_rand(100,999);
        $randval = date('H').$randval;
        return $randval;
    }
    /**
     * Converts an exception into an array.
     * @param \Exception $exception the exception being converted
     * @return array the array representation of the exception.
     */
    protected function convertExceptionToArray($exception)
    {
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException) {
            $exception = new HttpException(500, 'There was an error at the server.');
        }

        $array = [
            'name' => ($exception instanceof Exception || $exception instanceof ErrorException) ? $exception->getName() : 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        if ($exception instanceof HttpException) {
            $array['status'] = $exception->statusCode;
        }
        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            if (!$exception instanceof UserException) {
                $array['file'] = $exception->getFile();
                $array['line'] = $exception->getLine();
                $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
                if ($exception instanceof \yii\db\Exception) {
                    $array['error-info'] = $exception->errorInfo;
                }
            }
        }
        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }

        return $array;
    }
}
