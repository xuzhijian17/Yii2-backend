<?php
namespace institution\service;

use Yii;
use institution\service\JavaRestful;
/**
 *交易相关逻辑类
 */
class TradeService
{
    private $user;
    const PERPAGE = 20;//每页条数
    /**
     * 构造函数初始化登录session
     * @param array $session
     */
    function __construct($session)
    {
        $this->user = $session;
    }
    /**
     * 交易下单页面数据组合方法
     * @param array $user 登录session
     * @return array ['uncommittedOrder'=>'未下单数据','committedOrder'=>'已下单数据',
     * 'committedTotalNum'=>'已下单总数','committedTotalPage'=>'已下单总页数']
     */
    public function OrderData()
    {
        $uncommittedData = $this->UncommittedOrder();
        $committedData = $this->CommittedOrder();
        return ['uncommittedOrder'=>$uncommittedData,'committedOrder'=>$committedData['list'],
            'committedTotalNum'=>$committedData['totalnum'],'committedTotalPage'=>ceil($committedData['totalnum']/self::PERPAGE)
        ];
    }
    /**
     * 未下单数据
     */
    public function UncommittedOrder()
    {
        $obj = new JavaRestful('T001', ['code'=>$this->user['orgCode'],'userName'=>$this->user['userName']], 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && isset($res['res'])){
            $uncommittedOrderData = array_map(function ($e){
                $tmp = $e;
                //业务类型
                $tmp['typeName'] = $e['type'];//业务名称
                $bs = self::TransformBusiness(1, $e['type']);
                $tmp['typeCode'] = $bs['type'];
                $tmp['typeClass'] = $bs['class'];
                if (strpos($e['type'], ':')!==FALSE){
                    $typeStrArr = explode(':', $e['type']);
                    $tmp['typeName'] = isset($typeStrArr[0])?$typeStrArr[0]:'--';
                    $tmp['originalTypeName'] = isset($typeStrArr[1])?$typeStrArr[1]:'--';//撤单，原业务类型
                    $tmp['originalOrderSeq'] = rtrim($e['extOrderSeq'],'_1');//原指令序号
                }else {
                    $tmp['originalTypeName'] = '';//撤单，原业务类型
                }
                //份额/金额字段order
                if (in_array($bs['type'], ['020','022']))
                {
                    $tmp['order'] = $e['amount'].'元';
                }elseif (in_array($bs['type'], ['024','036'])){
                    $tmp['order'] = $e['shares'].'份';
                }else{
                    $tmp['order'] = empty($e['amount'])?$e['shares'].'份':$e['amount'].'元';
                }
                
                //有巨额赎回字段
//                 if (isset($e['largeRedemptionFlag'])){
//                     $tmp['largeRedemptionFlag'] = $e['largeRedemptionFlag']==0?'撤销':'顺延';
//                 }
                //订单日期
                $pos = strpos($e['cdate'],' ');
                $tmp['cdate'] = $pos===false?$e['cdate']:substr($e['cdate'], 0,$pos);
                return $tmp;
            }, $res['res']);
        }else{
            if ($res['code']!=JavaRestful::SUCC_CODE){
                Yii::error('T001接口返回失败->'.var_export($res,true));
            }
            $uncommittedOrderData = [];
        }
        return $uncommittedOrderData;
    }
    /**
     * 已下单数据
     * @param string $page=1 页码
     * @return array [] 处理数据
     */
    public function CommittedOrder($page=1)
    {
        $obj = new JavaRestful('T002', ['code'=>$this->user['orgCode'],'userName'=>$this->user['userName'],'page'=>$page,'rows'=>self::PERPAGE], 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && isset($res['res']['listObjects'])){
            $committedOrderData = array_map(function ($e){
                $tmp = $e;
                //业务类型
                $tmp['typeName'] = $e['type'];//业务名称
                $bs = self::TransformBusiness(0, $e['typeCode']);
                $tmp['typeClass'] = $bs['class'];
                if ($e['typeCode']=='053' && strpos($e['type'], ':')!==FALSE){
                    $typeStrArr = explode(':', $e['type']);
                    $tmp['typeName'] = isset($typeStrArr[0])?$typeStrArr[0]:'--';
                    $tmp['originalTypeName'] = isset($typeStrArr[1])?$typeStrArr[1]:'--';//撤单，原业务类型
                    $tmp['originalOrderSeq'] = rtrim($e['extOrderSeq'],'_1');//原指令序号
                }else {
                    $tmp['originalTypeName'] = '';
                }
                //份额/金额字段order
                if (in_array($e['typeCode'], ['020','022']))
                {
                    $tmp['order'] = $e['amount'].'元';
                }elseif (in_array($e['typeCode'], ['024','036'])){
                    $tmp['order'] = $e['shares'].'份';
                }else{
                    $tmp['order'] = empty($e['amount'])?$e['shares'].'份':$e['amount'].'元';
                }
                //受理状态
                $statusArr = self::TransformStatus($e['status']);
                $tmp['statusName'] =  $statusArr['name'];
                $tmp['statusClass'] = $statusArr['class'];
                //有巨额赎回字段
                if (isset($e['largeRedemptionFlag'])){
                    $tmp['largeRedemptionFlag'] = $e['largeRedemptionFlag']==0?'撤销':'顺延';
                }
                //订单日期
                $pos = strpos($e['cdate'],' ');
                $tmp['cdate'] = $pos===false?$e['cdate']:substr($e['cdate'], 0,$pos);
                //分红字段
                $tmp['bonus'] = self::TransformBonus($e['bonus']);
                return $tmp;
            }, $res['res']['listObjects']);
            $totalnum = isset($res['res']['total'])?$res['res']['total']:0;
        }else{
            if ($res['code']!=JavaRestful::SUCC_CODE){
                Yii::error('T001接口返回失败->'.var_export($res,true));
            }
            $committedOrderData = [];$totalnum=0;
        }
        return ['list'=>$committedOrderData,'totalnum'=>$totalnum];
    }
    /**
     * 删除未下单数据
     * @param string $ids json格式要删除的id数组
     */
    public function DelOrder($ids)
    {
        $ids = array_map(function ($e){
            return ['id'=>$e];
        }, $ids);
        $obj = new JavaRestful('T004', $ids, 0,true);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE ){
            return ['code'=>$res['code'],'desc'=>''];
        }else {
            Yii::error('T004接口返回失败->'.var_export($res,true));
            return ['code'=>$res['code'],'desc'=>$res['desc']];
        }
    }
    /**
     * 执行未下单数据
     * @param string $ids json格式要删除的id数组
     */
    public function ExcOrder($ids)
    {
        $ids = array_map(function ($e){
            return ['id'=>$e];
        }, $ids);
        $obj = new JavaRestful('T005', $ids, 0,true);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE ){
            return ['code'=>$res['code'],'desc'=>''];
        }else {
            Yii::error('T005接口返回失败->'.var_export($res,true));
            return ['code'=>$res['code'],'desc'=>$res['desc']];
        }
    }
    /**
     * 查询产品
     * @return['代码'=>'名称']
     */
    public function SearchProduct()
    {
        $obj = new JavaRestful('T006',null, 1);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && !empty($res['res'])){
            return $res['res'];
        }else {
            $res['code'] == JavaRestful::SUCC_CODE or Yii::error('T005接口返回失败->'.var_export($res,true));
            return [];
        }
    }
    /**
     * 人工下单
     * @param array $post 提交post参数
     * @param array session 用户session['orgCode'=>'机构代码','userName'=>'用户名']
     */
    public function OrderSave($post)
    {
        $post['orgCode'] = $this->user['orgCode'];
        $post['userName'] = $this->user['userName'];
        $post = array_filter($post);
        $obj = new JavaRestful('T003',[$post], 0,true);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE){
            return ['code'=>$res['code'],'desc'=>''];
        }else{
            Yii::error('T003接口返回失败->'.var_export($res,true));
            return ['code'=>$res['code'],'desc'=>$res['desc']];
        }
    }
    /**
     * 持仓查询
     * @param string $tradeacco 交易账号
     * @return array [['fundcode'=>'基金代码','fundname'=>'基金名称','melonmethod'=>'分红方式','usableremainshare'=>'可用份额']]
     */
    public function SearchPosition($tradeacco)
    {
        $param['orgCode'] = $this->user['orgCode'];
        $param['tradeacco'] = $tradeacco;
        $obj = new JavaRestful('T007', $param, 0);
        $res = $obj->apiRequest();
        if ($res['code']==JavaRestful::SUCC_CODE && isset($res['res'])){
            return array_map(function ($e){
                return ['fundcode'=>$e['fundcode'],'fundname'=>$e['fundname'],'melonmethod'=>($e['melonmethod']==1)?'现金分红':'红利再投资',
                    'usableremainshare'=>$e['usableremainshare']];
            }, $res['res']);
        }else{
            Yii::error('T007接口返回失败->'.var_export($res,true));
            return [];
        }
    }
    /**
     * 上传下单指令&执行
     * @param resource $file post上传excel
     */
    public function UploadExecute($file)
    {
        $excelDir = Yii::$app->params['excelPath'];
        if (!is_dir($excelDir))
        {
            mkdir($excelDir,0777,true);
        }
        $upfile = date('YmdHis').rand(100,999);
        $suffix = strrchr($file['name'],'.');//获取excel的后缀名
        $upfile .= $suffix;//上传文件的名称
        $upfile = $excelDir.$upfile;
        if(move_uploaded_file($file['tmp_name'],$upfile))
        {
            //上传成功
            $excelData = $this->getExcelData($upfile);
            $assembRes = $this->assembleData($excelData);//组装后下单参数
            if ($assembRes['code']==0 && !empty($assembRes['data'])){
                //组装成功 && 调用下单接口
                $obj = new JavaRestful('T003',$assembRes['data'], 0,true);
                $res = $obj->apiRequest();
                if ($res['code']==JavaRestful::SUCC_CODE){
                    return ['code'=>$res['code'],'desc'=>'','data'=>count($assembRes['data'])];
                }else{
                    Yii::error('T003接口返回失败(导入excel下单)->'.var_export($res,true));
                    return ['code'=>$res['code'],'desc'=>$res['desc']];
                }
            }else{
                //文件数据格式错误
                Yii::error('导入下单指令数据格式错误'.var_export($assembRes,true),__METHOD__);
                return ['code'=>'-101','desc'=>empty($assembRes['data'])?'没有正确指令导入':$assembRes['msg']];
            }
        }else {
            //上传失败
            return  ['code'=>'-101','desc'=>'文件上传失败'];
        }
    }
    /**
     * 组装数据
     * @param array $data excel原数据 (注:修改$titleList 使之适应模板title)
     * @return ['code'=>0,'msg'=>'提示信息','data'=>'组装后数据'](注:code非0表示出错)
     */
    public function assembleData($data)
    {
        //模板所必须12项title，数量不足视为格式不正确,有变动修改代码
        $titleList = ['0'=>'账户代码','1'=>'账户名称','2'=>'证券代码','3'=>'证券名称','4'=>'指令金额','5'=>'指令数量',
            '6'=>'巨额赎回','7'=>'分红方式','8'=>'委托方向','9'=>'转入证券代码','10'=>'转入证券名称','11'=>'指令序号'];
        $result = [];
        foreach ($data as $key=>$value)
        {
            if ($key==1){
                $titleList = array_intersect($value, $titleList);
                if (count($titleList)==12){
                    //更换titleList key对应索引号
                    self::changeTitle($titleList);
                }else {
                    return ['code'=>-1,'msg'=>'导入模板数据格式填写不正确','data'=>[]];
                    break;
                }
            }else {
                foreach ($value as $k => $val) {
                    if (isset($titleList[$k])){
                        //判断是否最后一行，标准为'委托方向type'为空
                        if ($titleList[$k]=='type' && empty($val)){
                            continue 2;
                        }
                        // '-' 'N/A'去掉
                        if ($val=='-' || $val=='N/A'){
                            continue;
                        }
                        //保存数组
                        $tmp[$titleList[$k]]=($titleList[$k]=='amount' || $titleList[$k]=='shares')?str_replace(',','',$val):$val;
                        $tmp['orgCode'] = $this->user['orgCode'];
                        $tmp['userName'] = $this->user['userName'];
                    }else {
                        //非必需字段省略
                        continue;
                    }
                }
                $result[] = $tmp;
            }
        }
        return ['code'=>0,'msg'=>'','data'=>$result];
    }
    /**
     * 业务类型转义
     * @param int $type 类型0业务代码 1业务字符串
     * @param string $param 业务代码type=0/业务字符串type=1
     * 业务代码说明 053撤单 020认购 022申购 024赎回 036转换 029分红
     * @return array ['name'=>'业务名称','class'=>'业务样式','type'=>'业务代码']
     */
    public static function TransformBusiness($type,$param)
    {
        if ($type==0)
        {
            switch ($param)
            {
                case '020':
                    $arr = ['name'=>'认购','class'=>'colRG','type'=>'020'];
                    break;
                case '022':
                    $arr = ['name'=>'申购','class'=>'colSG','type'=>'022'];
                    break;
                case '024':
                    $arr = ['name'=>'赎回','class'=>'colSH','type'=>'024'];
                    break;
                case '053':
                    $arr = ['name'=>'撤单','class'=>'colCD','type'=>'053'];
                    break;
                case '036':
                    $arr = ['name'=>'转换','class'=>'colZH','type'=>'036'];
                    break;
                case '029':
                    $arr = ['name'=>'修改分红方式','class'=>'colXG','type'=>'029'];
                    break;
                default:
                    $arr = ['name'=>'--','class'=>'','type'=>''];
            }
        }elseif ($type==1)
        {
            switch ($param)
            {
                case strpos($param, '认购')!==FALSE:
                    $arr = ['name'=>$param,'class'=>'colRG','type'=>'020'];
                    break;
                case strpos($param, '申购')!==FALSE:
                    $arr = ['name'=>$param,'class'=>'colSG','type'=>'022'];
                    break;
                case strpos($param, '赎回')!==FALSE:
                    $arr = ['name'=>$param,'class'=>'colSH','type'=>'024'];
                    break;
                case strpos($param, '撤单')!==FALSE:
                    $arr = ['name'=>$param,'class'=>'colCD','type'=>'053'];
                    break;
                case strpos($param, '转换')!==FALSE:
                    $arr = ['name'=>$param,'class'=>'colZH','type'=>'036'];
                    break;
                case strpos($param, '分红')!==FALSE:
                    $arr = ['name'=>$param,'class'=>'colXG','type'=>'029'];
                    break;
                default:
                    $arr = ['name'=>$param,'class'=>'','type'=>''];
            }
        }
        return $arr;
    }
    /**
     * 状态转义字符
     * @param string $status 受理状态
     * @return array ['name'=>'业务名称','class'=>'业务样式']
     */
    public static function TransformStatus($status)
    {
        switch ($status)
        {
            case strpos($status, '成功')!==FALSE:
                $arr =['name'=>'受理成功','class'=>'colCG'];
            break;
            case strpos($status, '失败')!==FALSE:
                $arr =['name'=>'受理失败','class'=>'colSB'];
            break;
            default:
                $arr =['name'=>$status,'class'=>''];
        }
        return $arr;
    }
    /**
     * 状态转义字符
     * @param string $bonus 分红字段
     * @return string 分红方式
     */
    public static function TransformBonus($bonus)
    {
        switch ($bonus)
        {
            case 0:
                $str = '红利再投资';
                break;
            case 1:
                $str = '现金分红';
                break;
            default:
                $str='';
        }
        return $str;
    }
    /**
     * 获取exce源数据
     * @param string 
     * @return array
     */
    public function getExcelData($pFilename)
    {
        require_once Yii::getAlias('@common').'/lib/excel/PHPExcel.php';
        $objReader = \PHPExcel_IOFactory::createReaderForFile($pFilename);
        $objPHPExcel = $objReader->load($pFilename);
        $objWorksheet  = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }
    /**
     * excel title 数组转化为[0=>'产品代码']=>[0=>'productCode']..
     * @param array $arr
     */
    public static function changeTitle(&$arr)
    {
        foreach ($arr as $key=>$val)
        {
            switch ($val)
            {
                case $val=='账户代码':
                    $arr[$key]='productCode';
                    break;
                case $val=='账户名称':
                    $arr[$key]='productName';
                    break;
                case $val=='指令金额':
                    $arr[$key]='amount';
                    break;
                case $val=='指令数量':
                    $arr[$key]='shares';
                    break;
                case $val=='证券代码':
                    $arr[$key]='fundCode';
                    break;
                case $val=='证券名称':
                    $arr[$key]='fundName';
                    break;
                case $val=='委托方向':
                    $arr[$key]='type';
                    break;
                case $val=='分红方式':
                    $arr[$key]='bonus';
                    break;
                case $val=='巨额赎回':
                    $arr[$key]='largeRedemptionFlag';
                    break;
                case $val=='转入证券代码':
                    $arr[$key]='targetFundCode';
                    break;
                case $val=='转入证券名称':
                    $arr[$key]='targetFundName';
                    break;
                case $val=='指令序号':
                    $arr[$key]='extOrderSeq';
                    break;
            }
        }
    }
}
