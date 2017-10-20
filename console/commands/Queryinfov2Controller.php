<?php

namespace console\commands;

use common\lib\CommFun;
use common\models\HandleErr;
use common\models\PortfolioPosition;
use Yii;
use common\lib\HundSun as H;
use common\models\FundPosition;
use common\models\PositionProfitloss;
use yii\base\Exception;

/**
 * 每天同步用户的份额,未付收益，统计盈亏数据等信息
 * crontab 命令：  *.5 15-17 * * * php /data/release/fund/yii queryinfov2/index
 *
 */
class Queryinfov2Controller extends Controller
{
	public $fund_position_table = "fund_position_";
	public $portfolio_position_table = "portfolio_position_";
	public $date;
	public $datetime;
	public $is_log; //是否记录handle_err
	public $db;
	public $instid;
	public $log_file;

	public function init(){
		$this->date = date("Y-m-d");
		$this->datetime = date("Y-m-d H:i:s");
		$this->db = Yii::$app->db_local;
		$this->log_file = 'queryinfov2_'.date('Ymd').'.log';
	}
	
    /**
     * 查询最新用户的份额信息
	 * uid 查询用户编号
	 * fundcode 查询基金编号
	 * hzf 合作分配的平台号
     */
    public function actionIndex($uid='', $instid='1')
    {
        set_time_limit(0);
		if (!empty($uid)) {    #指定查询
			$this->fund_position_table = 'fund_position_'.$instid;
			$this->portfolio_position_table = 'portfolio_position_'.$instid;
			$this->instid = $instid;
			$result = $this->queryInfoFromHs($uid);
			if (!$result) {
				$errInfo = "同步持仓失败,uid={$uid}";
				parent::commandLog($errInfo, 1, $this->log_file);
			}
		} else {
			$partners = $this->db->createCommand("SELECT * FROM `partner`")->queryAll();
			if (empty($partners)) {
				parent::commandLog("商户号为空", 1, $this->log_file);
				exit;
			}
			foreach ($partners as $k => $v) {
				$this->instid = $v['Instid'];
				if (!$this->checkTableExists()) {	//表不存在
					$errInfo = '商户号'.$this->instid.'还没有建表';
					parent::commandLog($errInfo, 1, $this->log_file);
					continue;
				}
				$user_list = $this->queryTradeUserUid();
				if (empty($user_list)) {
					$errInfo = '商户号'.$this->instid.',没有用户交易';
					parent::commandLog($errInfo, 1, $this->log_file);
					continue;
				}
				$this->fund_position_table = 'fund_position_'.$this->instid;
				$this->portfolio_position_table = 'portfolio_position_'.$this->instid;
				foreach($user_list as $key => $value) {
					$uid = $value['Uid'];
					$this->queryInfoFromHs($uid);
				}
			}
		}
    }

	//汇总出所有有交易的用户
	private function queryTradeUserUid()
	{
		$sql = "SELECT Uid FROM `trade_order_{$this->instid}` GROUP BY Uid";
		$result = $this->db->createCommand($sql)->queryAll();
		return $result;
	}

	/*
	 *  查询并更新数据
	 * @param $uid
	 * @param $fundcode
	 * @param $pid
	 * @param string $hzf
	 * @return bool
	 */
	private function queryInfoFromHs($uid, $is_log=true)
	{
		$this->is_log = $is_log;
		$n = 0;
		$t1 = time();
		$pubParams = null;
		if ($this->instid == 1000) { //如果等于企业用户
			$pubParams['usertype'] = 'o';
		}
		$hs = new H($uid, $pubParams,1);
		$hs->loginHs();	//判断登录(防止下面程序运行中sessionkey过期)
		$resS001 = $hs->apiRequest('S001', ['filterzerofundshare'=>'0']);
		if ($resS001['code'] != H::SUCC_CODE) { //查询不成功，进入处理记录
			$errInfo = "查询S001不成功table:{$this->fund_position_table};code={$resS001['code']};message={$resS001['message']}";
			parent::commandLog($errInfo.var_export($resS001, true), 1, $this->log_file);
			return false;
		}
		if (!isset($resS001['returnlist'][0]) || empty($resS001['returnlist'][0])) {
			$errInfo = "查询S001成功,returnlist为空,table:{$this->fund_position_table};code={$resS001['code']};message={$resS001['message']}";
			parent::commandLog($errInfo.var_export($resS001, true), 1, $this->log_file);
			return false;
		}

		$returnlist = $resS001['returnlist'];
		foreach ($returnlist as $key=>$info) {
			$fundcode = $info['fundcode'];
			$fundposition = new FundPosition(array('Uid','FundCode'), $this->instid);
			$updatearr = [];
			$updatearr['Lastuptime'] = date("Y-m-d H:i:s");
			$updatearr['CurrentRemainShare'] = $info['currentremainshare'];//当前份额余额
			$updatearr['FreezeSellShare'] 	= $info['tfreezeremainshare'];//交易冻结份额
			$updatearr['UnpaidIncome'] 		= $info['unpaidincome'];//未付收益
			$updatearr['Melonmethod'] 		= $info['melonmethod'];//分红方式
			//$updatearr['TotalProfitLoss'] 	= $info['totalincome'];//累记收益
			//$updatearr['DayProfitLoss'] 	= $info['dayincome'];//当日收益
			$fundposition_data = $fundposition->query("FundCode='{$fundcode}' AND Uid='{$uid}'", "one"); //更新之前先查出来，用于更新组合持仓
			if (!empty($fundposition_data)) {
				$update_sql = CommFun::JoinUpdateStr($updatearr);
				$sql = "UPDATE {$this->fund_position_table} SET {$update_sql} WHERE Uid='{$uid}' and FundCode='{$fundcode}'";
			} else {
				$updatearr['Uid'] = $uid;
				$updatearr['FundCode'] = $fundcode;
				$updatearr['InitTime'] = date("Y-m-d H:i:s");
				$updatearr['Melonmethod'] = $info['melonmethod'];//分红方式
				$insert_sql = CommFun::JoinInsertStr($updatearr);
				$fieldStr = $insert_sql['fields'];
				$valueStr = $insert_sql['values'];
				$sql = "INSERT INTO {$this->fund_position_table} ({$fieldStr}) VALUES ({$valueStr})";
			}
			try {
				$this->db->createCommand($sql)->execute();
				$errInfo = "fund_position同步份额成功,fundPositionSql:{$sql}";
				parent::commandLog($errInfo, 0, $this->log_file);
			} catch (Exception $e) {
				parent::commandLog($e->getMessage(),1, $this->log_file);
			}
			//组合更新开始
			$portfolio = new PortfolioPosition([], $this->instid);
			$portPosition = $portfolio->query("FundCode='{$fundcode}' AND Uid='{$uid}'", "all");
			if (!empty($portPosition)) { 	//如果组合持仓中有该基金则同步更新该基金的数据
				foreach ($portPosition as $ppkey=>$ppval) {
					$update_data = [
						"CurrentRemainShare" => $fundposition_data['CurrentRemainShare'] * ($ppval['CurrentRemainShare']/$updatearr['CurrentRemainShare']),
						"UnpaidIncome" => $fundposition_data['UnpaidIncome'] * ($ppval['UnpaidIncome']/$updatearr['UnpaidIncome']),
						//这两个字段有待考察，如果交易系统有些字段并且数据准确，则把注释放开，接着使用
						//"TotalProfitLoss" => $fundposition_data['TotalProfitLoss'] * ($ppval['TotalProfitLoss']/$updatearr['TotalProfitLoss']),
						//"DayProfitLoss" => $fundposition_data['DayProfitLoss'] * ($ppval['DayProfitLoss']/$updatearr['DayProfitLoss']),
						"FreezeSellShare" => $fundposition_data['FreezeSellShare'] * ($ppval['FreezeSellShare']/$updatearr['FreezeSellShare']),
						"Lastuptime" => $this->datetime,
					];
					$update_sql = CommFun::JoinUpdateStr($update_data);
					$sql = "UPDATE {$this->portfolio_position_table} SET {$update_sql} WHERE id='{$ppval['id']}'";
					try {
						$this->db->createCommand($sql)->execute();
						$errInfo = "组合同步份额成功,portfolioFundPositionSql:{$sql}";
						parent::commandLog($errInfo, 0, $this->log_file);
					} catch (Exception $e) {
						parent::commandLog($e->getMessage(),1, $this->log_file);
					}
				}
			}
			//组合更新结束
			/*
			$day = $info['navdate'];
			$dayincome = $info['dayincome'];

			$positionProfitloss  = new PositionProfitloss([], $this->instid);

			$where = "FundCode='{$fundcode}' AND uid='{$uid}' AND Day='{$day}'";
			$data = $positionProfitloss->query($where,'one');

			$positionProfitloss->field = ['FundCode'=>$fundcode,'PositionId'=>$positionId,'ProfitLoss'=>$dayincome,'Day'=>$day,'uid'=>$uid];
			if (empty($data)) {  //插入一条数据[每日收益]
				$update_result = $positionProfitloss->insert();
			} else {             //更新一条数据[每日收益]
				$update_result = $positionProfitloss->update($where);
			}

			if (!$update_result) {
				$errInfo = "position_profitloss表更新结果:{$update_result},更新字段:fdcode={$fundcode},positionid={$positionId},profitloss={$dayincome},day={$day},uid={$uid}";
				Yii::error($errInfo, __METHOD__);
				$this->saveHandleError($positionId, $this->instid, $errInfo);
			}
			*/
			$n ++ ;
		}
		return true;
	}

	//处理异常数据
	private function saveHandleError($Oid, $Instid, $errInfo)
	{
		if (!$this->is_log) {
			return false;
		}
		$errArr = ['OidType'=>1, 'Oid'=>$Oid, 'Instid'=>$Instid, 'Info'=>$errInfo, 'SystemTime'=>$this->datetime];
		HandleErr::insert($errArr);
	}

	private function checkTableExists()
	{
		$needCheckTable = ['fund_position_', 'idempotence_order_', 'portfolio_position_',
			'portfolio_trade_', 'position_profitloss_', 'trade_order_', 'valutrade_plan_'];
		foreach ($needCheckTable as $table) {
			$table .= $this->instid;
			$existTable = $this->db->createCommand("SHOW TABLES LIKE '{$table}'")->queryOne();
			if (empty($existTable)) {
				return false;
			}
		}
		return true;
	}
	/**
	 *  每日凌晨更新合作方查询动作清零
	 
	 *  00 00 * * * php /data/release/fund/yii queryinfo/clear-zero
	 */
// 	public function actionClearZero()
// 	{
// 		FundHzfCommands::clearZero();
// 	}
}
