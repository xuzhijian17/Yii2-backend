<?php
return [
    'adminEmail' => 'admin@example.com',
    'pageSize' => 10,

    'codeinfo' => [
        '0' => '成功',
        '100' => '权限不够',
        '101' => '参数验证失败',
        '102' => '非ajax请求',
        '103' => '用户已开户',
        '104' => '无数据更新',
        '105' => '商户不存在',
        '106' => '用户不存在',
        '107' => '商户已存在',
        '108' => '用户已存在',
        '109' => '密码错误',
        '110' => '记录不存在',
        '111' => '命令执行错误',

        '201' => '基金代码已存在',
        '202' => '基金代码不存在',
        '203' => '基金类型已存在',
        '204' => '基金类型不存在',
        '205' => '组合名称已存在',
        '206' => '组合名称不存在',
        '207' => '资讯内容已存在',
        '208' => '资讯内容不存在',
        '209' => '主题名称已存在',
        '210' => '主题名称不存在',
    ],
    //命令行地址
    'cronUrl'=>[
        'cron_fundinfo'=>'/usr/local/service/php/bin/php /data/release/fund/yii fund/open',//系统开盘
        'cron_confirmback'=>'/usr/local/service/php/bin/php /data/release/fund/yii trade/confirmback',//系统收盘
        'suspend'=>'/usr/local/service/php/bin/php /data/release/fund/yii tool/suspend',//节假日前暂停货基/短期理财申购
        'recover'=>'/usr/local/service/php/bin/php /data/release/fund/yii tool/recover',//节假日后恢复货基/短期理财申购
    ],
];
