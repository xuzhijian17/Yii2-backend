<?php
namespace frontend\controllers;
use frontend\models\SecuMain;
use frontend\services\FundService;
use frontend\models\InterimBulletin;
use frontend\models\Dividend;
use frontend\models\NetValueCashe;
use frontend\models\NetValue;
use frontend\models\ChargeRate;
use frontend\models\FundManager;
use frontend\models\AssetAllocation;
use frontend\services\TradeService;
use yii\log\FileTarget;
use yii\helpers\Url;
use Yii;
/**
 * Site controller
 */
class FundController extends Controller
{
	public $layout = "fund";
	public $base = '';
	public function init()
	{
		$InnerCode = $this->get('InnerCode');
		$data = FundService::getFundBase($InnerCode);
		$base = [
			'InnerCode' => $data['InnerCode'],
			'SecuCode' => $data['SecuCode'],
			'ChiName' => $data['ChiNameAbbr'],
			'FundTypeCode' => $data['FundTypeCode'],
			'MS' => $data['MS'],
			'MSType' => $data['MSType']
		];
		$this->base = $base;
		Yii::$app->view->params['base'] = $base;
	}
	//详情首页
    public function actionIndex()
    {
		$InnerCode = $this->get('InnerCode');
		$data = FundService::getFundDetails($InnerCode);
		$data = array_merge($this->base, $data);
		$risk = [3=>1, 7=>2, 10=>3, 15=>4, 20=>5];
		$data['RiskEvaluationDM'] = isset($risk[$data['RiskEvaluationDM']]) ? $risk[$data['RiskEvaluationDM']] : $data['RiskEvaluationDM'];
		$uid = $this->user ? $this->user['id'] : 0;
		$obj = new TradeService($uid, 0, $data['SecuCode']);
		$status = $obj->fundButStatus();
		$rate = ChargeRate::getChargeRateFind($InnerCode, 2) ? : ['ChargeRateDesciption'=>'0', 'MinimumChargeRate'=>0];
		//var_dump($rate);
		$data = array_merge($rate, $data);
		return $this->render('index',['data' => $data, 'status'=>$status]);
    }
	#分时图
	public function actionLine()
	{
		$line = [];
		$data = FundService::getFundMarket($this->get('InnerCode'), $this->get('attr'), $this->base['FundTypeCode']);
		//print_r($data);
		foreach($data as $val){
			if(!in_array($this->base['FundTypeCode'], [1106, 1109]))
				$line[] = [$val['time'] * 1000, $val['fundNet'], $val['fundRate'], $val['gfundNet'], $val['gfundRate']/*, $val['hsfundNet'], $val['hsfundRate']*/];
			else
				$line[] = [$val['time'] * 1000, $val['fundNet'], $val['fundRate']];
		}
		$this->renderJson($line);
	}
	//基金同类排名
	public function actionResult()
	{
		$data = FundService::getFundResultSort($this->get('InnerCode'), $this->get('MSType'));
		$this->renderJson(['list'=>$data]);
	}
	# 历史净值
	public function actionNetvalue()
	{
		$page = $this->get('page', 0);
		$type = $this->get('MSType');
		if(!$this->isAjax()) return $this->render('netvalue');	
		$class = $type == 1109 ? 'NetValue' : 'NetValueCashe';
		if($page != 0)
			$data = in_array($type, [1106, 1109]) ? NetValue::getNetValueList($this->get('InnerCode'), $page) 
				: NetValueCashe::getNetValueList($this->get('InnerCode'), $page);
		else
			$data = in_array($type, [1106, 1109]) ? NetValue::getNetValueList($this->get('InnerCode'), 1, 5)
				: NetValueCashe::getNetValueList($this->get('InnerCode'), 1, 5);
		$this->renderJson(['list'=>$data, 'type'=>$type]);
	}
	# 基金概况
	public function actionProfile()
	{
		$InnerCode = $this->get('InnerCode');
		$data = FundService::getFundProfile($InnerCode);
		return $this->render('profile',['data' => $data]);
		//var_dump($data);
	}
	# 基金经理详情页
	public function actionManager()
	{
		if(!$this->isAjax()){
			$data = FundManager::getManagerFund($this->get('args'), $this->get('InnerCode'));
			return $this->render('manager',['data' => $data]);
		}
		$data = FundManager::getManagerFundList($this->get('args'));
		foreach($data as &$val){
			$val['AccessionDate'] = substr($val['AccessionDate'], 0, 10);
			$val['DimissionDate'] = $val['DimissionDate'] ? substr($val['DimissionDate'], 0, 10) : '今';
			$val['Performance'] = sprintf('%.2f', round($val['Performance'] * 100, 2));
		}
		$this->renderJson(['list'=>$data]);
		//var_dump($data);
	}
	# 公告和分红
	public function actionNotice()
	{
		if(!$this->isAjax()) return $this->render('notice');
		if($this->get('type') == 1){
			$data = InterimBulletin::getInterimBulletinList($this->get('InnerCode'), $this->get('page', 0)); //基金临时公告
			if(!$data)	$this->renderJson(['list'=>[]]);
			foreach($data as &$val){
				$val['url'] = Url::to(['detail', 'InnerCode'=>$this->get('InnerCode'), 'id'=>$val["ID"]]);
			}
		}else{
			$data = Dividend::getDividendList($this->get('InnerCode'), $this->get('page', 0)); ////基金分红记录
		}
		$this->renderJson(['list'=>$data]);
	}
	# 公告详情页
	public function actionDetail()
	{
		$param = [
			'field' => 'BulletinDate, InfoTitle, Detail',
			'where' => 'ID = '.$this->get('id'),
		];
		$data = InterimBulletin::find($param);
		$data['Detail'] = str_replace($data['InfoTitle']."\n", "", $data['Detail']);
		$data['Detail'] = str_replace("\n", "<br/>", $data['Detail']);
		return $this->render('detail', ['data' => $data]);
	}
	#交易须知
	public function actionTradnotice()
	{
		$data = FundService::getTradNotice($this->get('InnerCode'));
		return $this->render('tradnotice', ['data' => $data]);
	}
	#投资组合
	public function actionGroup(){
		$data['zc'] = FundService::getInvestIndustry($this->get('InnerCode'), 0);
		$data['gp'] = FundService::getInvestIndustry($this->get('InnerCode'), 1);
		$data['zq'] = FundService::getInvestIndustry($this->get('InnerCode'), 2);
		$list = AssetAllocation::getAssetAllocationList($this->get('InnerCode'));
		if($list){
			$listData = [];
			foreach($list as $val){
				$listData['name'][] = $val['AssetType'].round($val['RatioInTotalAsset']*100, 2).'%';
				$listData['data'][] = ['value' => $val['RatioInTotalAsset'], 'name'=>$val['AssetType'].round($val['RatioInTotalAsset']*100, 2).'%'];
			}
		}
		//var_dump($listData);
		$listData['name'] = isset($listData['name']) ? json_encode($listData['name']) : '[]';
		$listData['data'] = isset($listData['data']) ? json_encode($listData['data']) : '[]';
		return $this->render('group', ['data' => $data, 'list'=>$listData]);
		$this->renderJson(['list'=>$data, 'show'=>$show, 'type'=>$this->get('type')]);
	}
	
	public function actionTest()
	{
		$data = AssetAllocation::getAssetAllocationList(13295);
		var_dump($data);
		// $data = FundService::getFundResultSort(20857, 1109);
		// $this->renderJson(['list'=>$data]);
	}
}
