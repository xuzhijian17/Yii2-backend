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
					<li><a href="<?= Url::to(['site/open-account']);?>" class="subLink cur">开户</a></li>
					<li><a href="<?= Url::to(['site/clos-account']);?>" class="subLink">销户</a></li>
					<li><a href="<?= Url::to(['site/data-change']);?>" class="subLink">资料变更</a></li>
				</ul>
			</li>
			<li><a href="<?= Url::to(['site/online-trading']);?>" class="subLink">网上交易</a></li>
			<li><a href="<?= Url::to(['site/download']);?>" class="subLink">柜台表格下载</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">开户</h2>
		<div class="newsCnt active">
			<h3>一、普通机构投资者应提交的资料：</h3>
			<p>
				1、加盖公章的企业营业执照复印件、组织机构代码证复印件及税务登记证复印件 或  统一信用代码复印件； <br />
				2、加盖公章的法人、业务经办人有效身份证件复印件（身份证或军人证等）； <br />
				3、加盖公章的法定代表人授权经办人办理业务的《授权委托书》； <br />
				4、加盖公章及法定代表人私章的《预留印鉴卡》一式三份； <br />
				5、加盖公章的指定银行账户的银行《开户许可证》或《开立银行账户申请表》或《或指定银行出具的开户证明》复印件；<br /> 
				6、填妥的《开放式基金账户类业务申请表》，并加盖公章及法定代表人私章。 <br />
				7、需开通传真交易功能的机构投资者,需签署《传真交易协议书》一式三份。 
			</p>
			<h3>二、特殊类型机构客户应提交以下相对应的开户材料： </h3>
			<p>
				8、 QFII机构客户除了上述材料以外，还应提供《合格境外机构投资者证券投资业务许可证》复印件。<br /> 
				9、金融机构集合计划作为开户主体的，应当提供证监会核准文件或备案通过的文件。 <br />
				10、金融机构为单一客户办理的定向资产管理计划开户（以产品名义开户的），应当提供证监会出具的证券公司取得客户资产管理业务资格的证明文件、加盖管理人公章的资产管理合同首尾页复印件。<br /> 
				11、企业年金作为开户主体的，管理人应提供该年金获得人力资源和社会保障局的确认函、托管行应提供加盖公章的企业年金基金管理机构资格证书。 <br />
				12、保险公司的保险产品作为开户主体的，保险公司应提供该产品获得保监会的相关批复或备案。<br />
				13、信托公司的信托产品作为开户主体的，信托公司应提供该产品获证监会核准的文件或备案通过的文件。
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

