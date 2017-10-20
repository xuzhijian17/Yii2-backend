<?php
namespace common\lib;

use common\models\FundInfo;
use common\models\FundPosition;
use console\commands\Controller;
use frontend\models\TradeOrder;
use Yii;
/**
 * 该接口主要用于对B端商家生成文件对账单
 * B端用sftp方式获取到相应接口的文件对账单进行对账
 * 目前主要包含以下几个接口：
 * 1.产品日净值文件
 * 2.基金交易确认文件
 * 3.基金收益明细文件-货币基金
 * 4.用户资产变更同步文件-非货币基金
 *
 * */
class FileInterfaceOrder
{
    public $instid;  //B端id
    public $version; //对账文件版本号，默认为1
    public $ftp_dir = "D:/mi/"; //sftp的目录
    public $fund_nav_filename;      //产品日净值文件名称
    public $fund_confirm_filename;  //基金交易确认文件名称
    public $fund_income_filename;   //基金收益明细文件-货币基金
    public $fund_assets_change_filename; //用户资产变更同步文件-非货币基金

    public static $TradeTypeMap = ["0"=>"122", "1"=>"124"];

    /*
     * 构造方法
     * FileInterfaceOrder constructor.
     * @param $instid
     * @param $version
     */
    public function __construct($instid, $version='1')
    {
        $this->instid = $instid;
        $this->version = $version;
        $date = date("Ymd");
        $this->date = date("Ymd");
        $this->fund_nav_filename = $this->ftp_dir."fundinfo/{$date}/{$instid}_product_daily_{$date}.txt";
        $this->fund_confirm_filename = $this->ftp_dir."trade/{$date}/{$instid}_trade_confirm_{$date}.txt";
        $this->fund_income_filename = $this->ftp_dir."trade/{$date}/{$instid}_user_dividend_{$date}.txt";
        $this->fund_assets_change_filename = $this->ftp_dir."trade/{$date}/{$instid}_user_profit_{$date}_1.txt";

        $this->_mkdir();
    }


    /**
     * 每日导出基金文件
     * @return bool
     */
    public function executeExportNav()
    {
        $fp = fopen($this->fund_nav_filename, 'w');
        if (empty($fp)) {
            $this->noteCommand("FileInterfaceOrder/executeExportNav", 0, "open file failed ".$this->fund_nav_filename);
            return false;
        }
        $count = FundInfo::getFundInfoCount(""); //基金总笔数
        $fund_list = FundInfo::getFundInfoList("", "all"); //库里所有基金

        $str = "version:{$this->version}|总笔数:{$count}\r\n";
        $str .= "导出日期|产品代码|产品名称|净值日期|净值|7日年化收益率|每万份收益|当天收益率|本周以来收益率|本月以来收益率";
        $str .= "|本季以来收益率|本年以来收益率|成立以来收益率|最近一周收益率|最近一月收益率|最近一季收益率|最近一年收益率\r\n";
        fputs($fp, $str);
        if (empty($fund_list)) {
            $this->noteCommand("FileInterfaceOrder/executeExportNav", 1, "get data is empty ".$this->fund_nav_filename);
            fclose($fp);
            return false;
        }
        
        foreach ($fund_list as $key=>$value) {
            $str = "{$this->date}|{$value['FundCode']}|{$value['FundName']}|{$value['TradingDay']}|{$value['PernetValue']}|{$value['LatestWeeklyYield']}|{$value['DailyProfit']}|";
            $str .= "{$value['NVDailyGrowthRate']}|{$value['RRInSelectedWeek']}|{$value['RRInSelectedMonth']}|本季以来收益率|{$value['RRSinceThisYear']}|";
            $str .= "{$value['RRSinceStart']}|{$value['RRInSingleWeek']}|{$value['RRInSingleMonth']}|{$value['RRInThreeMonth']}|{$value['RRInSingleYear']}\r\n";
            fputs($fp, $str);
        }
        $this->noteCommand("FileInterfaceOrder/executeExportNav", 1, "export data success ".$this->fund_nav_filename);
        fclose($fp);
        return true;
    }

    //基金每日交易确认文件
    public function executeExportConfirm()
    {
        $fp = fopen($this->fund_confirm_filename, 'w');
        if (empty($fp)) {
            $this->noteCommand("FileInterfaceOrder/executeExportConfirm", 0, "open file failed ".$this->fund_confirm_filename);
            return false;
        }
        $tradeOrder = new TradeOrder([], $this->instid);

        /*目前条件为空，这块涉及到按自然日给数据还是按工作日给数据的问题*/

        $where = "1=1";
        $count = $tradeOrder->getTradeOrderCount($where);    //当日确认的笔数
        $trade_list = $tradeOrder->query($where, "all");

        $str = "version:{$this->version}|总笔数:{$count}|总基金账户交易确认份数:0.00|总每笔交易确认金额:0.00\r\n";

        $str .= "第三方理财账号|基金公司交易账号|外部电商申请单据流水号|基金公司申请单流水号|业务类型|基金代码|交易发生时间|";
        $str .= "交易确认日期|交易所属日期|享受收益日期|收费方式|申请金额|成交单位净值|申请赎回基金份数|基金账户交易确认份数|";
        $str .= "每笔交易确认金额|手续费|交易处理返回代码|交易处理返回描述\r\n";
        fputs($fp, $str);
        if (empty($trade_list)) {
            $this->noteCommand("FileInterfaceOrder/executeExportConfirm", 1, "get data is empty ".$this->fund_confirm_filename);
            fclose($fp);
            return false;
        }

        foreach ($trade_list as $key=>$value) {
            $business_type = isset(self::$TradeTypeMap[$value['TradeType']]) ? self::$TradeTypeMap[$value['TradeType']] : "";

            $ConfirmTime = date("Ymd", strtotime($value['ConfirmTime']));
            $return_code = 9999;
            if ($value['TradeStatus'] == TradeOrder::TS_CONFIRM_FAIL) { //确认失败
                $return_code = 4001;
            } else if ($value['TradeStatus'] == TradeOrder::TS_CONFIRM_SUCCESS) { //确认成功
                $return_code = 0000;
            } else if ($value['TradeStatus'] == TradeOrder::TS_WITHDRAW) { //撤单
                $return_code = 4002;
            }
            $user_bank = $this->getUserBankMap($value['Uid']);
            $third_account = isset($user_bank[$value['TradeAcco']]) ? $user_bank[$value['TradeAcco']]['CdCard'] : ''; //
            $profit_date = date("Y-m-d", strtotime($value['ConfirmTime']));
            $fund_info = CommFun::GetFundInfo($value['FundCode']);
            $share_type = '';
            if ($fund_info['ShareType'] == 'A') {
                $share_type = 0; //前端
            } else {
                $share_type = 1; // 后端
            }
            $str = "{$third_account}|{$value['Uid']}|{$value['OrderNo']}|{$value['ApplySerial']}|{$business_type}|{$value['FundCode']}|{$value['ApplyTime']}|";
            $str .= "{$ConfirmTime}|{$value['TradeDay']}|{$profit_date}}|{$share_type}|{$value['ApplyAmount']}|{$value['ConfirmNetValue']}|{$value['ApplyShare']}";
            $str .= "|{$value['ConfirmShare']}|{$value['ConfirmAmount']}|{$value['Poundage']}|$return_code|{$value['OtherInfo']}\r\n";

            fputs($fp, $str);
        }
        $this->noteCommand("FileInterfaceOrder/executeExportConfirm", 1, "export data success ".$this->fund_confirm_filename);
        fclose($fp);
        return true;
    }

    //1.2.3	基金收益明细文件
    public function executeExportIncome()
    {
        $fp = fopen($this->fund_income_filename, 'w');
        if (empty($fp)) {
            $this->noteCommand("FileInterfaceOrder/executeExportIncome", 0, "open file failed ".$this->fund_income_filename);
            return false;
        }
        $fundPosition = new FundPosition([], $this->instid);
        $count = $fundPosition->getUserPositionFundNum("");   //持仓基金数量
        $fund_position_list = $fundPosition->query("", "all");  //持仓基金列表

        $str = "version:{$this->version}|总笔数:{$count}\r\n";
        $str .= "第三方理财账号|基金公司交易账号|销售人代码|分红日/发放日|基金代码|收费方式|投资者D日总资产|D日收益金额\r\n";
        fputs($fp, $str);
        if (empty($fund_position_list)) {
            $this->noteCommand("FileInterfaceOrder/executeExportIncome", 1, "get data is empty ".$this->fund_income_filename);
            fclose($fp);
            return false;
        }

        foreach ($fund_position_list as $key=>$value) {
            $fund_info = CommFun::GetFundInfo($value['FundCode']);
            $share_type = '';
            if ($fund_info['ShareType'] == 'A') {
                $share_type = 0; //前端
            } else {
                $share_type = 1; // 后端
            }
            $user_bank = $this->getUserBankMap($value['Uid']);
            $third_account = '';
            if (!empty($user_bank)) {
                $user_bank = array_shift($user_bank);
                $third_account = $user_bank['CdCard'];
            }
            $total_assets = $value['CurrentRemainShare'] * $fund_info['PernetValue'] + $value['UnpaidIncome'];
            $str = "{$third_account}|{$value['Uid']}|销售人代码|分红日/发放日|{$value['FundCode']}|{$share_type}|{$total_assets}| {$value['DayProfitLoss']}\r\n";
            fputs($fp, $str);
        }
        $this->noteCommand("FileInterfaceOrder/executeExportIncome", 1, "export data success ".$this->fund_income_filename);
        fclose($fp);
        return true;
    }

    //用户资产变更同步文件
    public function executeExportAssets()
    {
        $fp = fopen($this->fund_assets_change_filename, 'w');
        if (empty($fp)) {
            $this->noteCommand("FileInterfaceOrder/executeExportAssets", 0, "open file failed ".$this->fund_assets_change_filename);
            return false;
        }
        $fundPosition = new FundPosition([], $this->instid);
        $count = $fundPosition->getUserPositionFundNum("");   //持仓基金数量
        $fund_position_list = $fundPosition->query("", "all");  //持仓基金列表

        $str = "version:{$this->version}|总笔数:{$count}\r\n";
        $str .= "基金代码|收费方式|第三方理财账号|基金公司交易账号|基金总份额|基金冻结总份额|分红方式|交易确认日期\r\n";
        fputs($fp, $str);
        if (empty($fund_position_list)) {
            $this->noteCommand("FileInterfaceOrder/executeExportAssets", 1, "get data is empty ".$this->fund_assets_change_filename);
            fclose($fp);
            return false;
        }

        foreach ($fund_position_list as $key=>$value) {
            $fund_info = CommFun::GetFundInfo($value['FundCode']);
            $share_type = '';
            if ($fund_info['ShareType'] == 'A') {
                $share_type = 0; //前端
            } else {
                $share_type = 1; // 后端
            }
            $user_bank = $this->getUserBankMap($value['Uid']);
            $third_account = isset($user_bank[$value['TradeAcco']]) ? $user_bank[$value['TradeAcco']]['CdCard'] : ''; //

            $str = "{{$value['FundCode']}|{$share_type}|{$third_account}|{$value['Uid']}|{$value['CurrentRemainShare']}|{$value['FreezeSellShare']}|";
            $str .= "{$value['Melonmethod']}|交易确认日期\r\n";
            fputs($fp, $str);
        }
        $this->noteCommand("FileInterfaceOrder/executeExportNav", 1, "export data success ".$this->fund_assets_change_filename);
        fclose($fp);
        return true;
    }

    /*
     * 记录各个计划任务最终执行情况
     * @param $commName  控制器/方法名
     * @param $CommandStatus 0:未执行1:已执行
     * @param $Info 其他自定义信息
     */
    public function noteCommand($commName, $CommandStatus, $Info)
    {
        $db_local = Yii::$app->db_local;
        $time = date('Y-m-d H:i:s');
        $partner = CommFun::getPartnerInfo($this->instid);
        $instName = empty($partner['InstName']) ? '': $partner['InstName'];
        $sql = "INSERT INTO `fund_hzf_commands` SET Instid='{$this->instid}',CommandName='{$commName}',";
        $sql .= "LastDealTime='{$time}',InstName='{$instName}',CommandStatus='{$CommandStatus}',Info='{$Info}'";
        $db_local->createCommand($sql)->execute();
    }

    /*
 * 记录各个计划任务最终执行情况
 * @param $commName  控制器/方法名
 * @param $CommandStatus 0:未执行1:已执行
 * @param $Info 其他自定义信息
 */
    public function getUserBankMap($uid)
    {
        $db_local = Yii::$app->db_local;
        if (empty($uid)) {
            return [];
        }
        $sql = "select * from user_bank where uid='{$uid}'";
        $temp_rs = $db_local->createCommand($sql)->queryAll();
        $rs = [];
        foreach ($temp_rs as $key=>$val) {
            $rs[$val['TradeAcco']] = $val;
        }
        return $rs;
    }

    //检查各个文件接口目录是否创建
    private function _mkdir()
    {
        $this->_checkdir($this->fund_nav_filename);
        $this->_checkdir($this->fund_confirm_filename);
        $this->_checkdir($this->fund_income_filename);
        $this->_checkdir($this->fund_assets_change_filename);
    }

    /*
     * @param $dirname //目录全路径
     * @return bool
     */
    private function _checkdir($dirname)
    {
        $dirname = dirname($dirname);
        if (!file_exists($dirname)) {
            return @mkdir($dirname);
        }
        return true;
    }

}