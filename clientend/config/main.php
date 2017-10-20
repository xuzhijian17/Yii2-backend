<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-clientend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'clientend\controllers',
    'defaultRoute'=>'fund',
    'layoutPath'=>'/',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
//             'enableCookieValidation' => true,   // 是否开启cookie验证，如果开启则需要配置cookieValidationKey
//             'cookieValidationKey' => 'uNs7_mkmHTwOtQo4JgaQw_jxl5j4MinD',    // cookie验证所需要使用的hash密匙
            'enableCsrfValidation' => false,
        ],
        'errorHandler' => [
//            'class' => 'common\lib\ErrorHandler',
             // 'errorAction' => 'site/error',
        ],
        //日志文件配置
        'log' => [
            'traceLevel' => YII_DEBUG? 2 : 0,
            'targets' => [
                [
                    'class' =>'yii\log\FileTarget',
                    'levels' => ['error','warning'],
                    'logVars' => [],
                    'categories' => [],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logFile' => '/data/log/client/error/'.date('Ymd').'.log',
                    'maxFileSize'=>30720,
                    'maxLogFiles'=>2000,
                    'prefix'=>function ($message){ return ''; },
                ],
                [
                    'class' =>'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logVars' => [],
                    'categories' => [],
                    'except' => [
                        'yii\web\HttpException:404',
                        'yii\db\*',
                        'yii\web\Session*',
                    ],
                    'logFile' => '/data/log/client/info/'.date('Ymd').'.log',
                    'maxFileSize'=>30720,
                    'maxLogFiles'=>2000,
                    'prefix'=>function ($message){ return ''; },
                 ],
             ],
         ],
     ],
    'params' => $params,
];
