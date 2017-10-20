<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75 fixBut">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">
					<div class="appTitle">历史净值</div>
				</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="drop_down">
			下拉刷新
		</div>
		<div class="app_main drop_content">
			<div class="netval_show app_section" style="min-height:800px">
				<ul class="app_tab_List_Title">
					<?php if(in_array($this->params['base']['FundTypeCode'],[1109, 1106])){ ?>
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
			<!--app_section end-->
		</div>
		<!--app_content end-->
		<div class="drop_up">
			上拉加载
		</div>
	</div>
<script id="netval" type="text/html">
{{each list as value i}}
	<ul class="tabLineList">
	{{if (type == 1109 || type == 1106)}}
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
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/echarts.js"></script>
<script type="text/javascript">
var InnerCode = BASE['InnerCode'];
var page = 1;
var loading = false;
function loadMore() {
	if (loading === false) {
		loading = true;
		$.get("<?=Url::to('netvalue')?>", {InnerCode:InnerCode, 'MSType':BASE['MSType'], page: page}, function(data) {
			page++;
			var html = template('netval', data);
			$(".netval_show").append(html);
			loading = false;
		}, 'json');
	} else {
		return;
	}
}
$(document).ready(function(){
	loadMore()
})
function dropDown_update(){
	// var page = 1;
	// loadMore();
	dropOver();
}
function dropUp_update(){
	loadMore();
	dropOver();
}
</script>
