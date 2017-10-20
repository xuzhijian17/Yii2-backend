<?php
use yii\helpers\Url;
?>
<section class="banner cantactBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/about']);?>" class="subLink cur">公司介绍</a></li>
			<li><a href="<?= Url::to(['site/culture']);?>" class="subLink">企业文化</a></li>
			<li><a href="<?= Url::to(['site/practitioners']);?>" class="subLink">从业信息</a></li>
            <li><a href="<?= Url::to(['site/aptitude']);?>" class="subLink">资质证明</a></li>
			<li><a href="<?= Url::to(['site/contact']);?>" class="subLink">联系方式 </a></li>
			<li><a href="<?= Url::to(['site/recruit']);?>" class="subLink">诚聘英才</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">公司介绍</h2>
		<div class="newsCnt about">
			<h3 class="h3T">汇成简介</h3>
			<div class="imgText">
				<img src="<?= Url::base();?>/images/about01.jpg"/>
				<p>北京汇成基金销售有限公司成立于2015年7月，位于北京市海淀区，是一家致力于满足客户基金理财投资需求的专业公司，目前注册资本2000万人民币，于2016年3月获得由中国证券监督管理委员会颁发的独立第三方基金销售业务资格牌照。汇成基金(www.fundzone.cn)是北京汇成基金销售有限公司旗下专注基金销售服务的平台性网站。 汇成基金的管理团队是由银行、证券、基金销售公司等金融机构资深从业人员组成，该团队均拥有丰富的行业经验。团队的专业性不仅保障了我们基金销售业务处理能力和效率的高水准，而且推进了我们紧跟同行业的创新步伐，使我们整体业务保持在业内领先水平。</p>
				<p>“汇成基金”将始终秉承诚信、创新的态度，以客观、公正的视角，为投资者提供更理性、更有价值的基金理财服务，使投资者的财富实现不断增长。</p>
			</div>
			<h3 class="h3T">汇成优势</h3>
			<div class="imgText">
				<img src="<?= Url::base();?>/images/about02.jpg"/>
				<span class="inline">
					基金产品<br />
					<strong>海量基金：</strong>拥有1000多只基金，可供选择<br />
					<strong>基金推荐：</strong>根据市场及基金走势随时调研并推出优选基金，客户可随时、轻松、放心购买优基
				</span>
			</div>
			<div class="imgText">
				<img src="<?= Url::base();?>/images/about03.jpg"/>
				<span class="inline">
					现金宝<br />
					<strong>收益稳健升：</strong>现金宝中拥有多只优选货基，客户可同时持有多只基金，且随时可做基金转换，让您的收益达到最大化<br />
					<strong>T+0随心取：</strong>随时随地取现，闪电式到账
				</span>
			</div>
			<div class="imgText">
				<img src="<?= Url::base();?>/images/about04.jpg"/>
				<span class="inline">
					基金组合<br />
					<strong>风险分散化：</strong>保本、稳健、激进各种组合，满足所有投资偏好者<br />
					<strong>收益最大化：</strong>根据市场行情，策略性的调整组合，减少损失率，增大收益率
				</span>
			</div>
		</div>
	</div>
</section>

