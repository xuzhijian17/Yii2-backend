<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute'=>'website',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
//             'enableCookieValidation' => true,   // 是否开启cookie验证，如果开启则需要配置cookieValidationKey
//             'cookieValidationKey' => 'uNs7_mkmHTwOtQo4JgaQw_jxl5j4MinD',    // cookie验证所需要使用的hash密匙
            'enableCsrfValidation' => false,
        ],
        'errorHandler' => [
            'class' => 'common\lib\ErrorHandler',
//             'errorAction' => 'site/error',
        ]
     ],
    'params' => $params,
];
