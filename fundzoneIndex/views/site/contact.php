<?php
use yii\helpers\Url;
?>
<section class="banner newsBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/about']);?>" class="subLink">公司介绍</a></li>
			<li><a href="<?= Url::to(['site/culture']);?>" class="subLink">企业文化</a></li>
			<li><a href="<?= Url::to(['site/practitioners']);?>" class="subLink">从业信息</a></li>
            <li><a href="<?= Url::to(['site/aptitude']);?>" class="subLink">资质证明</a></li>
			<li><a href="<?= Url::to(['site/contact']);?>" class="subLink cur">联系方式 </a></li>
			<li><a href="<?= Url::to(['site/recruit']);?>" class="subLink">诚聘英才</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">联系方式</h2>
		<div class="newsCnt culture">
			地址：北京市海淀区中关村e世界A座1108室<br />
			电话：010-62680527<br />
			邮箱：service@funzone.cn
			<img src="<?= Url::base();?>/images/map.jpg"/>
		</div>
	</div>
</section>

