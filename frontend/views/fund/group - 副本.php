<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">投资组合</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="app_section group">
			<h2 class="section_Title"><span class="title_ico ico_gk_zhic">资产配置</span><span class="fR dateTR">2015-12-31</span></h2>
			<div class="itBlk pie">
				<div class="Statistics" id="Statistics"></div>
				<div class="pieItems pT20">
					<div class="pieItem"><span class="spot pieType01"></span>活期宝50.00%</div>
					<div class="pieItem"><span class="spot pieType02"></span>活期宝50.00%</div>
					<div class="pieItem"><span class="spot pieType03"></span>活期宝50.00%</div>
					<div class="pieItem"><span class="spot pieType04"></span>活期宝50.00%</div>
				</div>
			</div>
		</div>
	</div>
<script id="result" type="text/html">
{{if show == 1}}
<div class="app_section group">
	<h2 class="section_Title"><span class="title_ico ico_gk_chic">
	{{if type == 0}}
		行业配置
	{{else if type == 1}}
		重仓配置
	{{else}}
		持仓债券
	{{/if}}
	</span><span class="fR dateTR">2015-12-31</span></h2>
		<div class="equalW tableBlk">
		{{if type == 0}}
			<ul class="tableT">
				<li><div>行业名称</div></li>
				<li><div>占净值</div></li>
				<li><div>较上期</div></li>
			</ul>
		{{else if type == 1}}
			<ul class="tableT">
				<li><div>股票名称</div></li>
				<li><div>占净值</div></li>
				<li><div>较上期</div></li>
			</ul>
		{{else}}
			<ul class="tableT">
				<li><div>债券名称</div></li>
				<li><div>占净值</div></li>
				<li><div>较上期</div></li>
			</ul>
		{{/if}}
		{{each list as val i}}
				<ul class="tableC">
					<li><div>{{val['name']}}</div></li>
					<li><div>{{val['RatioInNV']}}%</div></li>
					<li><div>
						{{if val['XRatioInNV'] > 0}}
						<span class="riseUp">+{{val['RatioInNV']}}%</span>
						{{else if val['XRatioInNV'] == 0}}
						<span class="flat">{{val['RatioInNV']}}%</span>
						{{else}}
						<span class="fallDown">-{{val['RatioInNV']}}%</span>
						{{/if}}
					</div></li><!--颜色变化更换css涨riseUp，跌fallDown，平flat,新colBule-->
				</ul>
		{{/each}}
		</div>
	</div>
{{/if}}
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/echarts.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.get("<?=Url::to('group')?>", {"InnerCode":BASE['InnerCode'], 'type':0}, function(data){
		var html = template('result', data);
		//console.log(html);
		$(".app_page").append(html);
	})
})
function dropDown_update(){
	dropOver();
}
function dropUp_update(){
	dropOver();
}
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
