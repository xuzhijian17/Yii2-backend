<?php
use yii\helpers\Url;
?>
<section class="banner newsBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/news']);?>" class="subLink">投资资讯</a></li>
			<li><a href="<?= Url::to(['site/dynamic']);?>" class="subLink">汇成动态</a></li>
			<li><a href="<?= Url::to(['site/bulletin']);?>" class="subLink cur">汇成公告</a></li>
			<li><a href="<?= Url::to(['site/infomation']);?>" class="subLink">信息披露</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">汇成公告</h2>
		<div class="newList">
			<ul>
				<li>
					<a href="<?= Url::to(['site/bulletin-detail','id'=>3]);?>" class="dbk">
						<span class="newsDate">2016-06-02   14:23</span>
						系统维护通知
					</a>
				</li>
				<li>
					<a href="<?= Url::to(['site/bulletin-detail','id'=>2]);?>" class="dbk">
						<span class="newsDate">2016-04-29 14:30</span>
						五一期间部分基金暂停申购通知
					</a>
				</li>
				<li>
					<a href="<?= Url::to(['site/about']);?>" class="dbk">
						<span class="newsDate">2016-02-15 15:26</span>
						公司介绍
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

