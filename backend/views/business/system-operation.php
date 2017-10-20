<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="addOne colT">
		<ul>
			<li>
				<a href="<?= \yii\helpers\Url::to(['business/open-quotation']);?>" target="_blank" class="buttonA open-quotation">开盘</a>
			</li>
			<li><a href="<?= \yii\helpers\Url::to(['business/close-quotation']);?>" target="_blank" class="buttonA close-quotation">收盘</a></li>
		</ul>
	</div>
    <hr>
    <div class="addOne" style="margin-top: 20px">
        <ul class="fixed" style="margin-bottom: 20px;">
            <li style="float: left;">
                <a href="<?= \yii\helpers\Url::to(['business/suspend-subscribe']);?>" target="_blank" class="buttonA open-quotation">暂停申购</a>
            </li>
            <li><a href="<?= \yii\helpers\Url::to(['business/recover-subscribe']);?>" target="_blank" class="buttonA close-quotation">恢复申购</a></li>
        </ul>
        <p style="color:red">注：节假日前两天货基跟短期理财停止申购</p>
    </div>
	<!--addOne-->
    <div class="outPut" style=""></div>
</section>
<script type="text/javascript">
$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/system-operation']);?>";
	var data = {};

	/*$('.close-quotation').on('click', function(){

		Ajax(url,data);
	});*/
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
                var content;
                content = '<pre>'+rs.outPut+'</pre>';
        		$(".outPut").html(content).css("overflow: scroll");
        	}else{
        		alert(rs.message);
        	}
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

function viewContent(argument) {
    var html = '';

    return html; 
}
</script>
