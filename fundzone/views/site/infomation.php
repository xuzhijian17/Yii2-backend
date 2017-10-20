<?php
use yii\helpers\Url;
?>
<section class="banner newsBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/news']);?>" class="subLink">投资资讯</a></li>
			<li><a href="<?= Url::to(['site/dynamic']);?>" class="subLink">汇成动态</a></li>
			<li><a href="<?= Url::to(['site/bulletin']);?>" class="subLink">汇成公告</a></li>
			<li><a href="<?= Url::to(['site/infomation']);?>" class="subLink cur">信息披露</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">信息披露</h2>
		<div class="newList">
			<ul>
				<li>
					<a href="<?= Url::to(['site/practitioners']);?>" class="dbk">
						<span class="newsDate">2016-03-15   15:43</span>
						基金从业信息
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/关于发布《证券投资基金销售适用性指导意见》的通知.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-02-23 13:23</span>
						关于发布《证券投资基金销售适用性指导意见》的通知
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/证券投资基金信息披露管理办法.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-02-23 14:13</span>
						证券投资基金信息披露管理办法
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/开放式证券投资基金销售费用管理规定.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-02-23 16:35</span>
						开放式证券投资基金销售费用管理规定
					</a>
				</li>
			</ul>
		</div>
		<div class="pages" style="display: none;">
			<a href="javascript:void(0);" class="pagePrev page">&nbsp;</a>
			<a href="javascript:void(0);" class="page firstPage">1</a>
			<span class="page">······</span>
			<a href="javascript:void(0);" class="page">3</a>
			<a href="javascript:void(0);" class="page cur">4</a>
			<a href="javascript:void(0);" class="page">5</a>
			<!--<a href="javascript:void(0);" class="page cur">8</a>
			<a href="javascript:void(0);" class="page">9</a>-->
			<span class="page">······</span>
			<a href="javascript:void(0);" class="page endPage">56</a>
			<a href="javascript:void(0);" class="pageNext page">&nbsp;</a>
	    </div>
	</div>
</section>

