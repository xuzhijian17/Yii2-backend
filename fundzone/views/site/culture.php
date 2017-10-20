<?php
use yii\helpers\Url;
?>
<section class="banner cantactBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/about']);?>" class="subLink">公司介绍</a></li>
			<li><a href="<?= Url::to(['site/culture']);?>" class="subLink cur">企业文化</a></li>
			<li><a href="<?= Url::to(['site/practitioners']);?>" class="subLink">从业信息</a></li>
            <li><a href="<?= Url::to(['site/aptitude']);?>" class="subLink">资质证明</a></li>
            <li><a href="<?= Url::to(['site/contact']);?>" class="subLink">联系方式 </a></li>
			<li><a href="<?= Url::to(['site/recruit']);?>" class="subLink">诚聘英才</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">企业文化</h2>
		<div class="newsCnt culture">
			<img src="<?= Url::base();?>/images/culture.jpg"/>
		</div>
	</div>
</section>
