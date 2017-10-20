<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.6.3;dbname=db_fund',
            'username' => 'hcfund',
            'password' => 'wrbyD2[nH.M2NYi80Z%uA~x1OQUSY,mj',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            /*'slaveConfig' => [
                'username' => 'hcfund',
                'password' => 'wrbyD2[nH.M2NYi80Z%uA~x1OQUSY,mj',
                'attributes' => [
                    // use a smaller connection timeout
                    PDO::ATTR_TIMEOUT => 10,
                ],
            ],
            'slaves' => [
                ['dsn' => 'mysql:host=192.168.6.3;dbname=db_fund'],
            ],*/
        ],
		'db_local' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.6.3;dbname=db_fund',
            'username' => 'hcfund',
            'password' => 'wrbyD2[nH.M2NYi80Z%uA~x1OQUSY,mj',
            'charset' => 'utf8',
        ],
		'db_juyuan' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.8.3;dbname=jydb',
            'username' => 'jydb',
            'password' => 'kNx89gE7MciHrIkuGKwCZEhduaax0gHj',
            'charset' => 'utf8',
        ],
		'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '192.168.6.2',
            'port' => 6379,
            'database' => 0,
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
                    'logFile' => '/data/log/error/'.date('Ymd').'.log',
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
                        'hundsun',
                        'api'
                    ],
                    'logFile' => '/data/log/info/'.date('Ymd').'.log',
                    'maxFileSize'=>30720,
                    'maxLogFiles'=>2000,
                    'prefix'=>function ($message){ return ''; },
                ],
                [
                'class' =>'yii\log\FileTarget',
                'levels' => ['info'],
                'logVars' => [],
                'categories' => ['hundsun'],//恒生接口日志
                'except' => [],
                'logFile' => '/data/log/info/hundsun_'.date('Ymd').'.log',
                'maxFileSize'=>61440,
                'maxLogFiles'=>2000,
                'prefix'=>function ($message){ return ''; },
                ],
                [
                'class' =>'yii\log\FileTarget',
                'levels' => ['info'],
                'logVars' => [],
                'categories' => ['api'],//商户接口请求响应日志
                'except' => [],
                'logFile' => '/data/log/info/api_'.date('Ymd').'.log',
                'maxFileSize'=>61440,
                'maxLogFiles'=>2000,
                'prefix'=>function ($message){ return ''; },
                ],
            ],
        ],
        // 路由配置
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'bootstrap' => ['log'],
	'modules' => [
		'api'=>['class'=>'frontend\modules\api\ApiModule']
    ],
    'params' => $params,
];

