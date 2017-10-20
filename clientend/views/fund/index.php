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
		<title>基金理财</title>
		<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web');?>/css/fundApp.css"/>
		<script src="<?= Yii::getAlias('@web');?>/js/jquery.min.js" type="text/javascript"></script>
		<script src="<?= Yii::getAlias('@web');?>/js/slider.js" type="text/javascript"></script>
		<script src="<?= Yii::getAlias('@web');?>/js/fundApp.js" type="text/javascript"></script>
	</head>
	<body class="barBottom">
		<div class="topBar">
			<span class="pageT">基金理财</span>
			<!--<a href="javascript:history.back(-1);" class="backLink">
				<span class="back"></span>
			</a>-->
		</div>
		<div class="container">
			<div class="header">
				<div id="slideBox" class="slideBox">
					<div class="bd">
						<ul>
							<li><a class="pic" href="javascript:void(0);"><img src="<?= Yii::getAlias('@web');?>/images/banner.jpg" width="100%"/></a></li>
							<li><a class="pic" href="javascript:void(0);"><img src="<?= Yii::getAlias('@web');?>/images/banner.jpg" width="100%"/></a></li>
						</ul>
					</div>
				
					<div class="hd">
						<ul></ul>
					</div>
				</div>
				<!--slideBox end-->
				<div class="nav">
					<a href="javascript:void(0);" class="navLink">
						<img src="<?= Yii::getAlias('@web');?>/images/nav_ico01.png" height="40px"/>
						<span>活期宝</span>
					</a>
					<a href="javascript:void(0);" class="navLink">
						<img src="<?= Yii::getAlias('@web');?>/images/nav_ico02.png" height="40px"/>
						<span>基金超市</span>
					</a>
					<a href="javascript:void(0);" class="navLink">
						<img src="<?= Yii::getAlias('@web');?>/images/nav_ico03.png" height="40px"/>
						<span>我的关注</span>
					</a>
					<a href="javascript:void(0);" class="navLink">
						<img src="<?= Yii::getAlias('@web');?>/images/nav_ico04.png" height="40px"/>
						<span>主题推荐</span>
					</a>
				</div>
				<!--nav end-->
			</div>
			<!--header end-->
			<div class="main">
				<h2>
					<a href="<?= Url::to(['fund/hot-list']);?>" class="appLink">
						<span class="col6">热销基金</span>
						<span class="arrowR"></span>
					</a>
				</h2>
				
				<div class="hotFund">
					<?php if(isset($recommend) && !empty($recommend)): ?>
						<?php foreach($recommend as $key=>$value): ?>
							<?php if($key>2){break;};?>
							<a href="<?= Url::to(['fund/fund-detail','fundcode'=>$value['FundCode']]);?>">
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
							</a>
						<?php endforeach;?>
					<?php endif;?>
				</div>
				<!--hotFund end-->
				<h2>
					<a href="<?= Url::to(['fund/theme-list']);?>" class="appLink">
						<span class="col6">主题推荐</span>
						<span class="arrowR"></span>
					</a>
				</h2>
				<div class="theme">
					<ul>
						<?php if(isset($recommendTheme) && !empty($recommendTheme)): ?>
							<?php foreach($recommendTheme as $key=>$value): ?>
								<?php if($key>2){break;};?>
								<li><a href="<?= Url::to(['fund/theme-detail','tid'=>$value['id']]);?>"><img src="<?= $value['Image'];?>"/></a></li>
							<?php endforeach;?>
						<?php endif;?>
					</ul>
				</div>
				<!--theme end-->
				<div class="textBlk">
					<span class="bankTxtIco col9">民生银行全程保障交易资金安全<br />客服热线：<a href="tel:4006199059" class="colB">4006199059</a></span>
				</div>
			</div>
			<!--main end-->
		</div>
		<div class="footerBar">
			<ul>
				<li><a href="index.html" class="menuLink">投资</a></li>
				<li><a href="javascript:void(0);" class="menuLink">资产</a></li>
				<li><a href="javascript:void(0);" class="menuLink">账户</a></li>
			</ul>
		</div>
	</body>
</html>
<script type="text/javascript">
	TouchSlide({ 
		slideCell:"#slideBox",
		titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
		mainCell:".bd ul", 
		effect:"leftLoop", 
		autoPage:true,//自动分页
		autoPlay:false, //自动播放
		delayTime:350,
		interTime:2000
	});
</script>

