<?php
use yii\helpers\Url;
?>
	<div class="app_page pT145">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">
					<div class="appTitle">基金公告</div>
				</div>
			</div>
			<div class="navTouch">
				<div class="app_tab_nav clearfix fzS24">
					<div class="appTabItem curItem">公告</div>
					<div class="appTabItem">分红送配</div>
					<span class="curLine"></span>
				</div>
			</div>
		</div>
		<div class="drop_down">
			下拉刷新
		</div>
		<div class="app_content">
			<div class="app_tab_content noticePage clearfix">
				<div class="notice_show drop_content app_tab_content_column tab_column_cur" attr="1">
					
				</div>
				<div class="bonus_show drop_content app_tab_content_column" attr="2">
					
				</div>
			</div>
		</div>
		<!--app_content end-->
		<div class="drop_up">
			上拉加载
		</div>
	</div>
<script id="notice" type="text/html">
{{each list as value i}}
	<a class="lineLink" href="{{value['url']}}">
		<div class="borBNoL">
			<span class="col3 fzS26">
			{{value['InfoTitle'].substr(0, 40)}}
			{{if value['InfoTitle'].length > 40}}
				......
			{{/if}}
			</span>
			<span class="tBR">
				{{value['BulletinDate'].substr(0, 10)}}
			</span>
		</div>
	</a>
{{/each}}
</script>
<script id="bonus" type="text/html">
{{each list as value i}}
	<div class="borBNoL">
		<ul class="fhList">
			<li class="mB20"><span class="fL fzS26">分红方式</span><span class="fR fzS22">分红日   {{value['ReDate'].substr(0, 10)}}</span></li>
			<li><span class="fL fzS26 rise">10派{{value['ActualRatioAfterTax']}}元</span><span class="fR fzS22">派息日   {{value['ExecuteDate'].substr(0, 10)}}</span></li>
		</ul>
	</div>
{{/each}}
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/echarts.js"></script>
<script type="text/javascript">
var InnerCode = BASE['InnerCode'];
var noticePage = 1;
var noticeLoading = false;
var bonusPage = 1;
var bonusLoading = false;
function loadMoreNotice(type) {
	if (noticeLoading === false) {
		noticeLoading = true;
		$.get("<?=Url::to('notice')?>", {InnerCode:InnerCode, type:type, page: noticePage}, function(data) {
			noticePage++;
			var html = template('notice', data);
			$(".notice_show").append(html);
			noticeLoading = false;
		}, 'json');
	} else {
		return;
	}
}
function loadMoreBonus(type) {
	if (bonusLoading === false) {
		bonusLoading = true;
		$.get("<?=Url::to('notice')?>", {InnerCode:InnerCode, type:type, page: bonusPage}, function(data) {
			bonusPage++;
			var html = template('bonus', data);
			$(".bonus_show").append(html);
			bonusLoading = false;
		}, 'json');
	} else {
		return;
	}
}
$(document).ready(function(){
	loadMoreNotice(1)
	loadMoreBonus(2)
})
function dropDown_update(){
	dropOver();
}
function dropUp_update(){
	var type = $(".drop_content").attr('attr');
	if(type == 1){
		loadMoreNotice(1);
	}
	if(type == 2){
		loadMoreBonus(2)
	}
	dropOver();
}
</script>
