<?php
return [
    //机构端api接口ip/端口
    'JavaServerHost'=>'http://192.168.6.4:8080/cometotc/api',//生产服务器(端口)
    //token记录
    'JavaServerToken'=>'Basic ZGlyZWN0X2FwaV91c2VyOmRpcmVjdF9hcGlfdXNlcl9wc3c=',//机构端token设置
    //请求机构端api接口编号定义(原则上定义规则第一个字母与所属controller首字母相同,后续001递增)，每个接口写好注释 
    'japi'=>[
        'A001'=>'/web/user/login',//登录系统
        'A002'=>'/web/user/updatePwd', //修改密码
        'A003'=>'/web/account/apply', //机构开户提交
        'A004'=>'/web/assetPortfolio/list', //机构开户信息查询
        'A005'=>'/web/account/info', //机构信息查询

        'S001'=>'/web/fund/share', //查询持仓
        'S002'=>'/web/product/name/list', //产品查询
        'S003'=>'/web/order/application', //交易申请查询
        'S004'=>'/web/order/confirmation',//交易确认查询
        'S005'=>'/web/order/bonus', //分红查询

        'F001'=>'/web/fund/product/find/page',//产品超市
        'F002'=>'/web/fund/orgdic/post',//基金公司/机构查询

        'M001'=>'/web/message/list',//消息中心
        
        'T001'=>'/web/user/unhandled/order/list',//未下单查询
        'T002'=>'/web/user/order/list',//已下单查询
        'T003'=>'/web/user/unhandled/order/save',//导入下单指令
        'T004'=>'/web/user/unhandled/order/delete',//删除下单指令
        'T005'=>'/web/user/unhandled/order/send',//执行下单指令
        'T006'=>'/web/order/product/list',//查询产品
        'T007'=>'/web/fund/share/all',//分交易账号持仓查询
    ],
    //上传指令地址
    'excelPath'=>'/data/log/institution/excel/',
];
