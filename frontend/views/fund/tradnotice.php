<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">交易须知</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="app_section notice">
			<h2 class="section_Title"><span class="title_ico ico_gk_know">买入须知</span></h2>
			<div class="listRL">
				<ul class="textCnt">
					<li>
						<span class="fL">收费方式</span>
						<span class="fR">前端</span>
					</li>
					<li>
						<span class="fL">起购金额</span>
						<span class="fR"><?=$data['LowestSumPurLL']?>元</span>
					</li>
					<li>
						<span class="fL">赎回到账时间</span>
						<span class="fR">3个工作日</span>
					</li>
					<li>
						<span class="fL">最低赎回份额</span>
						<span class="fR"><?=$data['LowestSumRedemption']?>份</span>
					</li>
					<li>
						<span class="fL">管理费</span>
						<span class="fR"><?=$data['GlForHolding']?>%</span>
					</li>
					<li>
						<span class="fL">托管费</span>
						<span class="fR"><?=$data['TgForHolding']?>%</span>
					</li>
				</ul>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section notice">
			<h2 class="section_Title"><span class="title_ico ico_gk_buy">买入费率</span></h2> 
			<div class="equalW tableBlk">
			<?php if(!in_array($this->params['base']['FundTypeCode'], [1106, 1109])){ ?>
				<ul class="tableT">
					<li><div>交易金额</div></li>
					<li><div>标准汇率</div></li>
					<li><div>优惠汇率</div></li>
				</ul>
				<?php foreach($data['SgForHolding'] as $val){?>
				<ul class="tableC">
					<li><div><?=intval($val['BeginOfApplySumInterval']/10000)?>
					<?=intval($val['EndOfApplySumInterval']/10000) ? '- '.intval($val['EndOfApplySumInterval']/10000).'万' : '万以上'?></div></li>
					<li><div><?=$val['ChargeRateDesciption']?></div></li>
					<li><div><span class="rise"><?=$val['MaximumChargeRate'] < 0.006 ? $val['ChargeRateDesciption'] : '0.6%'?></span></div></li><!--颜色变化更换css涨riseUp，跌fallDown，平flat,新colBule-->
				</ul>
				<?php } ?>
				<?php } else { ?>
				<ul class="tableC">
					<li><div>申购费</div></li>
					<li><div><span class="rise"><?=$data['SgForHolding'][0]['ChargeRateDesciption']?></span></div></li><!--颜色变化更换css涨riseUp，跌fallDown，平flat,新colBule-->
				</ul>
			<?php } ?>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section notice">
			<h2 class="section_Title"><span class="title_ico ico_gk_sell">卖出费率</span></h2>
			<div class="equalW tableBlk">
			<?php if(!in_array($this->params['base']['FundTypeCode'], [1106, 1109])){ ?>
				<ul class="tableT">
					<li><div>持有期限</div></li>
					<li><div>费率</div></li>
				</ul>
				<?php $Term = ['', '年', '月', '日'];
				foreach($data['ShForHolding'] as $val){?>
				<ul class="tableC">
					<li><div><?=$val['BeginOfHoldTermInterval']?>
					<?=$val['EndOfHoldTermInterval'] ? '- '.$val['EndOfHoldTermInterval'].$Term[$val['TermMarkUnit']] : $Term[$val['TermMarkUnit']].'以上'?></div></li>
					<li><div><span class="rise"><?=$val['ChargeRateDesciption']?></span></div></li><!--颜色变化更换css涨riseUp，跌fallDown，平flat,新colBule-->
				</ul>
				<?php } ?>
			<?php } else { ?>
				<ul class="tableC">
					<li><div>赎回费</div></li>
					<li><div><span class="rise"><?=$data['SgForHolding'][0]['ChargeRateDesciption']?></span></div></li><!--颜色变化更换css涨riseUp，跌fallDown，平flat,新colBule-->
				</ul>
			<?php } ?>
			</div>
		</div>
		<!--app_section end-->
	</div>
<script src="<?=Yii::getAlias('@web')?>/js/jquery.min.js"></script>
<script src="<?=Yii::getAlias('@web')?>/js/mine.js"></script>