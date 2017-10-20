<?php
return [
    //恒生接口对应数组
    'hs_relation' => [
        'T001' =>['url'=>'/fundapi/restful/tradereq/fundlist'],//可购买基金列表
        'T002' =>['url'=>'/fundapi/restful/tradereq/prepareorder'],//可买入支付方式列表
        'T003' =>['url'=>'/fundapi/restful/tradereq/purchase'],//基金买入
        'P003' =>['url'=>'/fundapi/restful/integrate/login'],//登陆系统
        'P005' =>['url'=>'/fundapi/restful/system/session'],//取会话标示
        'P006' =>['url'=>'/fundapi/restful/system/riskmatch'],//风险评估
        'S010' =>['url'=>'/fundapi/restful/query/fundstatequery'],//基金信息查询
        'S021' =>['url'=>'/fundapi/restful/query/tradefarequery'],//交易费率查询
        'S022' =>['url'=>'/fundapi/restful/query/tradelimitquery'],//交易限制查询
    ],
    //恒生接口公共参数
    'pub_params' =>[
        'merid'=>'HCSJ',//商户号
        'channel'=>'1',//交易渠道 1:手机客户端;2:PC客户端;3:网页
        'usertype'=>'p',//投资者类型 p:个人,o:机构
        'signmode'=>'md5', //签名方式
        'version'=>'v1.0',//版本
        'format'=>'json',//响应格式 json:返回JSON;xml:返回XML
        'custtrust'=>'',//自定义委托方式 为空时以字典[默认委托方式]为准
    ],
    'hs_conf'=>[
        'host'=>'http://192.168.1.9:7001',//恒生接口地址
        'pass'=>'20160226',//商户密码
    ],
];
