<?php

namespace console\commands;

use common\models\HandleErr;
use common\models\PortfolioPosition;
use Yii;
use common\lib\HundSun as H;
use common\lib\CommFun as C;
use common\models\FundPosition;
use common\models\PositionProfitloss;
use console\models\FundHzfCommands;



/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author ddz <daidazhi@haotougu.com>
 * @since 2.0
 * 每天同步用户的份额,未付收益，统计盈亏数据等信息
 * crontab 命令：  *.5 15-17 * * * php /data/release/fund/yii queryinfo/index
 *
 */
class QueryinfoController extends Controller
{
	public $filename;    //日志文件名
	public $fund_position_table = "fund_position_";
	public $portfolio_position_table = "portfolio_position_";
	public $date;
	public $datetime;
	public $is_log; //是否记录handle_err

	public function init(){
		$this->filename = "queryinfo_".date("Y-m-d");
		$this->date = date("Y-m-d");
		$this->datetime = date("Y-m-d H:i:s");

	}
	
    /**
     * 查询最新用户的份额信息
	 * uid 查询用户编号
	 * fundcode 查询基金编号
	 * hzf 合作分配的平台号
     */
    public function actionIndex($uid='', $fundcode='', $instid='1')
    {
        set_time_limit(0);
        $db_local = Yii::$app->db_local;
		if (!empty($uid) && !empty($fundcode)) {    #指定查询
			$this->fund_position_table .= $instid;
			$this->portfolio_position_table .= $instid;

			$fundposition = new FundPosition(array('id'), $instid);
			$where = "Uid='{$uid}' and FundCode='{$fundcode}'";
			$data = $fundposition->query($where, 'one', 'id asc', '1');
			if (empty($data)) {
				$errInfo = "fund_position_{$instid}表为空,条件为:{$where}";
				Yii::error($errInfo, __METHOD__);
				exit;
			}

			$result = $this->queryInfoFromHs($uid, $fundcode, $data['id'], $instid);
			if (!$result) {
				$errInfo = "同步持仓失败,uid={$uid},fundcode={$fundcode},fundpositionid={$data['id']}";
				Yii::error($errInfo, __METHOD__);
				$this->saveHandleError($data['id'], $instid, $errInfo);
			}
		} else {
			 $partners = $db_local->createCommand("SELECT * FROM `partner`")->queryAll();
			 if (empty($partners)) {
			     Yii::error('商户号为空',__METHOD__);
				 exit;
			 }
			 foreach ($partners as $k => $v) {

				 $instid = $v['Instid'];
				 $this->fund_position_table .= $instid;
				 $this->portfolio_position_table .= $instid;

				 $fundposition = new FundPosition(array('id','Uid','FundCode'), $instid);
				 $where = "Lastuptime < '{$this->date}'";
				 $data = $fundposition->query($where, 'all', 'id asc', '500');
				 if (empty($data)) {
					 $errInfo = "fund_position_{$instid}表为空,条件为:{$where}";
					 Yii::error($errInfo, __METHOD__);
					 exit;
				 }

				 foreach($data as $key => $value) {
					 $uid = $value['Uid'];
					 $fundcode = $value['FundCode'];
					 $fundpositionId = $value['id'];
					 $result = $this->queryInfoFromHs($uid, $fundcode, $fundpositionId, $instid);
					 if (!$result) {
						 $errInfo = "同步持仓失败,uid={$uid},fundcode={$fundcode},fundpositionid={$fundpositionId}";
						 Yii::error($errInfo, __METHOD__);
						 $this->saveHandleError($fundpositionId, $instid, $errInfo);
					 }
				 }
			 }
		}
    }

	/**
	 *  查询并更新数据
	 * @param $uid
	 * @param $fundcode
	 * @param $pid
	 * @param string $hzf
	 * @return bool
	 */
	private function queryInfoFromHs($uid, $fundcode, $pid, $hzf='1', $is_log=true)
	{
		$this->is_log = $is_log;

		$hs = new H($uid);
		$hs->loginHs();	//判断登录(防止下面程序运行中sessionkey过期)
		$resS001 = $hs->apiRequest('S001', ['fundcode'=>$fundcode]);

		if ($resS001['code'] != H::SUCC_CODE) { //查询不成功，进入处理记录
			$errInfo = "查询S001不成功table:{$this->fund_position_table};id:{$pid};code={$resS001['code']};message={$resS001['message']}";
			Yii::error($errInfo.var_export($resS001, true), __METHOD__);
			return false;
		}
		if (!isset($resS001['returnlist'][0]) || empty($resS001['returnlist'][0])) {
			$errInfo = "查询S001成功,returnlist为空,table:{$this->fund_position_table};id:{$pid};code={$resS001['code']};message={$resS001['message']}";
			Yii::error($errInfo.var_export($resS001, true), __METHOD__);
			return false;
		}

		$returnlist = $resS001['returnlist'][0];
		$fundposition = new FundPosition(array('Uid','FundCode'), $hzf);

		$updatearr = [];
		$updatearr['Lastuptime'] = $this->datetime;
		$updatearr['CurrentRemainShare'] = $returnlist['currentremainshare'];//当前份额余额
		$updatearr['FreezeSellShare'] 	= $returnlist['tfreezeremainshare'];//交易冻结份额
		$updatearr['UnpaidIncome'] 		= $returnlist['unpaidincome'];//未付收益
		$updatearr['TotalProfitLoss'] 	= $returnlist['totalincome'];//累记收益
		$updatearr['DayProfitLoss'] 	= $returnlist['dayincome'];//当日收益
		$fundposition_data = $fundposition->query("FundCode='{$fundcode}' AND Uid='{$uid}'", "one"); //更新之前先查出来，用于更新组合持仓

		$rst = $fundposition->updateFundData($uid, $fundcode, $updatearr);
		if (!$rst) {
			$errInfo = "update_fund_position同步份额失败,table:{$this->fund_position_table};id:{$pid}";
			Yii::error($errInfo, __METHOD__);
			$this->saveHandleError($pid, $hzf, $errInfo);
			return false;
		}
		//组合更新开始
		$portfolio = new PortfolioPosition([], $hzf);
		$portPosition = $portfolio->query("FundCode='{$fundcode}' AND Uid='{$uid}'", "all");
		if (!empty($portPosition)) { //如果组合持仓中有该基金则同步更新该基金的数据
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
				$portfolio->updateFundData($ppval['id'], $update_data);
			}
		 }
		//组合更新结束

		$day = $returnlist['navdate'];
		$dayincome = $returnlist['dayincome'];

		$positionProfitloss  = new PositionProfitloss([], $hzf);

		$where = "FundCode='{$fundcode}' AND uid='{$uid}' AND Day='{$day}'";
		$data = $positionProfitloss->query($where,'one');

		$positionProfitloss->field = ['FundCode'=>$fundcode,'PositionId'=>$pid,'ProfitLoss'=>$dayincome,'Day'=>$day,'uid'=>$uid];
		if (empty($data)) {  //插入一条数据[每日收益]
			$update_result = $positionProfitloss->insert();
		} else {             //更新一条数据[每日收益]
			$update_result = $positionProfitloss->update($where);
		}

		if (!$update_result) {
			$errInfo = "position_profitloss表更新结果:{$update_result},更新字段:fdcode={$fundcode},positionid={$pid},profitloss={$dayincome},day={$day},uid={$uid}";
			Yii::error($errInfo, __METHOD__);
			$this->saveHandleError($pid, $hzf, $errInfo);
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

	/**
	 *  每日凌晨更新合作方查询动作清零
	 
	 *  00 00 * * * php /data/release/fund/yii queryinfo/clear-zero
	 */
// 	public function actionClearZero()
// 	{
// 		FundHzfCommands::clearZero();
// 	}
}
