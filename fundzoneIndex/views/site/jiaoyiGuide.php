<?php
use yii\helpers\Url;
?>
<section class="banner customerBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/call-center']);?>" class="subLink">开户指南</a></li>
			<li><a href="<?= Url::to(['site/trade-guide']);?>" class="subLink cur">交易指南</a></li>
			<li><a href="<?= Url::to(['site/trade-quota']);?>" class="subLink">交易限额</a></li>
			<li><a href="<?= Url::to(['site/basis']);?>" class="subLink">基础知识</a></li>
			<li><a href="<?= Url::to(['site/investor']);?>" class="subLink">投资者权益须知</a></li>
			<li><a href="<?= Url::to(['site/risk-warning']);?>" class="subLink">风险提示</a></li>
			<li><a href="<?= Url::to(['site/anti-money-laundering']);?>" class="subLink">反洗钱</a></li>
			<li><a href="<?= Url::to(['site/feedback']);?>" class="subLink">问题反馈</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">交易指南</h2>
		<div class="newsCnt graphic">
			<h3 class="h3T">第一，选择基金申购</h3>
			<img src="<?= Url::base();?>/images/guide01.jpg"/>
			<h3 class="h3T">第二，填写申购金额，点击继续</h3>
			<img src="<?= Url::base();?>/images/guide02.jpg"/>
			<h3 class="h3T">第三，确认信息是否有误</h3>
			<img src="<?= Url::base();?>/images/guide03.jpg"/>
			<h3 class="h3T">第四，申购成功</h3>
			<img src="<?= Url::base();?>/images/guide04.jpg"/>
		</div>
	</div>
</section>

