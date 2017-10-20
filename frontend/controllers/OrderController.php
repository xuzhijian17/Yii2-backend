<?php
namespace frontend\controllers;
use frontend\models\SecuMain;
use frontend\services\FundService;
use frontend\models\TradeOrder;
use frontend\models\NetValueCashe;
use frontend\models\NetValue;
use common\lib\HundSun;
use yii\helpers\Url;
use Yii;
/**
 * Site controller
 */
class OrderController extends Controller
{
	public $layout = false;
	
	public function init()
	{
		parent::init();
		$this->isLogin();
	}
	
    public function actionIndex()
    {
		$InnerCode = $this->get('InnerCode', 3246);
		$base = FundService::getFundBase($InnerCode);
		$data = FundService::getFundDetails($InnerCode);
		$data = array_merge($base, $data);
		$orderObj = new TradeOrder();
		$order = $orderObj->getFundTotal($this->user['id'], $data['SecuCode']);
		//var_dump($data);
		$mydata = ['profit' => 0, 'share' => 0, 'money' => 0];
		if($order){
			foreach($order as $val){
				$mydata['profit'] = $mydata['profit'] + ($data['UnitNV']-$order['ConfirmNetValue'])*$order['ConfirmAmount'] - $order['Poundage'];
				$mydata['share'] = $mydata['share'] + $order['ConfirmAmount'];
				$mydata['money'] = $mydata['money'] + $order['ConfirmAmount']*$data['UnitNV'];
			}
		}
		$data = array_merge($mydata, $data);
		//var_dump($data);
		return $this->render('index',['data' => $data]);
    }
	
	public function actionNetvalue()
    {
		$page = $this->get('page', 1);
		$InnerCode = $this->get('InnerCode', 3246);
		$base = FundService::getFundBase($InnerCode);
		$type = $base['MSType'];
		$orderObj = new TradeOrder();
		$sql = "select min('ConfirmTime') ConfirmTime from {$orderObj->tbName} where Uid = {$this->user['id']} and FundCode='{$base['SecuCode']}' and TradeType in(0,2,3) and TradeStatus=2";
		$db_local = Yii::$app->db_local;
		$date = $db_local->createCommand($sql)->queryOne();
		$date['ConfirmTime'] = "2016-03-01";
		if($date['ConfirmTime']){
			$data = in_array($type, [1106, 1109]) ? NetValue::getNetValueOrderList($InnerCode, $date['ConfirmTime'], $page) 
					: NetValueCashe::getNetValueOrderList($InnerCode, $date['ConfirmTime'], $page);
			$this->renderJson(['list'=>$data, 'page'=>$page]);
		}
		$this->renderJson(['list'=>[], 'page'=>0]);
    }
	
	public function actionOrderlist()
    {
		$page = $this->get('page', 1);
		$InnerCode = $this->get('InnerCode', 3246);
		$base = FundService::getFundBase($InnerCode);
		$orderObj = new TradeOrder();
		$data = $orderObj->getOrderList($this->user['id'], $base['SecuCode'], $page);
		$this->renderJson(['list'=>$data, 'page'=>$page]);
    }
	
	public function actionRecord()
    {
		$data = [];
		if($this->isAjax()){
			$orderObj = new TradeOrder();  $type = $this->get('type');
			if($type == 0){
				$where = "Uid={$this->user['id']}";
			}elseif($type == 1){
				$where = "Uid={$this->user['id']} and TradeStatus=1";
			}elseif($type == 2){
				$where = "Uid={$this->user['id']} and TradeType=0";
			}elseif($type == 3){
				$where = "Uid={$this->user['id']} and TradeType=1";
			}elseif($type == 4){
				$where = "Uid={$this->user['id']} and TradeType=3";
			}elseif($type == 5){
				$where = "Uid={$this->user['id']} and TradeType=2";
			}
			$data = $orderObj->query($where, 'all', ' order by SysTime desc');
			if($data){
				foreach($data as &$val){
					$val['InfoJson'] = json_decode($val['InfoJson'], true);
				}
			}
			$this->renderJson(['list'=>$data]);
		}
		return $this->render('record',['data' => $data]);
    }
	
	public function actionBonuslist()
    {
		$hs = new HundSun($this->user['id']);
		$res = $hs->apiRequest('T010', []);
		print_r($res);
    }
	
	public function actionBonus()
    {
		$code = $this->get('code');
		$hs = new HundSun($this->user['id']);
		$res = $hs->apiRequest('T010', []);
		if($res['code'] == 'ETS-5BP0000' && $res['items']){
			$fund = [];
			foreach($res['items'] as $val){
				$fund[$val['fundcode']] = $val;
			}
			if($this->isAjax()){
				$melonmethod = $this->get('type');
				$param = [
					'fundcode' => $fund[$code]['fundcode'],
					'melonmethod' => $melonmethod,
					'sharetype' => $fund[$code]['sharetype'],
					'tradeacco' => $fund[$code]['tradeacco'],
				];
				$res = $hs->apiRequest('T007', $param);
				if($res['code'] == 'ETS-5BP0000'){
					$this->renderJson(['error'=>1, 'msg'=>'修改成功']);
				}
				$this->renderJson(['error'=>0, 'msg'=>'修改失败']);
			}
		}else{
			
		}
		return $this->render('bonus',['data'=>$fund[$code]]);
		print_r($res);
    }
}
