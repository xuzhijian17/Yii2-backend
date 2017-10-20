<?php
use yii\helpers\Url;
?>
<section class="banner financeBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li>
				<a href="javascript:void(0);" class="subLink cur">柜台业务指南</a>
				<ul class="levelMenu">
					<li><a href="<?= Url::to(['site/open-account']);?>" class="subLink">开户</a></li>
					<li><a href="<?= Url::to(['site/clos-account']);?>" class="subLink cur">销户</a></li>
					<li><a href="<?= Url::to(['site/data-change']);?>" class="subLink">资料变更</a></li>
				</ul>
			</li>
			<li><a href="<?= Url::to(['site/online-trading']);?>" class="subLink">网上交易</a></li>
			<li><a href="<?= Url::to(['site/download']);?>" class="subLink">柜台表格下载</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">销户</h2>
		<div class="newsCnt active">
			<h3>一、普通机构投资者应提交的资料：</h3>
			<p>
				办理销户需提供以下资料：<br />
				1、填妥的《开放式基金帐户基本业务申请表》，并加盖预留印鉴；<br />
				2、销户企业的企业法人营业执照正本或副本原件及加盖单位公章的复印件，事业法人、社会团体或其它组织须提供民政部门或主管部门颁发的复印件或  统一信用代码复印件，并加盖公章。<br />
				3、经办人身份证复印件，并加盖公章。
			</p>
			<p class="fwB">
				账户开户及变更业务材料需寄至我司，邮寄地址如下：<br />
				公司地址：北京市海淀区中关村e世界财富中心A座1108室<br />
				北京汇成基金销售有限公司<br />
				机构服务部（收）<br />
				电话：010-56282140<br />
				邮编：100190
			</p>
		</div>
	</div>
</section>