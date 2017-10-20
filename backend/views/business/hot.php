<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to('business/add-hot');?>" class="buttonA">新增热销</a></li>
		</ul>
	</div>

	<div class="colT tableDiv">
		<ul class="thead">
			<li>基金简称</li>
			<li>基金代码</li>
			<li>创建时间</li>
			<li>更新时间</li>
			<li>状态</li>
			<li>是否推荐</li>
			<li>操作</li>
		</ul>
		<?php if($hots):?>
			<?php foreach($hots as $key => $value):?>
				<ul>
					<li><a class="linkA" href="javascript:void(0);"><?= $value['FundName'];?></a></li>
					<li><?= $value['FundCode'];?></li>
					<li><?= $value['UpdateTime'];?></li>
					<li><?= $value['InsertTIme'];?></li>
					<li><?= $value['StatusName'];?></li>
					<li><?= $value['RecommendName'];?></li>
					<li>
						<a class="linkA inLine" href="javascript:void(0);">下线</a>
						<a class="linkA inLine" href="javascript:void(0);">撤销推荐</a>
						<a class="linkA inLine" href="javascript:void(0);">删除</a>
					</li>
				</ul>
			<?php endforeach;?>
		<?php endif;?>
	</div>
	<!--tableDiv-->
	
</section>
