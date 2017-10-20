<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75 fixBut">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">
					<div class="appTitle2"><?=$data['ChiName']?><span><?=$data['SecuCode']?></span></div>
				</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<!--<div class="drop_down">
			下拉刷新
		</div>-->
		<div class="app_main drop_content">
			<div class="app_section">
				<div class="update">
					<?=$data['UpdateTime']?>更新
				</div>
				<div class="aFundTop">
					<ul class="aFundNameWarp">
						<li>
							<span><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '万份收益':'日涨幅';?></span>
							<?php if($data['NVDailyGrowthRate'] > 0){?>
							<span class="aFundRose rise">+<?=$data['NVDailyGrowthRate']?><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '':'%';?></span>
							<?php }elseif($data['NVDailyGrowthRate'] == 0){?>
							<span class="aFundRose flat"><?=$data['NVDailyGrowthRate']?><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '':'%';?></span>
							<?php }else{?>
							<span class="aFundRose fall"><?=$data['NVDailyGrowthRate']?><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '':'%';?></span>
							<?php }?>
						</li>
						<li>
							<span><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '七日年化':'单位净值';?></span>
							<span class="aFundVal"><?=$data['UnitNV']?><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '%':'';?></span>
						</li>
					</ul>
					<ul class="aFundInfo">
						<li class="w160 bRline">
							<span class=""><?=$data['MS']?>基金</span>
						</li>
						<li class="w160 bRline">
							<span class="riskLV riskLV0<?=$data['RiskEvaluationDM']?>"><?=$data['RiskEvaluation']?>风险</span>
						</li>
						<li>
							<span class="starLabe">晨星评级</span>
							<span class="star star0<?=$data['StarRank']?>">
								<span class="starCur"></span>
								<span class="starNone"></span>
							</span>
						</li>
					</ul>
				</div>
				<!--aFundTop end-->
			</div>
			<!--section end-->
			<div class="app_section">
				<div class="app_tab_nav clearfix">
					<div class="appTabItem chartTab curItem show" type='0'><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '万份收益':'单位净值走势';?></div>
					<div class="appTabItem chartTab show" type='1'><?=in_array($data['FundTypeCode'], [1106, 1109]) ? '七日年化收益率':'累计收益';?></div>
					<span class="curLine"></span>
				</div>
				<div class="app_tab_content aFundList clearfix">
					<div class="list_columnThr_content app_tab_content_column tab_column_cur">
						<?php if(!in_array($data['FundTypeCode'], [1106, 1109])){ ?>
						<div class="chartTip" style="margin-top:10px; display:none">
							<div class="pieItem"><span class="spot pieType07"></span>本基金:<span id="bjj" class="rise">+9.00%</span></div>
							<div class="pieItem"><span class="spot pieType08"></span>同类均值：<span id="tjj" class="rise">+2.00%</span></div>
							<div class="pieItem" style="width: 160px;"><span class="spot pieType09"></span>沪深300：<span id="hsjj" class="fall">-1.00%</span></div>
						</div>
						<?php } ?>
						<div class="chart" id="chart">
						</div>
						<div class="equalW  chartCntTab">
							<ul>
								<li><a href="javascript:void(0)" class="lineLink timeZone cur" attr="1">近一月</a></li>
								<li><a href="javascript:void(0)" class="lineLink timeZone" attr="3">近三月</a></li>
								<li><a href="javascript:void(0)" class="lineLink timeZone" attr="6">近半年</a></li>
								<li><a href="javascript:void(0)" class="lineLink timeZone" attr="12">近一年</a></li>
							</ul>
						</div>
					</div>
					<!--app_tab_content_column end-->
				</div>
			</div>
			<!--图表 end-->
			<div class="app_section">
				<div class="app_tab_nav clearfix">
					<div class="appTabItem curItem">业绩表现</div>
					<div class="appTabItem">历史净值</div>
					<span class="curLine"></span>
				</div>
				<div class="app_tab_content aFundList clearfix">
					<div class="result_show list_columnThr_content app_tab_content_column tab_column_cur">
						<ul class="app_tab_List_Title">
							<li class="w180">时间区间</li>
							<li class="w220 tac">涨跌幅</li>
							<li class="w180 tar">同类排名</li>
						</ul>
					</div>
					<!--app_tab_content_column end-->
					<div class="netval_show list_columnThr_content app_tab_content_column">
						<ul class="app_tab_List_Title">
						<?php if(in_array($data['FundTypeCode'], [1109, 1106])){ ?>
							<li class="w180">日期</li>
							<li class="w220 tac">万份收益</li>
							<li class="w180 tar">七日年化收益率</li>
						<?php }else{ ?>
							<li class="w170">日期</li>
							<li class="w155 tac">单位净值</li>
							<li class="w155 tac">累计净值</li>
							<li class="w100 tar">日涨幅</li>
						<?php } ?>
						</ul>
					</div>
					<!--app_tab_content_column end-->
				</div>
			</div>
			<!--app_section end-->
			<div class="app_section afundLink" style="margin-bottom: 0;">
				<a href="<?=Url::to(['fund/profile', 'InnerCode'=>$data['InnerCode']])?>" class="lineLink icoGK">基金概况</a>
				<a href="<?=Url::to(['fund/group', 'InnerCode'=>$data['InnerCode']])?>" class="lineLink icoZH">投资组合</a>
				<a href="<?=Url::to(['fund/tradnotice', 'InnerCode'=>$data['InnerCode']])?>" class="lineLink icoXZ">交易须知</a>
				<a href="<?=Url::to(['fund/notice', 'InnerCode'=>$data['InnerCode']])?>" class="lineLink icoGG">基金公告</a>
			</div>
		</div>
		<!--app_content end-->
		<div class="app_section afundBut equalW">
			<ul>
				<li class="butInfo">
					<span class="rate">费率</span>
					<span class="saleIco">sale</span>
					<span class="rateNum"><del><?=$data['ChargeRateDesciption']?></del>
					<?=$data['MinimumChargeRate'] > 0.006 ? '0.60%' : $data['ChargeRateDesciption'] ?>
					</span>
				</li>
				<?php if($status['buy']){ ?>
				<li><a href="<?=Url::to(['trade/purchase-page', 'code'=>$data['SecuCode']])?>" class="lineLink saleBuy">买入</a></li>
				<?php } ?>
				<?php if($status['sell']){ ?>
				<li><a href="<?=Url::to(['trade/sell-page', 'code'=>$data['SecuCode']])?>" class="lineLink saleBuy">卖出</a></li>
				<?php } ?>
				<?php if($status['invest']){ ?>
				<li><a href="<?=Url::to(['trade/valuavgr-page', 'code'=>$data['SecuCode']])?>" class="lineLink saleCast">定投</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
<script id="result" type="text/html">
{{each list as value i}}
	<ul class='tabLineList'>
		<li class="w180"><span class="col9">{{value['desc']}}</span></li>
		<li class="w220 tac">
		{{if value['RRIn'] > 0}}
			<span class="rise">+{{value['RRIn']}}%</span>
		{{else if value['RRIn'] == 0}}
			<span class="flat">+{{value['RRIn']}}%</span>
		{{else if value['RRIn'] < 0}}
			<span class="fall">{{value['RRIn']}}%</span>
		{{else}}
			<span class="flat">--</span>
		{{/if}}
		</li>
		<li class="w180 tar">
			{{if value['sort'] == 0}}
				--
			{{else}}
				{{value['sort']}}/<span class="col9">{{value['total']}}</span>
			{{/if}}
		</li>
	</ul>
{{/each}}
</script>
<script id="netval" type="text/html">
{{each list as value i}}
	<ul class="tabLineList">
	{{if (type == 1109 || type == 1106) }}
		<li class="w180"><span class="col6">{{value['EndDate']}}</span></li>
		<li class="w220 tac"><span class="col6">{{value['UnitNV']}}</span></li>
		<li class="w180 tar">
			{{if value['NVDailyGrowthRate'] > 0}}
				<span class="rise">+{{value['NVDailyGrowthRate']}}%</span>
			{{else if value['NVDailyGrowthRate'] == 0}}
				<span class="flat">+{{value['NVDailyGrowthRate']}}%</span>
			{{else}}
				<span class="fall">{{value['NVDailyGrowthRate']}}%</span>
			{{/if}}
		</li><!--span的class涨rise跌fall平flat-->
	{{else}}
		<li class="w170"><span class="col6">{{value['EndDate']}}</span></li>
		<li class="w155 tac"><span class="col6">{{value['UnitNV']}}</span></li>
		<li class="w155 tac"><span class="col6">{{value['AccumulatedUnitNV']}}</span></li>
		<li class="w100 tar">
			{{if value['NVDailyGrowthRate'] > 0}}
				<span class="rise">+{{value['NVDailyGrowthRate']}}%</span>
			{{else if value['NVDailyGrowthRate'] == 0}}
				<span class="flat">+{{value['NVDailyGrowthRate']}}%</span>
			{{else}}
				<span class="fall">{{value['NVDailyGrowthRate']}}%</span>
			{{/if}}
		</li><!--span的class涨rise跌fall平flat-->
	{{/if}}
	</ul>
{{/each}}
<a class="lineLink butlink" href="<?=Url::to(['netvalue', 'InnerCode' => $_GET['InnerCode']])?>">
	查看全部
</a>
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/echarts.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/trend.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	trendChart("<?=Url::to('line')?>", {"InnerCode":BASE['InnerCode'],'typeCode':BASE['FundTypeCode'], 'type':0, 'attr':1});
	$('.show').click(function(){
		var type = $(this).attr('type');
		if(type == 1){
			$('.chartTip').show();
		}else{
			$('.chartTip').hide();
		}
		trendChart("<?=Url::to('line')?>", {"InnerCode":BASE['InnerCode'], 'typeCode':BASE['FundTypeCode'], 'type':type, 'attr':1});
	})
	$('.timeZone').click(function(){
		var type = $('.curItem').attr('type');
		if(type == 1){
			$('.chartTip').show();
		}else{
			$('.chartTip').hide();
		}
		var attr = $(this).attr('attr');
		$(this).show().siblings().hide();
		trendChart("<?=Url::to('line')?>", {"InnerCode":BASE['InnerCode'], 'type':type, 'attr':attr});
	})
	$.get("<?=Url::to('result')?>", {"InnerCode":BASE['InnerCode'], 'MSType':BASE['MSType']}, function(data){
		var html = template('result', data);
		//console.log(html);
		$(".result_show").append(html);
	})
	$.get("<?=Url::to('netvalue')?>", {"InnerCode":BASE['InnerCode'], 'MSType':BASE['MSType']}, function(data){
		var html = template('netval', data);
		//console.log(html);
		$(".netval_show").append(html);
	})
})
function dropDown_update(){
	dropOver();
}
function dropUp_update(){
	dropOver();
}
</script>
