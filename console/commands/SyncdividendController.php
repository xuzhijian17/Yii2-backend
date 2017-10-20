<?php

namespace console\commands;

use common\lib\CommFun;
use Yii;
use Exception;

//从聚源数据库同步基金分红数据
//执行php /data/release/fund/yii SyncDividend/index
class SyncdividendController extends Controller
{
	public $db;
	public $jy_db;
	public $logFile;

	public function init(){
		$this->db = Yii::$app->db_local;
		$this->jy_db = Yii::$app->db_juyuan;
		$this->logFile = 'Syncdividend_'.date('Ymd').'.log';
	}

	/**
	 * 入口
	 * @param string $fundcode 传参同步某只基金，不传参同步所有基金
	 */
    public function actionIndex($fundcode="")
    {
        $t1 = time();
        set_time_limit(0);
		if (!empty($fundcode)) {
			$this->process($fundcode);
		} else {
			$fund_list = $this->getFundList();
			foreach ($fund_list as $key=>$value) {
				$this->process($value['FundCode']);
			}
		}
		$loginfo = '执行完成 耗时'.(time()-$t1).' s';
		parent::commandLog($loginfo,0,$this->logFile);
	}

	public function process($fundcode)
	{
		$fund_info = CommFun::GetFundInfo($fundcode);
		if (empty($fund_info)) {
			parent::commandLog("fund_info不存在{$fundcode}",1,$this->logFile);
			return false;
		}
		$dividend = $this->getJYDividendLast($fund_info['InnerCode']);
		if (empty($dividend)) {
			return false;
		}
		$local_dividend = $this->getLocalDividend($fundcode, date("Y-m-d", strtotime($dividend['ExRightDate'])));
		if (!empty($local_dividend)) {
			return false;
		}
		$r = $this->insertDividend($fundcode, $dividend['ExRightDate'], $dividend['ExecuteDate'], $dividend['ActualRatioAfterTax'], $dividend['InfoPublDate']);
		return $r;
	}

	//获取所有基金
	public function getFundList()
	{
		$sql = "select * from fund_info";
		$fund_list = $this->db->createCommand($sql)->queryAll();
		return $fund_list;
	}

	//某只基金的最后一次分红数据
	public function getJYDividendLast($innercode)
	{
		if (empty($innercode)) {
			return [];
		}
		$today = date("Y-m-d");
		$sql = "select * from MF_Dividend where InnerCode='{$innercode}' AND ExRightDate>='{$today}' order by InfoPublDate desc limit 1";
		$dividend = $this->jy_db->createCommand($sql)->queryOne();
		return $dividend;
	}

	/*
	 * @param $fundcode 基金代码
	 * @param $exrightdate 除息日 格式yyyy-mm-dd
	 * @return mixed
	 */
	public function getLocalDividend($fundcode, $exrightdate)
	{
		$sql = "select * from dividend where FundCode='{$fundcode}' AND date_format(ExrightDate, '%Y-%m-%d')='{$exrightdate}'";
		$dividend = $this->db->createCommand($sql)->queryOne();
		return $dividend;
	}

	public function insertDividend($FundCode, $ExRightDate, $ExecuteDate, $ActualRatioAfterTax, $InfoPublDate)
	{
		$sql = "insert into dividend set FundCode='{$FundCode}',ExRightDate='{$ExRightDate}',";
		$sql .= "ExecuteDate='{$ExecuteDate}',ActualRatioAfterTax='{$ActualRatioAfterTax}',InfoPublDate='{$InfoPublDate}'";
		try {
		    $r = $this->db->createCommand($sql)->execute();
		    parent::commandLog("成功执行sql:{$sql}",0,$this->logFile);
		    return $r;
		} catch (Exception $e) {
		    parent::commandLog($e->getMessage(),1,$this->logFile);
		    return false;
		}
	}
}
