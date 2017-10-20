<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-news-cat']);?>" class="buttonA">新增资讯分类</a></li>
		</ul>
	</div>

	<?php if($categorys):?>
		<div class="colT tableDiv">
			<ul class="thead">
				<li>ID</li>
				<li>资讯分类</li>
				<li>更新时间</li>
				<li>资讯数量（条）</li>
				<li>操作</li>
			</ul>
			<?php foreach($categorys as $key => $value):?>
				<ul>
					<li><?= $value['id'];?></li>
					<li><a class="linkA" href="<?= \yii\helpers\Url::to(['business/edit-news-cat','id'=>$value['id']]);?>"><?= $value['Category'];?></a></li>
					<li><?= $value['UpdateTime'];?></li>
					<li><?= $value['Nums'];?></li>
					<li><a class="linkA inLine edit" href="<?= \yii\helpers\Url::to(['business/news','cid'=>$value['id']]);?>">编辑</a><a class="linkA inLine del" href="javascript:void(0);" data-id="<?= $value['id'];?>">删除</a></li>
				</ul>
			<?php endforeach;?>
		</div>
	<?php endif;?>
	<!--tableDiv-->
</section>
<script type="application/javascript">
$(document).ready(function(){
	var url = "";
	var data = {};

    /**
     * 删除分类
     */
	$('.tableDiv').on('click', '.del', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		
		url = "<?= \yii\helpers\Url::to(['business/del-news-cat']);?>";
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