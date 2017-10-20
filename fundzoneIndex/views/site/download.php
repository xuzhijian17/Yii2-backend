<?php
use yii\helpers\Url;
?>
<section class="banner financeBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li>
				<a href="<?= Url::to(['site/open-account']);?>" class="subLink">柜台业务指南</a>
				<ul class="levelMenu" style="display: none;">
					<li><a href="<?= Url::to(['site/open-account']);?>" class="subLink">开户</a></li>
					<li><a href="<?= Url::to(['site/clos-account']);?>" class="subLink">销户</a></li>
					<li><a href="<?= Url::to(['site/data-change']);?>" class="subLink">资料变更</a></li>
				</ul>
			</li>
			<li><a href="<?= Url::to(['site/online-trading']);?>" class="subLink">网上交易</a></li>
			<li><a href="<?= Url::to(['site/download']);?>" class="subLink cur">柜台表格下载</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">柜台表格下载</h2>
		<div class="newList">
			<ul>
				<li>
					<a href="<?= Url::base();?>/doc/传真交易协议书.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						传真交易协议书
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/基金业务授权委托书.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						基金业务授权委托书
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/开放式基金交易类业务申请书（机构）.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						开放式基金交易类业务申请书（机构）
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/开放式基金特殊业务业务申请书.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						开放式基金特殊业务业务申请书
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/开放式基金业务印鉴卡.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						开放式基金业务印鉴卡
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/开放式基金账户类业务申请书（机构).pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						开放式基金账户类业务申请书（机构)
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/指定银行账户信息.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						指定银行账户信息
					</a>
				</li>
				<li>
					<a href="<?= Url::base();?>/doc/投资者权益须知.pdf" class="dbk" target="_blank">
						<span class="newsDate">2016-07-11   12:00</span>
						投资者权益须知
					</a>
				</li>
			</ul>
		</div>
	</div>
</section>

