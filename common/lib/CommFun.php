<?php
namespace common\lib;

use Yii;
use yii\db\Query;
use yii\base\Exception;
use frontend\models\IdempotenceOrder;

class CommFun
{
    /**
     * 加密解密方法(用于保存交易密码加密 /或身份证等信息)
     * @param string $string 要加密的内容
     * @param string $operation E:加密；D:解密
     * @param string $key 秘钥
     * @param number $expiry 秘钥过期时间
     * @return string
     */
    public static function AutoEncrypt($string, $operation = 'E', $key = 'LhHaHtaNFX', $expiry = 0)
    {
        $ckey_length = 4;
        $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'D' ? substr($string, 0, $ckey_length):
            substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'D' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'D') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                    return substr($result, 26);
                } else {
                    return '';
                }
        }else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
    
    /**
     * 生成订单号
     * @param int $id
     * @return string len=25
     */
    public static function getOrderNo($id)
    {
        $rand = date('ymd').substr(time(),-5).substr(microtime(),2,7);
        $fix = substr(md5($id),0,7);
        return $rand.$fix;
    }
    /**
     * 计算参数起的下个交易日
     * @param string $applyTime 申请时间  yyyy-mm-dd H:i:s
     * @return array $arr=>[0=>'第一个交易日',1=>'第二个交易日',2=>'第三个交易日']yyyy-mm-dd;
     */
    public static function getTradeDays($applyTime)
    {
        $i = 1;//记录日期 
        $j = 0;//记录交易日
        $holidaysArr = Yii::$app->params['holidays'];
        $arrDays = [];
        $stamp_start = strtotime($applyTime);
        while ($i < 20)
        {
            $strDay = "{$i} day";
            $stamp_day = strtotime("{$strDay}",$stamp_start);
            $day = date('Y-m-d',$stamp_day);
            $isWeekEnd = (date('N',$stamp_day) >= 6);
            $isHoliday = in_array($day, $holidaysArr);
            $i++;
            if ($isHoliday || $isWeekEnd){
                continue;
            }else {
                if ($j >2)
                {
                    break;
                }else {
                    $arrDays[] = $day;
                }
                $j++;
            }
        }
        return $arrDays;
    }
    /**
     * 返回下个交易日
     * @param $type 返回格式 0:yyyy-mm-dd;1:m月d日;2:时间戳
     * @param $applyTime 起始日期
     */
    public static function getNextTradeDay($type,$applyTime = '')
    {
        $i = 1;//记录日期
        $nextTDay = '';
        $holidaysArr = Yii::$app->params['holidays'];
        $stamp_start = empty($applyTime)?time():strtotime($applyTime);
        while ($i < 20)
        {
            $strDay = "{$i} day";
            $stamp_day = strtotime("{$strDay}",$stamp_start);
            $day = date('Y-m-d',$stamp_day);
            $isWeekEnd = (date('N',$stamp_day) >= 6);
            $isHoliday = in_array($day, $holidaysArr);
            $i++;
            if ($isHoliday || $isWeekEnd){
                continue;
            }else {
                $nextTDay = $day;
                break;
            }
        }
        if ($type ==0)
        {
            return $nextTDay;
        }elseif ($type ==1){
            return date('m月d日',strtotime($nextTDay));
        }elseif ($type == 2){
            return strtotime($nextTDay);
        }else {
            return $nextTDay;
        }
    }
    /**
     * 返回基金申请所属交易日期 
     * @return string yyyy-mm-dd
     */
    public static function getApplyTradeDay()
    {
        $day = date('Y-m-d');
        $holidaysArr = Yii::$app->params['holidays'];
        if (date('H:i') > '15:00')
        {
            $tradeDay = self::getNextTradeDay(0);
        }else {
            if (in_array($day, $holidaysArr) || date('N') >= 6)
            {
                $tradeDay = self::getNextTradeDay(0);
            }else {
                $tradeDay = $day;
            }
        }
        return $tradeDay;
    }
    /**
     * 返回当前日期上一个交易日
     * @param string $startDay 开始日期 /null为当前日期
     */
    public static function getLastTradeDay($startDay=null)
    {
        $i = -1;
        $holidaysArr = Yii::$app->params['holidays'];
        $lastTDay = '';
        $stamp_start = empty($startDay)?time():strtotime($startDay);
        while ($i > -20)
        {
            $strDay = "{$i} day";
            $i--;
            $stamp_day = strtotime("{$strDay}",$stamp_start);
            $day = date('Y-m-d',$stamp_day);
            $isHoliday = in_array($day, $holidaysArr);
            $isWeekEnd = date('N',$stamp_day) >= 6;
            if ($isHoliday || $isWeekEnd){
                continue;
            }else {
                $lastTDay = $day;
                break;
            }
        }
        return $lastTDay;
    }

    /**
     * 基金风险等级转换
     * @param string $riskevaluation 聚源风险等级代码
     * @return
     */
    public static function toRiskEvaluation($riskevaluation='')
    {
        switch ($riskevaluation) {
            case 3:
                $riskevaluation = '1';
                break;
            case 7:
                $riskevaluation = '2';
                break;
            case 10:
                $riskevaluation = '3';
                break;
            case 15:
                $riskevaluation = '4';
                break;
            case 20:
                $riskevaluation = '5';
                break;
            default:
                $riskevaluation = '99';
                break;
        }

        return $riskevaluation;
    }

    /**
     * 基金风险等级转换
     * @param string $riskevaluation 恒生风险等级代码
     * @return
     */
    public static function toRiskEvaluationHs($riskevaluation='')
    {
        switch ($riskevaluation) {
            case 0:
                $riskevaluation = '1';
                break;
            case 1:
                $riskevaluation = '2';
                break;
            case 2:
                $riskevaluation = '3';
                break;
            case 3:
                $riskevaluation = '4';
                break;
            case 4:
                $riskevaluation = '5';
                break;
            default:
                $riskevaluation = '99';
                break;
        }

        return $riskevaluation;
    }

    /**
    * 数据格式返回统一格式化
    * @param [mixed] code 错误代码，无错误传0或null
    * @param [mixed] data 需要进行格式化的数据
    * @param [mixed] auto 是否为列表数据，以及设置列表数据的字段
    * @return [array] 返回格式化后的数据，也可以直接使用引用参数dataInfo的值
    */
    public static function renderFormat($code = null, $data = [], $message = [])
    {
        // Default infomation
        $codeInfo = Yii::$app->params['codeinfo'];
        $dataInfo = &Yii::$app->params['datainfo'];     // It can be rewrite.
        
        // Validate code
        if ($code || $code == '0') {
            if (array_key_exists($code,$codeInfo)) {
                $dataInfo['error'] = $code;
                $dataInfo['message'] = $codeInfo[$code];
            }else{
                throw new Exception("view code `{$code}` is not found.");
            }
        }

        // The way for format data display.
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) && $key !== 'list') {
                    $dataInfo['list'] = array_merge($dataInfo['list'], [$key => $value]);
                }else{
                    $dataInfo[$key] = $value;   // It's rewrite datainfo `error` and `message` info.
                }
            }
        }

        // Custom message
        if (!empty($message)) {
            if (is_array($message) && isset($message['message'])) {
                if (isset($message['rewrite']) && !$message['rewrite']) {
                    $dataInfo['message'] .= $message['message'];
                }else{
                    $dataInfo['message'] = $message['message'];
                }
            }else{
                $dataInfo['message'] = $message;
            }
        }
        
        return $dataInfo;
    }

    
    /**
     * 验签函数
     * @param array $param 参数数组
     * @return boolean true成功/false失败
     */
    public static function validate($param)
    {
        if(isset($param['signmsg']) && isset($param['instid']))
        {
            $signmsg = $param['signmsg'];
            $partner = self::getPartnerInfo($param['instid']);
            if (empty($partner))
            {
                return false;//配置未定义
            }
            unset($param['signmsg']);
            $secretkey = $partner['PassWord'];
            ksort($param);
            $tokenStr = $secretkey;
            foreach($param as $k=>$v)
            {
                if(!empty($v) || $v == '0')
                {
                    $tokenStr .= $k.$v;
                }
            }
            $tokenStr .= $secretkey;
            if (strtoupper(md5($tokenStr)) == $signmsg)
            {
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /**
     * 获取商户信息partner  redis key=partner_info
     * @param int $instid 商户id
     * @return array ['Instid'=>'商户Id','InstName'=>'商户名称','PassWord'=>'密码','Divide'=>'分成比例']
     */
    public static function getPartnerInfo($instid)
    {
        $redis = Yii::$app->redis;
        $partner = $redis->hget('partner_info',$instid);
        if (empty($partner))
        {
            $db_local = Yii::$app->db_local;
            $rs = $db_local->createCommand("SELECT * FROM `partner` WHERE Instid = {$instid}")->queryOne();
            if (empty($rs))
            {
                return false;
            }else {
                $redis->hset('partner_info',$instid,json_encode($rs));
            }
            return $rs;
        }else {
            return json_decode($partner,true);
        }
    }
    /**
     * 返回格式统一处理(新定义返回码在配置文件中声明codeinfo)
     * @param mixed $code (string)返回码 (array)恒生接口返回数组/$code['custom'] 不为空自定义数组返回
     * @param array ['Instid'=>'商户号','OrderNo'=>'订单号','Oid'=>'业务id','Type'=>'订单类型'] idempotence_order字段数据
     * @param array $param 自定义数组键值对
     * @param int $w false不写日志/true写日志
     * @return array ['code'=>'返回码','message'=>'信息'](注:当code='-1000',message='恒生接口返回'/code='负数',message='配置自定义',code=0成功)
     */
    public static function handleCode($code,$idemp=[],$param=[],$w=TRUE)
    {
		$codeinfo = Yii::$app->params['codeinfo'];
		$exchange = Yii::$app->params['exchange'];
		if(is_array($code)){
		    if (empty($code['custom']))
		    {
		        $data = $code;
		        //恒生接口返回
		        if($code['code'] == 'ETS-5BP0000'){
		            $data['code'] = '0';
		            if (!empty($param)){
		                $data = array_merge($data,$param);
		            }
		        }else{
		            $data['code'] = empty($exchange[$code['code']])?'-1000':$exchange[$code['code']];//恒生返回code转化
		            $data['message'] = !empty($code['message'])? $code['message'] : $codeinfo['-1'];
		        }   
		    }else {
		        //自定义数组返回
		        unset($code['custom']);
		        $data = $code;
		    }
		}else{
			$data = isset($codeinfo[$code]) ? ['code'=>(string)$code,'message'=>$codeinfo[$code]] : ['code'=>'-1','message'=>$codeinfo['-1']];
		}
		$data['message'] = strip_tags($data['message']);
		if (!empty($idemp)){
		    $instid = $idemp['Instid'];
		    $idemp['Code'] = $data['code'];
		    $idemp['Message'] = mb_substr($data['message'],0,40,'utf-8');
		    $idemp['Ctime'] = date('Y-m-d H:i:s');
		    unset($idemp['Instid']);
		    IdempotenceOrder::insert($instid, $idemp);
		}
		if ($w){
		    Yii::info("ip:{$_SERVER['REMOTE_ADDR']} | url:{$_SERVER['REQUEST_URI']} \n response:".var_export($data,true),'api');//记录日志
		}
		exit(json_encode($data,JSON_UNESCAPED_UNICODE));
    }
    /**
     * 订单校验函数 已存在返回处理结果
     * @param string $merid 商户号
     * @param string $orderno 订单号
     */
    public static function validateIdempotenceOrder($merid,$orderno)
    {
        $rs = IdempotenceOrder::find($merid, $orderno);
        if (!empty($rs) && isset($rs['Code']))
        {
            $arr = ['code'=>$rs['Code'],'message'=>$rs['Message'],'custom'=>1];
            self::handleCode($arr);
        }
    }
    /**
     * 订单创建
     * @param string $merid 商户号
     * @param array param ['OrderNo'=>'订单号','Oid'=>'业务主键id','Type'=>'类型 0:开户1:定投2购买;3:赎回;4撤单;5组合购买;
     * 6组合赎回;7组合撤单;8鉴权银行卡',
     * 'Status'=>'状态 -1失败 0:成功 1:完成'] 注意:key值首字母大写(对应表字段)
     */
    public static function createIdempotenceOrder($merid,$param)
    {
        $param['Ctime'] = date("Y-m-d H:i:s");
        IdempotenceOrder::insert($merid, $param);
    }
	
	/** 
	 *  转码json中文正常显示
	 */
	public static function decodeUnicode($str)
	{
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
			create_function(
				'$matches',
				'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
			),
			$str);
	}

    /**
     * PHP 多维数组排序 根据二维数组中某个项排序，例如：multi_array_sort($arr,'age')则是根据$arr二维数组中的age元素进行排序
     * @param [array] $multi_array 排序数组
     * @param [mixed] $sort_key 排序字段
     * @param [const] $sort 排序方式
     */
    public static function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){ 
        if(is_array($multi_array)){
            $key_array = array();
            foreach ($multi_array as $row_array){ 
                if(is_array($row_array)){ 
                    $key_array[] = $row_array[$sort_key]; 
                }else{ 
                    return false; 
                } 
            }
            array_multisort($key_array,$sort,$multi_array); 
        }else{ 
            return false; 
        }
        
        return $multi_array; 
    } 

	/**
	 * 封装处理字段拼装 insert语句
	 * @param array $array 数据库字段键值对
	 * @param mixed $dist null:正常拼装;1:过滤字段值为-404数据(特定函数调用)
	 * @return array ['fields'=>'字段名','values'=>'字段值']
	 */
	public static function JoinInsertStr($array,$dist=NULL)
	{
	    if (empty($array)){
	        return false;
	    }
	    $fieldStr = $valueStr = '';
	    foreach ($array as $key => $value) {
	        if ($value === null || (!empty($dist) && $value == -404))
	        {
	            continue;
	        }else {
	            $fieldStr .='`'.$key.'`,';
	            $valueStr .= "'{$value}',";
	        }
	    }
	    $fieldStr = rtrim($fieldStr,',');
	    $valueStr = rtrim($valueStr,',');
	    return ['fields'=>$fieldStr,'values'=>$valueStr];
	}
	/**
	 * 封装处理字段拼装 update语句 set部分
	 * @param array $array 数据库字段键值对
	 * @param mixed $dist null:正常拼装;1:过滤字段值为-404数据(特定函数调用)
	 * @return string `字段名1`='字段值1',`字段名2`='字段值2'....
	 */
	public static function JoinUpdateStr($array,$dist=NULL)
	{
	    if (empty($array)){
	        return false;
	    }
	    $fieldStr='';
	    foreach ($array as $key => $value) {
	        if (!empty($dist) && $value == -404){
	            continue;
	        }else {
	            $fieldStr .='`'.$key.'` = \''.$value.'\',';
	        }
	    }
	    $fieldStr = rtrim($fieldStr,',');
	    return $fieldStr;
	}
	/**
	 * 获取基金基本信息 对应数据表fund_info
	 * @param string $fundcode 基金代码
	 * @return mixed 为空:false/不为空： array ['FundCode'=>'基金代码','InnerCode'=>'内部代码','FundName'=>'基金名称','ChiSpelling'=>'基金名称','PernetValue'=>'最新净值','TradingDay'=>'净值日期',
     * 'NVDailyGrowthRate'=>'日涨幅','RRInSelectedWeek'=>'本周以来回报率','RRInSingleWeek'=>'近一周回报率','RRInSingleMonth'=>'近一月回报率','RRInSelectedMonth'=>'本月以来回报率',
     * 'RRInThreeMonth'=>'三个月涨幅','RRInSixMonth'=>'六个月涨幅','RRInSingleYear'=>'近一年涨幅','RRSinceThisYear'=>'今年以来涨幅','RRSinceStart'=>'成立以来回报率','DailyProfit'=>'万份收益',
     * 'LatestWeeklyYield'=>'7日年化收益率','FundRiskLevel'=>'基金风险等级0:低;1:中;2:高','FundState'=>'基金状态:0正常1发行2发行成功3发行失败4停止交易
     * 5停止申购6停止赎回7权益登记8红利发放9基金封闭a基金终止','ShareType'=>'收费方式A:前端收费B:后端收费C:其他','DeclareState'=>'申购状态1:可申购',
     * 'SubScribeState'=>'认购状态1:可认购','ValuagrState'=>'定投状态1:可定投','WithDrawState'=>'赎回状态:1可赎回','MinHoldShare'=>'最小持有份额',
     * 'MinRedemeShare'=>'最低赎回份额','MinPurchaseAmount'=>'最低申购金额','MinSubscribAmount'=>'最低认购金额','MinAddPurchaseAmount'=>'申购追加最小值',
     * 'MinValuagrAmount'=>'最低定投金额','MinAddValuagrAmount'=>'最低定投追加金额','ManageFee'=>'管理费','CustodyFee'=>'尾随佣金','MarketFee'=>'营销费','MoneyFund'=>'0:普通产品;1:T+0产品'
     * 'FundTypeCode'=>'1101-股票型;1103-混合型;1105-债券型;1107-保本型;1109-货币型;1199-其他型','FundType'=>'基金类型(描述)','SysTime'=>'更新时间'];
	 */
	public static function GetFundInfo($fundcode)
	{
	    $redis = Yii::$app->redis;
	    $fundInfo = $redis->hget('fund_info',$fundcode);
	    if (empty($fundInfo))
	    {
	        $db_local = Yii::$app->db_local;
	        $rsFundInfo = $db_local->createCommand("SELECT * FROM fund_info WHERE FundCode = '{$fundcode}'")->queryOne();
	        if(empty($rsFundInfo))
	        {
	            return false;
	        }
	        $redis->hset('fund_info',$fundcode,json_encode($rsFundInfo,JSON_UNESCAPED_UNICODE));
	        return $rsFundInfo;
	    }else {
	        return json_decode($fundInfo,true);
	    }
	}


    /**
     * 获取多个基金的简单信息
     * @param $fundcodes 多个基金逗号隔开
     * @return array
     */
    public static function getFundNameByFundCodes($fundcodes)
    {
        if (empty($fundcodes)) {
            return [];
        }
        $db_local = Yii::$app->db_local;
        $rsFundInfo = $db_local->createCommand("SELECT FundCode,FundName,FundState,PernetValue FROM fund_info WHERE FundCode IN ('{$fundcodes}') ")->queryAll();
        if(empty($rsFundInfo)) {
            return [];
        }
        $result = [];
        foreach ($rsFundInfo as $key=>$val) {
            $result[$val['FundCode']] = $val;
        }
        return $result;
    }
    /**
     * 防客户端sql注入攻击 (目前过滤 ''','%','like','../','./',';','_')
     * @param mixed array/string 可以将post数组传入 引用传递
     * 
     */
    public static function clean_xss(&$string)
    {
        if (! is_array ( $string ))
        {
            $string = trim ( $string );
            $string = str_replace ( array ("'", "%", "../", "./",";","_","like"), '', $string );
            return True;
        }
        $keys = array_keys ( $string );
        foreach ( $keys as $key )
        {
            self::clean_xss( $string [$key] );
        }
    }
    /**
     * 判断是否无交易密码商户
     * @param int $instid 商户号
     * @return bool true是/false否
     */
    public static function ckNoPass($instid)
    {
        $noNeedPass = [2,3];//不需要交易密码商户
        if (in_array($instid, $noNeedPass))
        {
            return true;
        }else {
            return false;
        }
    }

    /**
     * 获取基金最低起购金额
     * @param [mixed] $fundCode 基金代码
     * @return [array|false] LowestSumSubLL 最低认购金额下限（元）, LowestSumPurLL 最低申购金额下限（元） 
     */
    public static function getLowestSumLL($fundCode)
    {
        $db_juyuan = Yii::$app->db_juyuan;

        $LowestSumLL = (new Query)
            ->select(['LowestSumSubLL','LowestSumPurLL'])
            ->from('SecuMain')
            ->join('LEFT JOIN', 'MF_FundArchives', 'SecuMain.InnerCode = MF_FundArchives.InnerCode')
            ->where(['SecuCategory' => 8, 'SecuCode'=>$fundCode])
            ->one($db_juyuan)
        ;
        
        return $LowestSumLL;
    }

    /**
     * 检查商户是否存在及相应的商户表是否存在，不存在则直接返回
     * @param $instid
     * @return bool
     */
    public static function checkPartnerTableExists($instid)
    {
        $db_local = Yii::$app->db_local;
        $partners = $db_local->createCommand("SELECT * FROM `partner` WHERE Instid='{$instid}'")->queryOne();
        if (empty($partners)) {
            self::handleCode(-12);
        }
        $needCheckTable = ['fund_position_', 'idempotence_order_', 'portfolio_position_',
                        'portfolio_trade_', 'position_profitloss_', 'trade_order_', 'valutrade_plan_'];
        foreach ($needCheckTable as $table) {
            $table .= $instid;
            $existTable = $db_local->createCommand("SHOW TABLES LIKE '{$table}'")->queryOne();
            if (empty($existTable)) {
                self::handleCode(-13);
            }
        }
        return true;
    }
}