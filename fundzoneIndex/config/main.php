<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'web-fundzone',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'fundzone\controllers',
    'components' => [
        //user组件 登录控制使用
        'user'=>[
            'identityClass' => 'fundzone\models\UserLogin'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
