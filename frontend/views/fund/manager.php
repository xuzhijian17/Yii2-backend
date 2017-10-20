<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">基金经理</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="app_section jjQA">
			<div class="borBNoL qaDetail">
				<div class="qaInfo">
					<img src="<?php echo Yii::getAlias('@web');?>/images/photo.jpg" width="75" height="75" alt="" />
					<?=$data['Name']?>
				</div>
				<div class="qaDate"><?=substr($data['AccessionDate'], 0, 10)?>至今</div>
			</div>
			<div class="textblk">
				<div class="borBNoL">
					<?=$data['Background']?>
				</div>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section jjQA">
			<h2 class="section_Title"><span class="title_ico ico_gk_slg">任职表现</span></h2>
			<div class="qaListT">
				<span class="w280">基金名称</span>
				<span class="w180 tac">任期</span>
				<span class="w120 tar">汇报</span>
			</div>
			<div class="drop_content">
				<div class="borBNoL qaLisC">
					<ul class="list">
						
					</ul>
				</div>
			</div>
		</div>
		<!--app_section end-->
	</div>
<script id="lilist" type="text/html">
{{each list as value i}}
	<li>
		<span class="w280">{{value['ChiNameAbbr'].substr(0, 8)}}</span>
		<span class="w180 tac">{{value['AccessionDate']}}至{{value['DimissionDate']}}</span>
		{{if value['Performance'] > 0}}
			<span class="w120 tar rise">+{{value['Performance']}}%</span>
		{{else if value['Performance'] == 0}}
			<span class="w120 tar flat">+{{value['Performance']}}%</span>
		{{else if value['Performance'] < 0}}
			<span class="w120 tar fall">{{value['Performance']}}%</span>
		{{/if}}
	</li>
{{/each}}
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script type="text/javascript">
var args = "<?=$_GET['args']?>";
var InnerCode = "<?=$_GET['InnerCode']?>";
$(document).ready(function(){
	$.get("<?=Url::to('manager')?>", {InnerCode:InnerCode, args:args}, function(data) {
		var html = template('lilist', data);
		$(".list").append(html);
	});
});
</script>