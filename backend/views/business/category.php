<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-cat']);?>" class="buttonA">新增基金类型</a></li>
		</ul>
	</div>

	<?php if($categorys):?>
		<div class="colT tableDiv">
			<ul class="thead">
				<li>ID</li>
				<li>基金类型</li>
				<li>更新时间</li>
				<li>基金数量（只）</li>
				<li>操作</li>
			</ul>
			<?php foreach($categorys as $key => $value):?>
				<ul>
					<li><?= $value['id'];?></li>
					<li><a class="linkA" href="<?= \yii\helpers\Url::to(['business/edit-cat','id'=>$value['id']]);?>"><?= $value['Category'];?></a></li>
					<li><?= $value['UpdateTime'];?></li>
					<li><?= $value['FundNums'];?></li>
					<li><a class="linkA inLine edit" href="<?= \yii\helpers\Url::to(['business/fund-list','id'=>$value['id'],'type'=>$type]);?>">编辑</a><a class="linkA inLine del" href="javascript:void(0);" data-id="<?= $value['id'];?>">删除</a></li>
				</ul>
			<?php endforeach;?>
		</div>
	<?php endif;?>
	<!--tableDiv-->
	
</section>
<script type="application/javascript">
$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/cat-list']);?>";
	var data = {};

    /**
     * 删除基金
     */
	$('.tableDiv').on('click', '.del', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		
		url = "<?= \yii\helpers\Url::to(['business/del-cat']);?>";
		data = {'id':id};
		
		$.ajax({
	        type: 'POST',
	        async: true,
	        url: url,
	        data: data,
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		t.parents('ul').fadeOut();
	        	}
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

});


</script>