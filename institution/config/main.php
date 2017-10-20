<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'web-institution',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'institution\controllers',
    'defaultRoute'=>'account/login',
    'components' => [
        //mongodb
        'mongodb' => [
            'class' => 'yii\mongodb\Connection',
            'dsn' => 'mongodb://192.168.6.4:27017/hcotc',
        ],
        'errorHandler' => [
            'errorAction' => 'exception/error',
        ],
    ],
    'params' => $params,
];
