<?php

namespace console\commands;

use common\lib\HundSun;
use common\models\HandleErr;
use common\models\PortfolioPosition;
use Yii;
use common\lib\HundSun as H;
use common\lib\CommFun as C;
use common\models\FundPosition;
use common\models\PositionProfitloss;
use console\models\FundHzfCommands;

class ScriptController extends Controller
{


	public function init(){

	}

    public function actionIndex()
    {
		$db = Yii::$app->db_local;
		$sql = "select * from user where instid !=1000";
		$user_list = $db->createCommand($sql)->queryAll();

		foreach($user_list as $key=>$user) {
			$daan = [
				'00001'=>$this->getRandValue(),
				'00002'=>$this->getRandValue(),
				'00003'=>$this->getRandValue(),
				'00007'=>$this->getRandValue(),
				'00008'=>$this->getRandValue(),
				'00009'=>$this->getRandValue(),
				'00010'=>$this->getRandValue(),
				'00011'=>$this->getRandValue(),
				'00012'=>$this->getRandValue(),
			];
			$uid = $user['id'];
			$params = [
				'custname'=>$user['Name'],
				'identityno'=>$user['CardID'],
				'identitytype'=>'0',
				'qnoandanswer'=> base64_encode(json_encode($daan)),
			];

			$hs = new H($uid, [],1);
			$hs->loginHs();	//判断登录(防止下面程序运行中sessionkey过期)
			$resC004 = $hs->apiRequest('C004', []);
			if($resC004['code'] == HundSun::SUCC_CODE) {
				echo "<pre>";
				print_r($resC004);
				$risklist = $resC004['risklist'];
				if (empty($risklist[0]['myanswer'])) {
					$resC005 = $hs->apiRequest('C005', $params);
					echo "<pre>";
					print_r($resC005);
				}
			}


		}

    }

	public function getRandValue()
	{
		$itemvalu_rand = ['004', '005'];
		return $itemvalu_rand[rand(0, 1)];
	}

}
