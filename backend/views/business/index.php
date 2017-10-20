<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-news']);?>" class="buttonA">新增资讯</a></li>
		</ul>
	</div>

	<div class="colT tableDiv">
		<!-- <ul class="thead">
			<li class="wb25">标题</li>
			<li class="wb25">状态</li>
			<li class="wb25">上传时间</li>
			<li class="wb25">操作</li>
		</ul>
		<ul>
			<li>习大大访美</li>
			<li><a class="linkA" href="javascript:void(0);">已上线</a></li>
			<li>2015-11-14 09：56</li>
			<li><a class="linkA inLine" href="javascript:void(0);">下线</a><a class="linkA inLine" href="javascript:void(0);">删除</a></li>
		</ul>
		<ul>
			<li>习大大访问英国</li>
			<li><a class="linkA" href="javascript:void(0);">已上线</a></li>
			<li>2015-11-14 09：56</li>
			<li><a class="linkA inLine" href="javascript:void(0);">下线</a><a class="linkA inLine" href="javascript:void(0);">删除</a></li>
		</ul>
		<ul>
			<li>习大大回国</li>
			<li><a class="linkA" href="javascript:void(0);">已上线</a></li>
			<li>2015-11-14 09：56</li>
			<li><a class="linkA inLine" href="javascript:void(0);">下线</a><a class="linkA inLine" href="javascript:void(0);">删除</a></li>
		</ul> -->
	</div>
	<!--总量统计-->
	
</section>
