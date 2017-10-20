<?php
use yii\helpers\Url;
?>
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">公告</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="app_content">
			<div class="detailTop">
				<h3 class="detailT"><?=$data['InfoTitle']?></h3>
				<span class="detailDate"><?=substr($data['BulletinDate'],0, 10)?></span>
			</div>
			<div class="detailCnt">
				<?=$data['Detail']?>
			</div>
		</div>
		<!--app_section end-->
	</div>