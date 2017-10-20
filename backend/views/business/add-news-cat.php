<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="addOne colT">
		<ul>
			<li>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="" id="category" value="<?= isset($category)?$category:'';?>" class="textInput" placeholder="请输入分类名称" />
					</div>
				</div>
			</li>
			<li><a href="javascript:void(0);" class="buttonA add">提交</a></li>
		</ul>
	</div>
	<!--addOne-->
</section>
<script type="text/javascript">
$(document).ready(function(){
	var url = "<?= isset($category)?\yii\helpers\Url::to(['business/edit-news-cat']):\yii\helpers\Url::to(['business/add-news-cat']);?>";
	var data = {};

	$('.add').on('click', function(){
		var category = $("#category").val();
        
		if (!category) {
			return;
		}

		data = {'category':category};

        var id = $_GET['id'];
        if (id) {
            data['id'] = id;
        }

		Ajax(url,data);
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data) {
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
        		window.location.href = "<?= \yii\helpers\Url::to(['business/category-news']);?>";
        	}else{
        		alert(rs.message);
        	}
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}
</script>
