<?php
use yii\helpers\Url;
?>
	<section class="banner cantactBanner"><!--banner--></section>
	<section class="main maxW fixed">
		<div class="main_left">
			<ul class="subMenu">
		        <li><a href="<?= Url::to(['site/about']);?>" class="subLink">公司介绍</a></li>
				<li><a href="<?= Url::to(['site/culture']);?>" class="subLink">企业文化</a></li>
				<li><a href="<?= Url::to(['site/practitioners']);?>" class="subLink">从业信息</a></li>
	            <li><a href="<?= Url::to(['site/aptitude']);?>" class="subLink cur">资质证明</a></li>
				<li><a href="<?= Url::to(['site/contact']);?>" class="subLink">联系方式 </a></li>
				<li><a href="<?= Url::to(['site/recruit']);?>" class="subLink">诚聘英才</a></li>
	      	</ul>
		</div>
		<div class="main_right">
			<h2 class="cntT">资质证明</h2>
			<div class="newsCnt aptitude">
				<div class="scrollWarp single">
					<div class="scrollPage singlePage">
						<a href="javascript:void(0);" class="prevPage prevBut2"></a>
						<a href="javascript:void(0);" class="nextPage nextBut2"></a>
					</div>
					<div class="scrollWarp">
						<div class="scrollCnt fixed">
							<span class="scrollUnit singleShow"><img src="<?= Url::base();?>/images/zzzm.jpg"/></span>
							<span class="scrollUnit singleShow"><img src="<?= Url::base();?>/images/zzzm01.jpg"/></span>
							<span class="scrollUnit singleShow"><img src="<?= Url::base();?>/images/zzzm02.jpg"/></span>
							<span class="scrollUnit singleShow"><img src="<?= Url::base();?>/images/zzzm.jpg"/></span>
							<span class="scrollUnit singleShow"><img src="<?= Url::base();?>/images/zzzm01.jpg"/></span>
							<span class="scrollUnit singleShow"><img src="<?= Url::base();?>/images/zzzm02.jpg"/></span>
						</div>
					</div>
				</div>
				<div class="aptitudeCnt">
					<img src="<?= Url::base();?>/images/aptitude_T01.jpg"/>
					<p>在汇成基金开户时会严格验证投资人的个人信息，确认账户是投资人本人开立。投资人要求重置密码、修改重要账户信息时，会要求提供足够的个人信息，以确保是投资人本人发起。</p>
					<img src="<?= Url::base();?>/images/aptitude_T02.jpg"/>
					<p>投资人在交易中出现的资金支付和结算过程，全程受到监督银行监督，其他任何机构或个人是无法获取和挪用的，投资人的投资资金也不能被转出到其他机构或个人的账户中。</p>
					<img src="<?= Url::base();?>/images/aptitude_T03.jpg"/>
					<p>投资人的投资资金在使用一张银行卡进行申购基金后，赎回时资金只能回到该卡，保持同卡进出原则。</p>
					<img src="<?= Url::base();?>/images/aptitude_T04.jpg"/>
					<p>投资人在汇成基金的全部投资资金，依证监会（监管机关）要求独立存放并由民生银行（存管银行）监督并管理。</p>
					<img src="<?= Url::base();?>/images/aptitude_T05.jpg"/>
					<p>系统7x24小时实时监控交易，在操作出现异常情况时，账户将会被锁定；民生银行全程监督交易资金支付和结算过程，并同时承担相应责任。</p>
					<img src="<?= Url::base();?>/images/aptitude_T06.jpg"/>
					<p>汇成基金交易系统用户信息、账户信息、交易信息等进行128位的SSL加密。SSL是一种国际标准的加密及身份认证通信协议。SSL协议在通讯双方间建立起一条安全的、可信任的通讯通道。它具备：信息保密性、信息完整性、相互鉴定的安全特征。</p>
				</div>
			</div>
		</div>
	</section>

<script type="text/javascript">
$(document).ready(function(){
	scrollLoop(".scrollCnt",1,880)
});
</script>