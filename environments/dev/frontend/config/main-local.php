<?php

$config = [
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        //聚源库
        'db_juyuan' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.1.14;dbname=jydb',
            //'dsn' => 'sqlsrv:Server=192.168.1.12;Database=JYDB',
            'username' => 'jydb',
            'password' => 'jydb',
            'charset' => 'utf8',
        ],
        //本地库
        'db_local' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.1.9;dbname=fund',
            'username' => 'fundtest',
            'password' => 'fund123',
            'charset' => 'utf8',
        ],
        //redis 缓存
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '192.168.1.9',
            'port' => 6379,
            'database' => 0,
        ],
        //日志文件配置
        'log' => [
            'traceLevel' => YII_DEBUG? 3 : 0,
            'targets' => [
                [
                    'class' =>'yii\log\FileTarget',
                    'levels' => ['error', 'warning','info'],
                ]
            ],
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
