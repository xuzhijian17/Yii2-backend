<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kid">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>热销基金</title>
		<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web');?>/css/fundApp.css"/>
		<script src="<?= Yii::getAlias('@web');?>/js/jquery.min.js" type="text/javascript"></script>
		<script src="<?= Yii::getAlias('@web');?>/js/slider.js" type="text/javascript"></script>
		<script src="<?= Yii::getAlias('@web');?>/js/fundApp.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="topBar">
			<span class="pageT">热销基金</span>
			<a href="javascript:history.back(-1);" class="backLink">
				<span class="back"></span>
			</a>
		</div>
		<div class="container">
			<div class="main">
				<div class="hotFund">
					<?php if(isset($recommend) && !empty($recommend)): ?>
						<?php foreach($recommend as $key=>$value): ?>
							<dl>
								<dt>
									<span class="fs20 colB"><?= $value['FundAbbrName'];?>（<?= $value['FundCode'];?>）</span>
									<span class="fs14 col6"><?= $value['Tags'];?></span>
								</dt>
								<dd>
									<span class="colR fs20"><?= round($value['RRSinceStart'],2);?>%</span>
									<span class="fs16">成立以来</span>
								</dd>
								<dd>
									<span class="colR fs20"><?= round($value['MinPurchaseAmount'],0);?>元</span>
									<span class="fs16">起购额</span>
								</dd>
							</dl>
						<?php endforeach;?>
					<?php endif;?>
				</div>
				<!--hotFund end-->
			</div>
			<!--main end-->
		</div>
	</body>
</html>

















