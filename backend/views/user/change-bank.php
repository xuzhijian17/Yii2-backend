<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'userinfo','uid'=>$uid,'instid'=>$instid]);?>
<section class="mine_section">
	<!--query end--> 
	<div class="change-bank">
		
	</div>
</section>
<script type="application/javascript">
var uid = '<?= $uid?>';
var instid = '<?= $instid?>';
$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['user/change-bank']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
    if (uid) {
    	data = {'uid':uid,'instid':instid};

		Ajax(url, data);
    }
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
        	$('.change-bank').html('');
        },
        complete: function(XMLHttpRequest, textStatus){

        },
        success: function(rs){
        	if (rs.error == 0) {
        		if(rs.list.length != 0){
        			$.each(rs.list, function(i, data){
	        			$('.change-bank').append(viewList(data));
	                });
        		}else{
        			$('.change-bank').html(viewEmpty());
        		}
        		
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

function viewList(data) {
	var html = '';

	html += '<div class="colT tableDiv">';
	html += '<ul><li>旧银行：'+(data['OldBankName']?data['OldBankName']:'-')+'</li>';
	html += '<li>新银行：'+(data['NewBankName']?data['NewBankName']:'-')+'</li></ul>';
	html += '<ul><li>旧银行卡号：'+data['OldBankAcco']+'</li>';
	html += '<li>新银行卡号：'+data['NewBankAcco']+'</li></ul>';
	html += '<ul><li>绑卡时间：'+(data['OldBindTime']?data['OldBindTime']:'-')+'</li>';
	html += '<li>换卡时间：'+data['NewBindTime']+'</li></ul></div>';

	return html;
}
</script>