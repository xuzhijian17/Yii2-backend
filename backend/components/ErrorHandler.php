<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\components;

use Yii;
use yii\base\Exception;
use yii\base\ErrorException;
use yii\base\UserException;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\lib\CommFun;

/**
 * ErrorHandler handles uncaught PHP errors and exceptions.
 *
 * ErrorHandler displays these errors using appropriate views based on the
 * nature of the errors and the mode the application runs at.
 *
 * ErrorHandler is configured as an application component in [[\yii\base\Application]] by default.
 * You can access that instance via `Yii::$app->errorHandler`.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Timur Ruziev <resurtm@gmail.com>
 * @since 2.0
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * @var integer maximum number of source code lines to be displayed. Defaults to 19.
     */
    public $maxSourceLines = 19;
    /**
     * @var integer maximum number of trace source code lines to be displayed. Defaults to 13.
     */
    public $maxTraceSourceLines = 13;
    /**
     * @var string the route (e.g. 'site/error') to the controller action that will be used
     * to display external errors. Inside the action, it can retrieve the error information
     * using `Yii::$app->errorHandler->exception. This property defaults to null, meaning ErrorHandler
     * will handle the error display.
     */
    public $errorAction;
    /**
     * @var string the path of the view file for rendering exceptions without call stack information.
     */
    public $errorView = '@yii/views/errorHandler/error.php';
    /**
     * @var string the path of the view file for rendering exceptions.
     */
    public $exceptionView = '@yii/views/errorHandler/exception.php';
    /**
     * @var string the path of the view file for rendering exceptions and errors call stack element.
     */
    public $callStackItemView = '@yii/views/errorHandler/callStackItem.php';
    /**
     * @var string the path of the view file for rendering previous exceptions.
     */
    public $previousExceptionView = '@yii/views/errorHandler/previousException.php';
    /**
     * @var array list of the PHP predefined variables that should be displayed on the error page.
     * Note that a variable must be accessible via `$GLOBALS`. Otherwise it won't be displayed.
     * Defaults to `['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION']`.
     * @see renderRequest()
     * @since 2.0.7
     */
    public $displayVars = ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'];

    public $uuid;
    public $type;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->uuid = md5(uniqid(mt_rand(), true));
        
        parent::init();
    }

    /**
     * Renders the exception.
     * @param \Exception $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        $useErrorView = $response->format === Response::FORMAT_HTML && !YII_DEBUG;
        
        // Judge response format
        if ($response->format === Response::FORMAT_HTML) {
            if (YII_ENV_TEST || isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                // AJAX request
                $response->data = '<pre>' . $this->htmlEncode(static::convertExceptionToString($exception)) . '</pre>';
            } else {
                // if there is an error during error rendering it's useful to
                // display PHP error in debug mode instead of a blank screen
                if (YII_DEBUG) {
                    ini_set('display_errors', 1);
                }
                $file = $useErrorView ? $this->errorView : $this->exceptionView;
                $response->data = $this->renderFile($file, [
                    'uuid' => $this->uuid,
                    'exception' => $exception
                ]);
            }
        } elseif ($response->format === Response::FORMAT_JSON) {
            $response->data = CommFun::renderFormat('-1');
        } elseif ($response->format === Response::FORMAT_RAW) {
            $response->data = static::convertExceptionToString($exception);
        } else {
            $response->data = $this->convertExceptionToArray($exception);
        }
        
        // Set response status code
        if ($exception instanceof HttpException) {
            $response->setStatusCode($exception->statusCode);
        } else {
            $response->setStatusCode(500);
        }
        
        // Logger error message(except 404 error)
        if (!YII_DEBUG && !$exception instanceof NotFoundHttpException) {
            $array = [
                'code' => $exception->getCode(),
                'name' => $this->getExceptionName($exception) ?: 'Exception',
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'type' => get_class($exception),
                'url' => Yii::$app->request->absoluteUrl,
                'uuid' => $this->uuid,
                // 'exception' => $this->convertExceptionToArray($exception),
            ];
            $errorMessage = json_encode($array,JSON_UNESCAPED_UNICODE);

            // Send mail with ErrorException
            if ($exception instanceof ErrorException) {
                Yii::$app->log->targets['email']->enabled = true;
                Yii::error($errorMessage,'FatalErrors');
            }

            Yii::error($errorMessage,__METHOD__);
        }
        
        $response->send();
    }
}
