<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',  //使用redis来保存缓存
            'redis' => 'redis',
        ],
        'session' => [
            'class' => 'yii\web\CacheSession',  //使用Cache组件来保存session
            'timeout'=>3600,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => true,   // 是否开启cookie验证，如果开启则需要配置cookieValidationKey
            'cookieValidationKey' => 'uNs7_mkmHTwOtQo4JgaQw_jxl5j4MinD',    // cookie验证所需要使用的hash密匙
            'enableCsrfValidation' => false,
        ],
        'response' => [
            // 'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'admin' => [
            'class' => 'backend\components\Admin',
            'loginUrl' => ['site/login'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/error-'.date("Y-m-d").'.log',
                    'logVars' => [],
                    'except' => [
                        'yii\web\HttpException:404',    // 记录除404以外的所有错误
                        'FatalErrors'    // FatalErrors分类的错误不记录（主要是为了避免重复记录）
                    ],
                ],
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/fatal-error.log',
                    'fileMode' => 0775,
                    'logVars' => [],
                    'categories' => ['FatalErrors'],
                ],
                'email' => [
                    'class' => 'yii\log\EmailTarget',
                    'levels' => ['error'],
                    'categories' => ['FatalErrors'],
                    'message' => [
                        'charset'=>'UTF-8', 
                        'from' => ['xuzhijian17@qq.com'=>'admin'],
                        'to' => ['309942223@qq.com','xuzj@haotougu.com'],
                        'subject' => 'bg.51jijinhui.com - error log message',
                        'view'=>'text',
                        'textBody'=>'xxx'
                    ],
                    'enabled' => false
                ],
            ],
        ],
        'errorHandler' => [
            // 'class' => 'backend\components\ErrorHandler',
            'errorView' => '@backend/views/site/error.php',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'view' => [
            'title' => '基金汇管理后台',
        ]
    ],
    'params' => $params,
];
