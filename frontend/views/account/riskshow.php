<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?=Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>测评结果</title>
</head>
<body class="app_body app_detail">
	<div class="app_page pT75 fixBut">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:void(0);">返回</a></div>
				<div class="app_title">测评结果</div>
				<!--<div class="app_Rlink"><a href="fengxianpingce.html" class="">重新评测</a></div>-->
			</div>
		</div>
		<div class="riskWarp">
			<h2 class="riskT">您的投资类型为</h2>
			<div class="riskRst riskRst0<?=$data['key']?>"><!--风险等级从低到高riskRst01，riskRst02，riskRst03，riskRst04，riskRst05-->
				<span><?=$data['data']?></span><!--类型从低到高：保守型，稳健型，平衡型，成长型，进取型-->
			</div>
		</div>
		<div class="riskNotice">保护本金不受损失，保证收益稳定</div>
		<div class="button_section">
			<a class="buttonA" href="jiaoyi_goumai.html">
				继续购买
			</a>
		</div>
	</div>
    
</body>
</html>