<?php
use yii\helpers\Url;
?>
<section class="banner customerBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/call-center']);?>" class="subLink cur">开户指南</a></li>
			<li><a href="<?= Url::to(['site/trade-guide']);?>" class="subLink">交易指南</a></li>
			<li><a href="<?= Url::to(['site/trade-quota']);?>" class="subLink">交易限额</a></li>
			<li><a href="<?= Url::to(['site/basis']);?>" class="subLink">基础知识</a></li>
			<li><a href="<?= Url::to(['site/investor']);?>" class="subLink">投资者权益须知</a></li>
			<li><a href="<?= Url::to(['site/risk-warning']);?>" class="subLink">风险提示</a></li>
			<li><a href="<?= Url::to(['site/anti-money-laundering']);?>" class="subLink">反洗钱</a></li>
			<li><a href="<?= Url::to(['site/feedback']);?>" class="subLink">问题反馈</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">开户指南</h2>
		<div class="newsCnt graphic">
			<h3 class="h3T">第一，进入开户页面，选择您持有的银行卡</h3>
			<img src="<?= Url::base();?>/images/open01.jpg"/>
			<h3 class="h3T">第二，填写您的银行卡信息</h3>
			<img src="<?= Url::base();?>/images/open02.jpg"/>
			<h3 class="h3T">第三，填写完整信息后，确认信息无误，跳转银联验证</h3>
			<img src="<?= Url::base();?>/images/open03.jpg"/>
			<h3 class="h3T">第四，银联在线验证</h3>
			<img src="<?= Url::base();?>/images/open04.jpg"/>
			<h3 class="h3T">第五，信息验证成功</h3>
			<img src="<?= Url::base();?>/images/open05.jpg"/>
			<h3 class="h3T">第六，填写详细资料，及问卷调查</h3>
			<img src="<?= Url::base();?>/images/open06.jpg"/>
			<h3 class="h3T">第七，设置登录密码</h3>
			<img src="<?= Url::base();?>/images/open07.jpg"/>
			<h3 class="h3T">第八，开户成功</h3>
			<img src="<?= Url::base();?>/images/open08.jpg"/>
		</div>
	</div>
</section>

