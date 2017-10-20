<?php
namespace institution\controllers;

use institution\lib\InstCommFun;
use institution\service\RecordService;
use Yii;
use institution\service\JavaRestful;
/**
 *交易/持仓记录相关控制器
 */
class RecordController extends BaseController
{
    public $uid;//uid属性
    public $orgCode;
    public $title;
    public $apply_busname_map;
    public $confim_busname_map;
    public $confirm_state;

    public function init()
    {
        parent::init();
        $this->isLogin();
        $user_login = Yii::$app->session['user_login'];
        $this->orgCode = $user_login['orgCode'];
        $this->apply_busname_map = ['subscribe'=>'认购','purchase'=>'申购','sale'=>'赎回','convert'=>'基金转换','withdraw'=>'撤销申请','dividend'=>'设置分红方式'];
        $this->confim_busname_map = ['subscribe'=>'认购结果','purchase'=>'申购确认','sale'=>'赎回确认','force_add'=>'强制调增','force_red'=>'强制调减','convert_in'=>'基金转换入','convert_out'=>'基金转换出','dividend'=>'设置分红方式'];

        $this->confirm_state = ['success'=>'确认成功','fail'=>'确认失败','behavior'=>'行为确认','withdraw'=>'已撤销交易','unhandle'=>'未处理'];
    }

    //持仓查询
    public function actionPosition()
    {

        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];

        $tradeacco = $this->get('tradeacco');

        $order = $this->get('order');
        $by = $this->get('by', 'desc');
        $url = $order_url = $product_url = $export_url = "/record/position/?";
        if (!empty($order)) {
            $product_url = $url."order={$order}&by={$by}&";
            $export_url .= "order={$order}&by={$by}&";
        }
        if (!empty($tradeacco)) {
            $order_url = $url."tradeacco={$tradeacco}&";
            $export_url .= "tradeacco={$tradeacco}";
        }
        $recordService = new RecordService();
        $product_list = $recordService->QueryProduct($orgCode);
        $position = $recordService->QueryPosition($orgCode, $tradeacco, $order, $by);
        return $this->render('position', ['position'=>$position, 'product_list'=>$product_list, 'order_url'=>$order_url, 'product_url'=>$product_url, 'export_url'=>trim($export_url, '&')]);
    }

    //导出持仓数据
    public function actionExportposition()
    {
        $tradeacco = $this->get('tradeacco');

        $order = $this->get('order');
        $by = $this->get('by', 'desc');

        $this->xls_header("用户持仓数据");
        $xls_str = "账户全称 \t 产品简称 \t产品代码 \t 持仓份额 \t 可用份额 \t 参考市值(元) \t 未付收益(元) \t 参考盈亏 \t  参考收益率 \n";

        $recordService = new RecordService();
        $user_login = Yii::$app->session['user_login'];
        $orgCode = $user_login['orgCode'];
        $position = $recordService->QueryPosition($orgCode, $tradeacco, $order, $by);
        foreach ($position as $k=>$v) {
            $currentremainshare = InstCommFun::number_format($v['currentremainshare']);
            $usableremainshare = InstCommFun::number_format($v['usableremainshare']);
            $marketvalue = InstCommFun::number_format($v['marketvalue']);
            $unpaid = number_format($v['unpaidincome'], 2);
            $floating = InstCommFun::number_format($v['floating']);
            $yiedrate = $v['yieldrate']*100;
            $xls_str .= "{$v['extname']} \t {$v['fundname']} \t=\"{$v['fundcode']}\" \t {$currentremainshare} \t {$usableremainshare} \t {$marketvalue} \t {$unpaid} \t {$floating} \t  {$yiedrate} \n";
        }
        echo mb_convert_encoding($xls_str, "gb2312", "utf-8");
        exit;
    }
    
    //交易查询
    public function actionOrder()
    {
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $apply_list = $recordService->QueryTradeApplyList();
        $confirm_list = $recordService->QueryTradeConfirmList();
        $bonus_list = $recordService->QueryTradeBonusList();
        return $this->render('order', ['accomap'=>$tradeaccomap,
            'applylist'=>$apply_list,
            'confirmlist'=>$confirm_list,
            'bonuslist'=>$bonus_list,
            'confim_busname_map'=>$this->confim_busname_map,
            'apply_busname_map' => $this->apply_busname_map,
            'confirm_state'=>$this->confirm_state,
            ]
        );
    }

    //交易申请异步查询
    public function actionTradeapply()
    {
        $return = ['code'=>-1, 'message'=>'', 'html'=>''];
        if (!$this->isAjax()) {
            exit(json_encode($return));
        }
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $page = $this->post("page", 1);
        $page = $page<1 ? 1 :$page;
        $args = [];
        $tradeacco = $this->post("tradeacco");
        $startdate = $this->post("startdate");
        $enddate = $this->post("enddate");
        $product = $this->post("product");
        $orderseq = $this->post("orderseq");
        $state = $this->post("state");
        $busName = $this->post("business");
        if (!empty($busName) && isset($this->apply_busname_map[$busName])) {
            $args['busName'] = $this->apply_busname_map[$busName];
        }
        if (!empty($tradeacco)) {
            $args['accountCode'] = $tradeacco;
        }
        if (!empty($startdate)) {
            $args['sdate'] = date("Ymd", strtotime($startdate));
        }
        if (!empty($enddate)) {
            $args['edate'] = date("Ymd", strtotime($enddate));
        }
        if (!empty($product)) {
            $args['fundcode'] = $product;
        }
        if (!empty($orderseq)) {
            $args['orderseq'] = $orderseq;
        }
        if (!empty($state)) {
            if($state == "valid") {
                $args['appType'] = "有效";
            }elseif($state == "invalid"){
                $args['appType'] = "无效";
            }else{
                $args['appType']  = "未校验";
            }
        }
        $apply_list = $recordService->QueryTradeApplyList($args, $page);
        $html = $this->renderPartial('recordapply', ['accomap'=>$tradeaccomap,
            'applylist'=>$apply_list,
            'apply_busname_map' => $this->apply_busname_map
        ]);
        $return['code'] = 1;
        $return['html'] = $html;
        exit(json_encode($return));
    }

    //导出交易申请数据
    public function actionExportapply()
    {
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $page = $this->get("page", 1);
        $page = $page<1 ? 1 :$page;
        $args = [];
        $tradeacco = $this->get("tradeacco");
        $startdate = $this->get("startdate");
        $enddate = $this->get("enddate");
        $product = $this->get("product");
        $orderseq = $this->get("orderseq");
        $state = $this->get("state");
        $busName = $this->get("business");
        if (!empty($busName) && isset($this->apply_busname_map[$busName])) {
            $args['busName'] = $this->apply_busname_map[$busName];
        }
        if (!empty($tradeacco)) {
            $args['accountCode'] = $tradeacco;
        }
        if (!empty($startdate)) {
            $args['sdate'] = date("Ymd", strtotime($startdate));
        }
        if (!empty($enddate)) {
            $args['edate'] = date("Ymd", strtotime($enddate));
        }
        if (!empty($product)) {
            $args['fundcode'] = $product;
        }
        if (!empty($orderseq)) {
            $args['orderseq'] = $orderseq;
        }
        if (!empty($state)) {
            if($state == "valid") {
                $args['appType'] = "有效";
            }elseif($state == "invalid"){
                $args['appType'] = "无效";
            }else{
                $args['appType']  = "未校验";
            }
        }

        $recordService = new RecordService();
        $apply_list = $recordService->QueryTradeApplyList($args, $page, 0);
        $this->xls_header("用户交易申请");
        $xls_str = "账户全称 \t 产品简称 \t 产品代码 \t 业务名称 \t 申请(金额/份额) \t 申请状态 \t 申请时间 \t ";
        $xls_str .= "申请编号 \t 原申请编号 \t  转入基金简称 \t  转入基金代码 \t 巨额赎回标志 \t 指令序号 \t 分红方式\n";
        foreach ($apply_list['list'] as $k=>$value) {
            $tradeacco = isset($tradeaccomap[$value['tradeacco']])?$tradeaccomap[$value['tradeacco']]:'';
            if (strpos($value['businflagStr'], "赎回") !== false || strpos($value['businflagStr'], "转换") !== false) {
                $money = InstCommFun::money_format($value['applyshare'],2,'.',' ').' 份';
            }else{
                $money = InstCommFun::money_format($value['applysum'],2,'.',' ').' 元';
            }
            $applydate = date("Y-m-d", strtotime($value['applydate']));
            if (!isset($value['mintredeem']) || $value['mintredeem'] ===""){
                $is_redemp = "--";
            }else{
                $is_redemp = $value['mintredeem'] == 1? '继续赎回' : '取消';
            }
            if (!isset($value['melonmethod']) || $value['melonmethod'] ===""){
                $bonus = "--";
            }else{
                $bonus = $value['melonmethod'] == 1? '现金分红' : '红利再投资';
            }
            $xls_str .= "{$tradeacco} \t {$value['fundname']} \t";
            $xls_str .= "=\"{$value['fundcode']}\" \t {$value['businflagStr']} \t {$money} \t {$value['kkstat']} \t {$applydate} \t";
            $xls_str .= "=\"{$value['applyserial']}\" \t";
            $xls_str .= "=\"{$value['originalapplyserial']}\" \t {$value['targetfundname']} \t {$value['targetfundcode']} \t {$is_redemp} \t";
            $xls_str .= "=\"{$value['orderseq']}\" \t {$bonus}\n";
        }
        echo mb_convert_encoding($xls_str, "gb2312", "utf-8");
        exit;
    }

    //交易确认异步查询
    public function actionTradeconfirm()
    {
        $return = ['code'=>-1, 'message'=>'', 'html'=>''];
        if (!$this->isAjax()) {
            exit(json_encode($return));
        }
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $page = $this->post("page", 1);
        $page = $page<1 ? 1 :$page;
        $args = [];
        $tradeacco = $this->post("tradeacco");
        $startdate = $this->post("startdate");
        $enddate = $this->post("enddate");
        $product = $this->post("product");
        $orderseq = $this->get("orderseq");
        $state = $this->post("state");
        $busName = $this->post("business");
        if (!empty($busName) && isset($this->confim_busname_map[$busName])) {
            $args['busName'] = $this->confim_busname_map[$busName];
        }
        if (!empty($tradeacco)) {
            $args['accountCode'] = $tradeacco;
        }
        if (!empty($startdate)) {
            $args['sdate'] = date("Ymd", strtotime($startdate));
        }
        if (!empty($enddate)) {
            $args['edate'] = date("Ymd", strtotime($enddate));
        }
        if (!empty($product)) {
            $args['fundcode'] = $product;
        }
        if (!empty($orderseq)) {
            $args['orderseq'] = $orderseq;
        }
        if (!empty($state) && isset($this->confirm_state[$state])) {
            $args['confirmType'] = $this->confirm_state[$state];
        }
        $confirm_list = $recordService->QueryTradeConfirmList($args, $page);
        $html = $this->renderPartial('recordconfirm', ['tradeaccomap'=>$tradeaccomap,
            'confirmlist'=>$confirm_list,
            'confim_busname_map'=>$this->confim_busname_map
        ]);
        $return['code'] = 1;
        $return['html'] = $html;
        exit(json_encode($return));
    }

    //导出交易确认数据
    public function actionExportconfirm()
    {
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $page = $this->get("page", 1);
        $page = $page<1 ? 1 :$page;
        $args = [];
        $tradeacco = $this->get("tradeacco");
        $startdate = $this->get("startdate");
        $enddate = $this->get("enddate");
        $product = $this->get("product");
        $orderseq = $this->get("orderseq");
        $state = $this->get("state");
        $busName = $this->get("business");
        if (!empty($busName) && isset($this->confim_busname_map[$busName])) {
            $args['busName'] = $this->confim_busname_map[$busName];
        }
        if (!empty($tradeacco)) {
            $args['accountCode'] = $tradeacco;
        }
        if (!empty($startdate)) {
            $args['sdate'] = date("Ymd", strtotime($startdate));
        }
        if (!empty($enddate)) {
            $args['edate'] = date("Ymd", strtotime($enddate));
        }
        if (!empty($product)) {
            $args['fundcode'] = $product;
        }
        if (!empty($orderseq)) {
            $args['orderseq'] = $orderseq;
        }
        if (!empty($state) && isset($this->confirm_state[$state])) {
            $args['confirmType'] = $this->confirm_state[$state];
        }

        $recordService = new RecordService();
        $confirm_list = $recordService->QueryTradeConfirmList($args, $page, 0);

        $this->xls_header("用户交易确认");
        $xls_str = "  账户全称 \t 产品简称 \t 产品代码 \t 业务名称 \t 确认(金额/份额) \t 确认状态 \t 申请时间 \t 确认时间 \t ";
        $xls_str .= "申请编号 \t 转入基金简称 \t  转入基金代码 \t 申请(金额/份额) \t 单位净值(元) \t 手续费(元) \t 分红方式\n";
        $cbp = $this->confim_busname_map;

        foreach ($confirm_list['list'] as $k=>$value) {
            $tradeacco = isset($tradeaccomap[$value['tradeacco']])?$tradeaccomap[$value['tradeacco']]:'';
            if (strpos($value['businflagStr'], "赎回") !== false
                || strpos($value['businflagStr'], "转换出") !== false
                || strpos($value['businflagStr'], "调增") !== false
                || strpos($value['businflagStr'], "调减") !== false) {
                $money = InstCommFun::money_format($value['tradeconfirmshare'],2,'.',' ').' 份';
            }else{
                $money = InstCommFun::money_format($value['tradeconfirmsum'],2,'.',' ').' 元';
            }
            $applydate = date("Y-m-d", strtotime($value['applydate']));
            $confirmdate = date("Y-m-d", strtotime($value['confirmdate']));
            if (!isset($value['melonmethod']) || $value['melonmethod'] ===""){
                $bonus = "--";
            }else{
                $bonus = $value['melonmethod'] == 1? '现金分红' : '红利再投资';
            }
            if (strpos($value['businflagStr'], "赎回") !== false
                || strpos($value['businflagStr'], "转换") !== false) {
                $applymoney = InstCommFun::money_format($value['requestshares'],2,'.',' ').' 份';
            }else{
                $applymoney = InstCommFun::money_format($value['requestbalance'],2,'.',' ').' 元';
            }
            $xls_str .= "{$tradeacco} \t {$value['fundname']} \t=\"{$value['fundcode']}\" \t {$value['businflagStr']} \t {$money} \t {$value['confirmflag']} \t {$applydate} \t {$confirmdate} \t";
            $xls_str .= "=\"{$value['applyserial']}\" \t {$value['targetfundname']} \t {$value['targetfundcode']} \t ";
            $xls_str .= "{$applymoney} \t {$value['netvalue']} \t {$value['poundage']} \t  {$bonus}\n";
        }
        echo mb_convert_encoding($xls_str, "gb2312", "utf-8");
        exit;
    }

    //分红异步查询
    public function actionTradebonus()
    {
        $return = ['code'=>-1, 'message'=>'', 'html'=>''];
        if (!$this->isAjax()) {
            exit(json_encode($return));
        }
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $page = $this->post("page", 1);
        $page = $page<1 ? 1 :$page;
        $args = [];
        $tradeacco = $this->post("tradeacco");
        $startdate = $this->post("startdate");
        $enddate = $this->post("enddate");
        if (!empty($startdate)) {
            $args['sdate'] = date("Ymd", strtotime($startdate));
        }
        if (!empty($enddate)) {
            $args['edate'] = date("Ymd", strtotime($enddate));
        }
        if (!empty($tradeacco)) {
            $args['accountCode'] = $tradeacco;
        }

        $bonus_list = $recordService->QueryTradeBonusList($args, $page);
        $html = $this->renderPartial('recordbonus', [
            'tradeaccomap'=>$tradeaccomap,
            'bonuslist'=>$bonus_list
        ]);
        $return['code'] = 1;
        $return['html'] = $html;
        exit(json_encode($return));
    }

    //导出分红数据
    public function actionExportbonus()
    {
        $recordService = new RecordService();
        $tradeaccomap = $recordService->QueryProduct($this->orgCode);
        $page = $this->get("page", 1);
        $page = $page<1 ? 1 :$page;
        $args = [];
        $tradeacco = $this->get("tradeacco");
        $startdate = $this->get("startdate");
        $enddate = $this->get("enddate");
        if (!empty($tradeacco)) {
            $args['accountCode'] = $tradeacco;
        }
        if (!empty($startdate)) {
            $args['sdate'] = date("Ymd", strtotime($startdate));
        }
        if (!empty($enddate)) {
            $args['edate'] = date("Ymd", strtotime($enddate));
        }

        $recordService = new RecordService();
        $bonus_list = $recordService->QueryTradeBonusList($args, $page, 0);

        $this->xls_header("用户分红");
        $xls_str = "账户全称 \t 产品简称 \t 产品代码 \t 分红方式 \t 红利（金额/份额） \t 发放日 \t 确认日期 \t 权益登记日 \t ";
        $xls_str .= "登记份额 \t 实发金额（元）\n";

        foreach ($bonus_list['list'] as $k=>$value) {
            $tradeacco = isset($tradeaccomap[$value['tradeacco']])?$tradeaccomap[$value['tradeacco']]:'';
            $meloncutting = date("Y-m-d", strtotime($value['meloncutting']));
            $confirmdate = date("Y-m-d", strtotime($value['confirmdate']));
            $enrolldate  = date("Y-m-d", strtotime($value['enrolldate']));
            if (!isset($value['melonmethod']) || $value['melonmethod'] ===""){
                $bonus = "--";
            }else{
                $bonus = $value['melonmethod'] == 1? '现金分红' : '红利再投资';
            }
            $enrollshare = InstCommFun::money_format($value['enrollshare'],2,'.',' ');
            $factbonussum = InstCommFun::money_format($value['factbonussum'],2,'.',' ');
            $xls_str .= "{$tradeacco} \t {$value['fundname']} \t=\"{$value['fundcode']}\" \t {$bonus} \t {$value['bonusshare']} \t {$meloncutting} \t";

            $xls_str .= " {$confirmdate} \t {$enrolldate} \t {$enrollshare} \t {$factbonussum}\n";
        }
        echo mb_convert_encoding($xls_str, "gb2312", "utf-8");
        exit;
    }

    //excel 设置输出头
    public function xls_header($file_name = "export")
    {
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            $file_name = urlencode($file_name);
            $file_name = str_replace("+", "%20", $file_name);
        }
        header ( "Expires: 0" );
        header ( 'Content-Type: application/vnd.ms-excel');
        header ( 'Content-Disposition: attachment;filename="' . $file_name . date('Y-m-d',time()). '.xls"' );
        header ( 'Cache-Control: max-age=0' );
        header ( 'Cache-Control: max-age=1' );
        header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
        header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
        header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
        header ( 'Pragma: public' ); 						// HTTP/1.0
    }

}