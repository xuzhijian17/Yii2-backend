<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">投资组合</div>
			</div>
		</div>
		<div class="app_section group">
			<h2 class="section_Title"><span class="title_ico ico_gk_zhic">资产配置</span><span class="fR dateTR">2015-12-31</span></h2>
			<div class="itBlk pie">
				<div class="Statistics" id="chart" style="width:480px"></div>
				<!--<div class="pieItems pT20">
					<div class="pieItem"><span class="spot pieType01"></span>活期宝50.00%</div>
					<div class="pieItem"><span class="spot pieType02"></span>活期宝50.00%</div>
					<div class="pieItem"><span class="spot pieType03"></span>活期宝50.00%</div>
					<div class="pieItem"><span class="spot pieType04"></span>活期宝50.00%</div>
				</div>-->
			</div>
		</div>
		<!--app_section end-->
		<?php 
		foreach($data as $key=>$value){
			if($value){?>
		<div class="app_section group">
			<h2 class="section_Title"><span class="title_ico ico_gk_hangy">
				<?php if($key == 'zc'){ ?>
					行业配置
				<?php }elseif($key == 'gp'){ ?>
				重仓配置
				<?php }else{ ?>
				债券配置
				<?php } ?>
			</span><span class="fR dateTR"><?=substr($value[0]['ReportDate'], 0, 10)?></span></h2>
			<div class="equalW tableBlk">
				<ul class="tableT">
					<li><div>行业名称</div></li>
					<li><div>占净值</div></li>
					<li><div>较上期</div></li>
				</ul>
				<?php foreach($value as $val){?>
					<ul class="tableC">
						<li><div><?=$val['name']?></div></li>
						<li><div><?=$val['RatioInNV']?>%</div></li>
						<li><div>
						<?php if($val['XRatioInNV'] > 0){?>
							<span class="riseUp">+<?=$val['XRatioInNV']?>%</span>
						<?php }elseif($val['XRatioInNV'] < 0){ ?>
							<span class="fallDown"><?=$val['XRatioInNV']?>%</span>
						<?php }else{ ?>
							<span class="flat"><?=$val['XRatioInNV']?>%</span>
						<?php } ?>
						</div></li><!--颜色变化更换css涨riseUp，跌fallDown，平flat,新colBule-->
					</ul>
				<?php }?>
				
			</div>
		</div>
		<?php }
		} ?>
		<!--app_section end-->
	</div>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/echarts.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var chart = echarts.init(document.getElementById("chart"));
	chart.setOption({
		tooltip: {
			show:false,
			showContent : false
		},
		legend: {
			orient: 'vertical',
			x: 'right',
			top : '20',
			left: '360',
			data:<?=$list['name']?>
		},
		grid:{
			
		},
		series: [
			{
				type:'pie',
				radius: ['50%', '90%'],
				avoidLabelOverlap: false,
				label: {
					normal: {
						show: false,
						position: 'center'
					},
					emphasis: {
						show: true,
						textStyle: {
							fontSize: '14',
							fontWeight: 'bold'
						}
					}
				},
				labelLine: {
					normal: {
						show: false
					}
				},
				data:<?=$list['data']?>
			}
		]
	});
})
function dropDown_update(){
	dropOver();
}
function dropUp_update(){
	dropOver();
}
</script>
