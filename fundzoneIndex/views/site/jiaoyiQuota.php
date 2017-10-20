<?php
use yii\helpers\Url;
?>
<section class="banner customerBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/call-center']);?>" class="subLink">开户指南</a></li>
			<li><a href="<?= Url::to(['site/trade-guide']);?>" class="subLink">交易指南</a></li>
			<li><a href="<?= Url::to(['site/trade-quota']);?>" class="subLink cur">交易限额</a></li>
			<li><a href="<?= Url::to(['site/basis']);?>" class="subLink">基础知识</a></li>
			<li><a href="<?= Url::to(['site/investor']);?>" class="subLink">投资者权益须知</a></li>
			<li><a href="<?= Url::to(['site/risk-warning']);?>" class="subLink">风险提示</a></li>
			<li><a href="<?= Url::to(['site/anti-money-laundering']);?>" class="subLink">反洗钱</a></li>
			<li><a href="<?= Url::to(['site/feedback']);?>" class="subLink">问题反馈</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">交易限额</h2>
		<div class="newsCnt graphic">
			<table border="0" cellspacing="1" cellpadding="0" class="table quotatable" bgcolor="#EEEEEE">
				<thead>
					<tr>
						<th colspan="3" align="center">公募基金单笔申购限额（元）</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="3">
							<table border="0" cellspacing="1" cellpadding="0" class="table">
								<tr>
									<td align="center" width="33%">银行名称</td>
									<td align="center" width="33%">单笔限额（元）</td>
									<td align="center" width="33%">日累计限额（元）</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" width="33%">中国银行</td>
						<td align="center" width="33%">5万</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">建设银行</td>
						<td align="center" width="33%">5万</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">工商银行</td>
						<td align="center" width="33%">5万</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">中信银行</td>
						<td align="center" width="33%">5万</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">浦发银行</td>
						<td align="center" width="33%">5万</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">兴业银行</td>
						<td align="center" width="33%">5万</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">邮储银行</td>
						<td align="center" width="33%">5000</td>
						<td align="center" width="33%">5000</td>
					</tr>
					<tr>
						<td align="center" width="33%">上海银行</td>
						<td align="center" width="33%">5000</td>
						<td align="center" width="33%">5万</td>
					</tr>
					<tr>
						<td align="center" width="33%">平安银行</td>
						<td align="center" width="33%">5000</td>
						<td align="center" width="33%">5000</td>
					</tr>
					<tr>
						<td align="center" width="33%">交通银行</td>
						<td align="center" width="33%">2万</td>
						<td align="center" width="33%">2万</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>