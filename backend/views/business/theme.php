<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-theme']);?>" class="buttonA">新增主题</a></li>
		</ul>
	</div>

	<div class="colT tableDiv">
		<ul class="thead">
			<li>主题名称</li>
			<li>基金数量</li>
			<li>创建时间</li>
			<li>更新时间</li>
			<li>状态</li>
			<li>是否推荐</li>
			<li>操作</li>
		</ul>
		<?php if(isset($themes) && !empty($themes)):?>
			<?php foreach($themes as $key => $value):?>
				<ul>
					<li><a class="linkA" href="<?= \yii\helpers\Url::to(['business/edit-theme','id'=>$value['id']]);?>"><?= $value['Theme'];?></a></li>
					<li><a class="linkA" href="<?= \yii\helpers\Url::to(['business/fund-list','id'=>$value['id'],'type'=>$type]);?>"><?= $value['FundNums'];?></a></li>
					<li><?= $value['InsertTime'];?></li>
					<li><?= $value['UpdateTime'];?></li>
					<li><?= $value['StateName'];?></li>
					<li><?= $value['RecommendName'];?></li>
					<li>
						<a class="linkA inLine online" data-id="<?= $value['id'];?>" data-status="<?= $value['Status']==1?0:1;?>" href="javascript:void(0);"><?= $value['Status']==1?'下线':'上线';?></a>
						<a class="linkA inLine recommend" data-id="<?= $value['id'];?>" data-recommend="<?= $value['Recommend']==1?0:1;?>" href="javascript:void(0);"><?= $value['Recommend']==1?'取消推荐':'推荐';?></a>
						<a class="linkA inLine del" data-id="<?= $value['id'];?>" href="javascript:void(0);">删除</a>
					</li>
				</ul>
			<?php endforeach;?>
		<?php endif;?>
	</div>
	<!--tableDiv-->
</section>
<script type="application/javascript">
$(document).ready(function(){
	var url = "";
	var data = {};

	/**
     * 上线主题
     */
	$('.tableDiv').on('click', '.online', function(){
		var id = $(this).attr("data-id");
		var status = $(this).attr("data-status");
		
		url = "<?= \yii\helpers\Url::to(['business/online-theme']);?>";
		data = {'id':id,Status:status};
		
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
	        		window.location.reload(true);
	        	}
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

	/**
     * 推荐主题
     */
	$('.tableDiv').on('click', '.recommend', function(){
		var id = $(this).attr("data-id");
		var recommend = $(this).attr("data-recommend");
		
		url = "<?= \yii\helpers\Url::to(['business/recommend-theme']);?>";
		data = {'id':id,'Recommend':recommend};
		
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
	        		window.location.reload(true);
	        	}
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

    /**
     * 删除主题
     */
	$('.tableDiv').on('click', '.del', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		
		url = "<?= \yii\helpers\Url::to(['business/del-theme']);?>";
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

