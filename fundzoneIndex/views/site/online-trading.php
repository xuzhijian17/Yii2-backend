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
			<li><a href="<?= Url::to(['site/online-trading']);?>" class="subLink cur">网上交易</a></li>
			<li><a href="<?= Url::to(['site/download']);?>" class="subLink">柜台表格下载</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">网上交易</h2>
		<div class="newsCnt active">
			<h3>一、申购<br />预缴款</h3>
			<p>
				投资者申请申购本基金，应在申购当日15:00之前将足额申购资金以“转账”或“电汇”方式，划入本公司在中国民生银行开立的“北京汇成基金销售有限公司监管账户”，并确保在当日交易时间内到账。
			</p>
			<h3>民生银行监管账户：</h3>
			<p>
				户  名：北京汇成基金销售有限公司 <br />
				开户行：中国民生银行总行营业部<br />
				账  号：695196430<br />
				大额支付行号：305100001016
			</p>
			<h3>办理申购申请</h3>
			<p>
				已开户的机构投资者到我司办理申购业务应携带以下材料：<br />
				（1）已填好的《开放式基金申（认）购申请表》，并加盖预留印鉴；<br />
				（2）加盖银行受理印章的“汇款凭证回单”复印件。
			</p>
			<h3>二、赎回</h3>
			<p>
				提供填好的《开放式基金赎回申请表》，并加盖预留印鉴。
			</p>
			<p class="fwB">
				备注：需将所有资料先传真至010-62680827，并电话010-56282140确认。
			</p>
				
			
		</div>
	</div>
</section>

