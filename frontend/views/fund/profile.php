<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">基金概况</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="app_section basic">
			<h2 class="section_Title"><span class="title_ico ico_gk_info">基金信息</span></h2>
			<div class="listRL">
				<ul class="textCnt">
					<li>
						<span class="fL">基金全称</span>
						<span class="fR"><?=$data['ChiName']?></span>
					</li>
					<li>
						<span class="fL">成立日期</span>
						<span class="fR"><?=substr($data['EstablishmentDate'], 0, 10)?></span>
					</li>
					<li>
						<span class="fL">资产规模</span>
						<span class="fR"><?=$data['NetAssetsValue']?>亿</span>
					</li>
					<li>
						<span class="fL">基金管理人</span>
						<span class="fR"><?=$data['InvestAdvisorName']?></span>
					</li>
					<li>
						<span class="fL">基金托管人</span>
						<span class="fR"><?=$data['TrusteeName']?></span>
					</li>
				</ul>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section basic">
			<h2 class="section_Title"><span class="title_ico ico_gk_qa">基金经理</span></h2>
			<div class="listLink">
			<?php foreach($data['Manager'] as $val){ ?>
				<a href="<?=Url::to(['manager', 'InnerCode'=>$this->params['base']['InnerCode'], 'args'=>$val['args']])?>" class="lineLink">
					<div class="borBNoL">
						<div class="qaInfo">
							<img src="<?php echo Yii::getAlias('@web');?>/images/photo.jpg" width="50" height="50" alt="" />
							<?=$val['Name']?>
						</div>
						<div class="qaDate"><?=substr($val['AccessionDate'], 0, 10)?>至今</div>
					</div>
				</a>
			<?php } ?>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section basic">
			<h2 class="section_Title"><span class="title_ico ico_gk_idea">投资理念</span></h2>
			<div class="textblk">
				<div class="borBNoL">
					<?=$data['InvestTarget']?>
				</div>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section basic">
			<h2 class="section_Title"><span class="title_ico ico_gk_slg">投资策略</span></h2>
			<div class="textblk">
				<div class="borBNoL">
					<?=$data['InvestOrientation']?>
				</div>
			</div>
		</div>
		<!--app_section end-->
	</div>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/echarts.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	
})
function dropDown_update(){
	dropOver();
}
function dropUp_update(){
	dropOver();
}
</script>
