<?php
return [
    //恒生接口对应数组
    'hs_relation' => [
        'T001' =>['url'=>'/fundapi/restful/tradereq/fundlist'],//可购买基金列表
        'T002' =>['url'=>'/fundapi/restful/tradereq/prepareorder'],//可买入支付方式列表
        'T003' =>['url'=>'/fundapi/restful/tradereq/purchase'],//基金买入
        'T006' =>['url'=>'/fundapi/restful/tradereq/sale'],//基金卖出
        'T008' =>['url'=>'/fundapi/restful/tradereq/withdrawlist'],//可撤单列表
        'T009' =>['url'=>'/fundapi/restful/tradereq/withdraw'],//撤单
        'P003' =>['url'=>'/fundapi/restful/integrate/login'],//登陆系统
        'P005' =>['url'=>'/fundapi/restful/system/session'],//取会话标示
        'P006' =>['url'=>'/fundapi/restful/system/riskmatch'],//风险评估
        'P007' =>['url'=>'/fundapi/restful/integrate/tradelogin'],//交易登陆
        'S001' =>['url'=>'/fundapi/restful/query/sharelist'],//基金份额查询
        'S002' =>['url'=>'/fundapi/restful/query/todaytradelist'],//当日申请交易查询
        'S003' =>['url'=>'/fundapi/restful/query/histradelist'],//历史交易申请查询
        'S004' =>['url'=>'/fundapi/restful/query/hisconfirmlist'],//历史交易确认查询
        'S007' =>['url'=>'/fundapi/restful/query/netvalue'],//基金净值查询
        'S010' =>['url'=>'/fundapi/restful/query/fundstatequery'],//基金信息查询
        'S021' =>['url'=>'/fundapi/restful/query/tradefarequery'],//交易费率查询
        'S022' =>['url'=>'/fundapi/restful/query/tradelimitquery'],//交易限制查询
        'S034' =>['url'=>'/fundapi/restful/query/discountquery'],//费率折扣查询
        'C037' =>['url'=>'/fundapi/restful/account/meropenacco'],//交易限制查询
        'C011' =>['url'=>'/fundapi/restful/account/modifyuserinfo'],//修改用户信息
        'C010' =>['url'=>'/fundapi/restful/account/getuserinfo'],//读取用户信息
        'C020' =>['url'=>'/fundapi/restful/account/querytradeacco'],//查交易账号
        'B010' =>['url'=>'/fundapi/restful/capital/yeepayopenacco'],//签约
        'I012' =>['url'=>'/fundapi/restful/valuavgr/tradefundlist'],//可定投基金申购
        'I016' =>['url'=>'/fundapi/restful/valuavgr/getvalubanklist'],//可定投支付方式
        'I005' =>['url'=>'/fundapi/restful/valuavgr/getvalulist'],//定投协议列表
        'I006' =>['url'=>'/fundapi/restful/valuavgr/valutrade'],//定投申购
        'I007' =>['url'=>'/fundapi/restful/valuavgr/valutradechange'],//定投申购协议变更
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
    //公共假期非交易日
    'holidays'=>['2016-01-01','2016-01-02','2016-01-03','2016-02-07','2016-02-08','2016-02-09','2016-02-10'
        ,'2016-02-11','2016-02-12','2016-02-13','2016-04-02','2016-04-03','2016-04-04','2016-04-30','2016-05-01'
        ,'2016-05-02','2016-06-09','2016-06-10','2016-06-11','2016-09-15','2016-09-16','2016-09-17','2016-10-01'
        ,'2016-10-02','2016-10-03','2016-10-04','2016-10-05','2016-10-06','2016-10-07'],
];
