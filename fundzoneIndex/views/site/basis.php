<?php
use yii\helpers\Url;
?>
<section class="banner customerBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/call-center']);?>" class="subLink">开户指南</a></li>
			<li><a href="<?= Url::to(['site/trade-guide']);?>" class="subLink">交易指南</a></li>
			<li><a href="<?= Url::to(['site/trade-quota']);?>" class="subLink">交易限额</a></li>
			<li><a href="<?= Url::to(['site/basis']);?>" class="subLink cur">基础知识</a></li>
			<li><a href="<?= Url::to(['site/investor']);?>" class="subLink">投资者权益须知</a></li>
			<li><a href="<?= Url::to(['site/risk-warning']);?>" class="subLink">风险提示</a></li>
			<li><a href="<?= Url::to(['site/anti-money-laundering']);?>" class="subLink">反洗钱</a></li>
			<li><a href="<?= Url::to(['site/feedback']);?>" class="subLink">问题反馈</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">基础知识</h2>
		<div class="newsCnt basis">
			<dl>
				<dt>
					投资开放式基金有哪些费用？
				</dt>
				<dd class="showCnt show">
					和其他投资方式一样，投资开放式基金也需支付一些费用，具体的费用种类及费率标准投资者可以查阅有关基金的契约或招募说明书。一般说来，从投资者购买基金到赎回为止，涉及的主要费用包括投资者直接负担的费用基金运作费用。投资者直接负担的费用由投资者直接支付，包括：（1）认购费。在基金设立募集期购买基金称为认购。认购费是向在基金设立募集期内购买基金的投资者收取的费用。为了鼓励投资者在设立募集期内购买基金，许多基金设定的认购费率比基金成立后的申购费率有一定的优惠。（2）申购费：在基金成立后购买基金称为申购。申购费是在投资者申购时收取的费用。我国法律规定，申购费率不得超过申购金额的5％。目前国内的开放式基金的申购费率一般为申购金额的1%～2%，并且设多档费率，申购金额大的适用的费率也较低。（3）赎回费：赎回费是在投资者赎回时从赎回款中扣除的费用，我国法律规定，赎回费率不得超过赎回金额的3％，赎回费收入在扣除基本手续费后，余额应当归基金所有。目前国内开放式基金的赎回费率一般在1%以下。基金运作费用是为维持基金的运作，从基金资产中扣除的费用，不由投资者直接支付，具体包括：（1）基金管理费：是支付给基金管理人的费用，以负担管理基金发生的成本。基金管理费每日计提，年费率一般在1%～3%之间，我国目前一般为1.5％。（2）基金托管费：是支付给基金托管行的费用，以负担保管基金资产等发生的支出。基金托管费每日计提，年费率一般在0.25%左右。（3）其他费用：主要包括投资交易费用、基金信息披露费用、与基金相关的会计师费和律师费、持有人大会费等，这些费用也作为基金的运营成本直接从基金资产中扣除。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC icoUp">收起</a>
				</dd>
			</dl>
			<dl>
				<dt>
					基金说的“T日”是什么意思？
				</dt>
				<dd class="showCnt">
					“T日”是指直销机构在规定时间内受理投资者开户、销户、认购、申购、赎回或其他业务申请的交易日，“T+1”、“T+2”和“T+N”均指交易日。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					什么是股票基金？什么是债券基金？什么是混合基金？什么是货币市场基金？
				</dt>
				<dd class="showCnt">
					根据投资对象的不同，投资基金可分为股票基金、债券基金、混合基金、货币市场基金、期货基金、期权基金，认股权证基金等。股票基金是指以股票为投资对象的投资基金（股票投资比重占60%以上）；债券基金是指以债券为投资对象的投资基金（债券投资比重占80%以上）；混合基金是指股票和债券投资比率介于以上两类基金之间可以灵活调控的；货币市场基金是指以国库券、大额银行可转让存单、商业票据、公司债券等货币市场短期有价证券为投资对象的投资基金。 
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					什么是期货基金？什么是期权基金？什么是认股权证基金？
				</dt>
				<dd class="showCnt">
					期货基金是指以各类期货品种为主要投资对象的投资基金；期权基金是指以能分配股利的股票期权为投资对象的投资基金；认股权证基金是指以认股权证为投资对象的投资基金。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					什么是对冲基金？什么是套利基金？什么是FOF？什么是伞型基金？什么是保本基金？
				</dt>
				<dd class="showCnt">
					这是一些特殊类型的基金。对冲基金，是指以私募方式募集资金并利用杠杆融资通过投资于公开交易的证券和金融衍生产品来获取收益的证券投资基金。套利基金，又称套汇基金，是指将募集的资金主要投资于国际金融市场利用套汇技巧低买高卖进行套利以获取收益的证券投资基金。基金中的基金（FOF）。顾名思义,这类基金的投资标的就是基金,因此又被称为组合基金。基金公司集合客户资金后,再投资自己旗下或别家基金公司目前最有增值潜力的基金,搭配成一个投资组合。伞型基金。伞型基金又被称为系列基金。伞型基金的组成,是基金下有一群投资于不同标的的子基金,且各子基金的管理工作均进行。只要投资在任何一家子基金,即可任意转换到另一个子基金,不须额外负担费用。保本基金。保本基金是指通过采用投资组合保险技术，保证投资者在投资到期时至少能够获得投资本金或一定回报的证券投资基金。保本基金的投资目标是在锁定下跌风险的同时力争有机会获得潜在的高回报。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					什么是基金开放日？
				</dt>
				<dd class="showCnt">
					开放日是指为投资者办理基金申购、赎回等业务的工作日。在我国，一般来讲，证券交易所的交易日即为开放式基金的开放日。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					什么是封闭式基金？什么是开放式基金？
				</dt>
				<dd class="showCnt">
					根据运作方式的不同，证券投资基金可分为封闭式基金和开放式基金。封闭式证券投资基金，又称为固定式证券投资基金，是指基金的预定数量发行完毕，在规定的时间（也称“封闭期”）内基金资本规模不再增大或缩减的证券投资基金。从组合特点来说，它具有股权性、债权性和监督性等重要特点。开放式证券投资基金，又称为变动式证券投资基金，是指基金证券数量从而基金资本可因发行新的基金证券或投资者赎回本金而变动的证券投资基金。从组合特点来说，它具有股权性、存款性和灵活性等重要特点。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					什么是公募基金？什么是私募基金？
				</dt>
				<dd class="showCnt">
					根据募集方式不同，证券投资基金可分为公募基金和私募基金。公募基金，是指以公开发行方式向社会公众投资者募集基金资金并以证券为投资对象的证券投资基金。它具有公开性、可变现性、高规范性等特点。私募基金，指以非公开方式向特定投资者募集基金资金并以证券为投资对象的证券投资基金。它具有非公开性、募集性、大额投资性、封闭性和非上市性等特点。 
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					公募基金和私募基金的区别？
				</dt>
				<dd class="showCnt">
					公募基金是面向社会公众公开发售的基金，募集对象不固定，投资金额要求低，适宜中小投资者参与，必须遵守基金法律和法规的约束，并接受监管部门的严格监管；而私募基金只能采取非公开方式，是面向特定投资者募集发售的基金，投资金额要求高，投资资格和投资人数受限制，投资风险较高。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					申购申请何时确认？
				</dt>
				<dd class="showCnt">
					投资者在T日（申请当日15：00之前）申请申购，T+1日确认份额，投资者可在T+2日起查看盈亏变化。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
			<dl>
				<dt>
					赎回申请何时确认？
				</dt>
				<dd class="showCnt">
					投资者在T日（申请当日15：00之前）申请赎回，T+1日确认赎回，货币型基金T+2到账，非货币型基金T+4到账。
				</dd>
				<dd class="talR">
					<a href="javascript:void(0)" class="textShow buttonC">展开</a>
				</dd>
			</dl>
		</div>
	</div>
</section>
