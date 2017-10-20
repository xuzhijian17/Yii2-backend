<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-baccount']);?>" class="buttonA">新增企业</a></li>
		</ul>
	</div>
	<div class="colT tableDiv">
		<ul class="thead">
			<li class="wb33">企业名称</li>
			<li class="wb33">交易账号</li>
			<li>操作</li>
		</ul>
		<?php if(isset($baccountData) || !empty($baccountData)):?>
			<?php foreach($baccountData as $key => $value):?>
				<ul>
					<li><?= $value['CompanyName'];?></li>
					<li><?= $value['TradeAcco'];?></li>
					<li><a class="linkA inLine" href="<?= \yii\helpers\Url::to(['business/edit-baccount','uid'=>$value['id']]);?>">编辑</a><a class="linkA inLine remove" data-id="<?= $value['id'];?>" href="javascript:void(0);">删除</a></li>
				</ul>
			<?php endforeach;?>
		<?php endif;?>
	</div>
	<!--总量统计-->
</section>
<script type="application/javascript">
$(document).ready(function(){
	var url = "";
	var data = {};

    /**
	* 删除用户
	*/
	$('.tableDiv').on('click','.remove', function(){
		var uid = $(this).attr("data-id");

		if (!window.confirm("确定删除用户")) {
			return;
		}

		$.ajax({
	        type: 'POST',
	        async: true,
	        url: "<?= \yii\helpers\Url::to(['user/remove']);?>",
	        data: {'uid':uid,'status':-2,'type':1},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		window.location.reload(true);
	        	}else{
	        		alert(rs.message);
	        	}
	        	// console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log('Ajax request error!');
	        }
	    });
	});
});
</script>