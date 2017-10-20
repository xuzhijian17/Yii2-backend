<?php
return [
    //恒生接口对应数组
    'hs_relation' => [
        'T001' =>['url'=>'/fundapi/restful/tradereq/fundlist'],//可购买基金列表
        'T002' =>['url'=>'/fundapi/restful/tradereq/prepareorder'],//可买入支付方式列表
        'T003' =>['url'=>'/fundapi/restful/tradereq/purchase'],//基金买入
        'T006' =>['url'=>'/fundapi/restful/tradereq/sale'],//基金卖出
        'T007' =>['url'=>'/fundapi/restful/tradereq/bonus'],
        'T008' =>['url'=>'/fundapi/restful/tradereq/withdrawlist'],//可撤单列表
        'T009' =>['url'=>'/fundapi/restful/tradereq/withdraw'],//撤单
        'T010' =>['url'=>'/fundapi/restful/tradereq/bonuslist'],
        'T020' =>['url'=>'/fundapi/restful/tradereq/realtimetransfer'],//快速过户
        'P001' =>['url'=>'/fundapi/restful/system/dict'],//读取字典项内容
        'P003' =>['url'=>'/fundapi/restful/integrate/login'],//登陆系统
        'P005' =>['url'=>'/fundapi/restful/system/session'],//取会话标示
        'P006' =>['url'=>'/fundapi/restful/system/riskmatch'],//风险评估
        'P007' =>['url'=>'/fundapi/restful/integrate/tradelogin'],//交易登陆
        'S001' =>['url'=>'/fundapi/restful/query/sharelist'],//基金份额查询
        'S002' =>['url'=>'/fundapi/restful/query/todaytradelist'],//当日申请交易查询
        'S003' =>['url'=>'/fundapi/restful/query/histradelist'],//历史交易申请查询
        'S004' =>['url'=>'/fundapi/restful/query/hisconfirmlist'],//历史交易确认查询
        'S005' =>['url'=>'/fundapi/restful/query/hisbonuslist'],//历史分红查询
        'S006' =>['url'=>'/fundapi/restful/query/marketlist'],//历史基金行情查询
        'S007' =>['url'=>'/fundapi/restful/query/netvalue'],//基金净值查询
        'S010' =>['url'=>'/fundapi/restful/query/fundstatequery'],//基金信息查询
        'S012' =>['url'=>'/fundapi/restful/query/capitaldetail'],//资金流水查询
        'S017' =>['url'=>'/fundapi/restful/query/fundprofitquery'],//基金收益率查询
        'S021' =>['url'=>'/fundapi/restful/query/tradefarequery'],//交易费率查询
        'S022' =>['url'=>'/fundapi/restful/query/tradelimitquery'],//交易限制查询
        'S025' =>['url'=>'/fundapi/restful/query/tradeapplistquery'],//历史交易申请查询
        'S034' =>['url'=>'/fundapi/restful/query/discountquery'],//费率折扣查询
        'S049' =>['url'=>'/fundapi/restful/query/hisprofitquery'],//历史收益查询
        'C004' =>['url'=>'/fundapi/restful/account/queryrisk'],
        'C005' =>['url'=>'/fundapi/restful/account/modifyrisk'],
        'C006' =>['url'=>'/fundapi/restful/account/checkweakpwd'],
        'C010' =>['url'=>'/fundapi/restful/account/getuserinfo'],//读取用户信息
        'C011' =>['url'=>'/fundapi/restful/account/modifyuserinfo'],//修改用户信息
        'C012' =>['url'=>'/fundapi/restful/account/modifypwd'],
        'C020' =>['url'=>'/fundapi/restful/account/querytradeacco'],//查交易账号
        'C029' =>['url'=>'/fundapi/restful/account/resetpwd'],
        'C037' =>['url'=>'/fundapi/restful/account/meropenacco'],//交易限制查询
        'B010' =>['url'=>'/fundapi/restful/capital/yeepayopenacco'],//签约
        'B011' =>['url'=>'/fundapi/restful/capital/yeepaynopwdsign'],
        'B012' =>['url'=>'/fundapi/restful/capital/yeepayremit'],
        'B012' =>['url'=>'/fundapi/restful/capital/yeepayremit'],
        'B016' =>['url'=>'/fundapi/restful/capital/merchangebankcard'], //换卡
        'B040' =>['url'=>'/fundapi/restful/capital/banksendauthcode'], //鉴权发送验证码
        'B041' =>['url'=>'/fundapi/restful/capital/bankverifyauthcode'],    //鉴权验证验证码
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
        'host'=>'http://192.168.8.2:7008',//恒生接口地址(普通请求用)
        'host_cmd' => 'http://192.168.8.3:7008',//恒生接口地址(批量请求用)
        'pass'=>'20160226',//商户密码
        'company_host'=>'http://192.168.8.3:8001',//企业版接口
        'company_pass'=>'20160713',//企业版商户密码
        'company_merid'=>'HCSJ',//企业版商户号
    ],
    //公共假期非交易日
    'holidays'=>['2016-01-01','2016-01-02','2016-01-03','2016-02-07','2016-02-08','2016-02-09','2016-02-10'
        ,'2016-02-11','2016-02-12','2016-02-13','2016-04-02','2016-04-03','2016-04-04','2016-04-30','2016-05-01'
        ,'2016-05-02','2016-06-09','2016-06-10','2016-06-11','2016-09-15','2016-09-16','2016-09-17','2016-10-01'
        ,'2016-10-02','2016-10-03','2016-10-04','2016-10-05','2016-10-06','2016-10-07'],
    //短信配置
    'msg_account' => 'haotouguyj',
    'msg_passwd' => 'B1KTj7xAv8zJ',
    'msg_url' => 'http://120.106818.com/SendMTU8/SendMessage_utf8.aspx',
    
    // 定义默认数据返回格式
    'datainfo' => [
        'error' => 0,
        'message' => '',
        'list'=> []
    ],

    // 定义返回代码信息，-1~-20为公用定义错误代码，之后可分阶段自定义(基金超市:-100~-199,账户类:-200~-299,查询类:-300~-399,交易类:-400~-499)
    'codeinfo' => [
        '0' => '成功',
        '-1' => '错误信息未定义',
        '-2' => '签名信息不正确',
        '-3' => '缺少必要参数',
        '-4' => '此订单请求处理失败',
        '-5' => '此订单请求处理中',
        '-6' => '此订单请求处理完成',
        '-7'=> 'hcid用户不存在',
        '-8'=> '密码错误',
        '-9' => '参数类型不正确',
        '-10' => '权限不够',
        '-11' => '参数验证失败',
        '-12' => '该商户不存在',
        '-13' => '该商户存在异常',
        
        '-100' => '基金代码不存在',
        
        '-200' => '用户银行卡不存在',
        '-201'=>'密码修改失败',
        '-202'=>'登陆失败，身份证号错误',
        '-203'=>'登陆失败，输入密码错误',
        '-204'=>'此用户已开户',
        '-205'=>'该银行编号不存在',
        '-206'=>'支付渠道暂时停用该银行，恢复时间另行通知',
        '-207'=>'需提出换卡申请',
        
        '-402'=>'获取不到该基金申购/认购状态',
        '-403'=>'输入金额/份额需大于零小于一个亿',
        '-404'=>'系统获取不到此基金数据',
        '-405'=>'此基金处于非交易状态',
        '-407'=>'撤单原申请编号不存在',
        '-410'=>'要赎回的基金不存在持仓份额',
        '-411'=>'赎回份额不能大于可用份额',
        '-412'=>'原业务状态不允许撤单',
        '-413'=>'原订单已撤单',
        '-414'=>'请指定申请数值',
        '-415'=>'赎回后持有份额不能低于最低保留份额,请全部赎回',
        '-416'=>'赎回份额不能低于最低赎回份额限制',
        '-417'=>'购买基金不能小于最低起购额',
        
        '-500' => '组合中单只基金的金额小于此基金的最低起购额',
        '-501' => '组合不存在',
        '-504' => '系统获取不到此组合数据',
        '-505' => '申请金额小于组合起购金额',
        '-506' => '系统获取不到此组合交易数据',
        '-507' => '系统获取不到此组合持仓数据',
        '-1000'=>'代销系统内部返回错误 ',
        '-1001'=>'内部服务处理超时',
        '-1002'=>'数据异常'
    ],
    'exchange'=>[
        '1208'=>'-410',//客户无此份额类型的基金
        '1224'=>'-411',//赎回份额大于可用份额
        '1275'=>'-412',//原业务状态不允许撤单
        '1268'=>'-413',//原申请已存在撤单申请，不能重复撤单
        '1149'=>'-414',//请指定申请数值
        '1215'=>'-415',//赎回后帐面份额低于最低保留份额！请全部赎回！
        '0958'=>'-416',//赎回份额不能低于最低赎回份额限制
        '1146'=>'-416',//赎回份额不能低于最低赎回份额限制
        '9997'=>'-208',//验签失败
        '20001'=>'-209',//姓名或身份证号不正确
        '20002'=>'-210',//卡信息或银行预留手机号不正确
        '10002'=>'-211',//开户失败
        '10003'=>'-212',//报文解析异常
        '10005'=>'-213',//流水号重复
        '10006'=>'-214',//商户状态不正常
        '10007'=>'-215',//系统异常
        '10008'=>'-216',//验证次数超限
        '10010'=>'-217',//卡类型有误
        '10011'=>'-218',//不支持的银行卡
        '20003'=>'-219',//身份验证失败
        '20004'=>'-220',//短信验证码错误
        '20005'=>'-221',//请求参数为空
        '20006'=>'-222',//短信验证码已失效
        '2016'=>'-223',//证件在其他TA已开户
    ],
    //易宝配置参数
    'yeepay'=>[
        'url'=>'http://ishanghu.yeepay.com/merchant/fundServlet',//请求地址
        'password'=>'111111',//密码
        'privateKeyFile'=>'/data/release/fund/openssl/private.pem',//私钥地址
        'publicKeyFile'=>'/data/release/fund/openssl/public.cer',//公钥地址
        'mctid'=>'20160122001'//商户id
    ],
    //日志路径
    'logPath'=>['command'=>'/data/log/command/','error'=>'/data/log/error/','info'=>'/data/log/info/','institution'=>'/data/log/institution/'],
    //B端api接口ip/端口
    'BServerHost'=>'http://192.168.6.2',//生产服务器(端口)
    
    //请求B端api接口编号定义
    'apino'=>[
        '1'=>'/api/account/banksendauthcode',//3.1.1银行快捷鉴权
        '2'=>'/api/account/bankverifyauthcode',//3.1.2快捷鉴权验证
        '3'=>'/api/account/meropenacco',//3.1.3开户
        '4'=>'/api/account/modifypwd',//3.1.4修改交易密码
        '5'=>'/api/trade/purchase',// 3.4.1买入基金
        '6'=>'/api/trade/withdraw',//3.4.2撤单申请
        '7'=>'/api/trade/sale',//3.4.3基金卖出
        '8'=>'/api/trade/valutrade',//3.4.4	定投申购协议新增
        '9'=>'/api/trade/valutradechange',//3.4.5	定投申购协议变更
        '10'=>'/api/trade/bonus',//3.4.6	修改分红方式
    ],
];