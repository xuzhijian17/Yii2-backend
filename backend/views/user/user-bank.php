<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'userinfo','uid'=>$uid,'instid'=>$instid]);?>
<section class="mine_section">
	<!--query end--> 
	<div class="bank-list">
		
	</div>
</section>
<script type="application/javascript">
var uid = '<?= $uid?>';
var instid = '<?= $instid?>';
$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['user/user-bank']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
    if (uid) {
    	data = {'uid':uid,'instid':instid};

		Ajax(url, data);
    }

	/**
	* 换卡权限
	*/
	$('.bank-list').on('click','.authorization', function(){
		var uid = $('.uid').val();
		var authorization = $(this).attr("data-authorization")=='0'?'1':'0';
		var t = $(this);
		$.ajax({
	        type: 'POST',
	        async: true,
	        url: "<?= \yii\helpers\Url::to(['user/authorization']);?>",
	        data: {'uid':uid,'authorization':authorization},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		t.attr("data-authorization",authorization);
	        		if (t.attr("data-authorization")=='1') {
	        			t.text('禁止换卡');
	        		}else{
	        			t.text('允许换卡');
	        		}
	        	}else{
	        		alert(rs.message);
	        	}
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log('Ajax request error!');
	        }
	    });
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
        	$('.bank-list').html('');
        },
        complete: function(XMLHttpRequest, textStatus){

        },
        success: function(rs){
        	if (rs.error == 0) {
                $('.bank-list').append(viewList(rs));
        	}else{
        		$('.bank-list').html(viewEmpty());
        		console.log(rs);
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

function viewList(data) {
	var html = '';
	
	html += '<table border="0" cellspacing="1" cellpadding="0" class="table talC">';
	html += '<tr><td>银行：'+(data['BankName']?data['BankName']:'-')+'</td>';
	html += '<td>渠道：'+(data['InstName']?data['InstName']:'-')+'</td>';
	html += '<td>状态：'+(data['Status']==='0'?'已绑卡':'未绑卡')+'&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="buttonB inLine authorization" data-authorization="'+data['Authorization']+'">'+(data['Authorization']==='1'?'禁止换卡':'允许换卡')+'</a></td></tr>';
	html += '<tr><td colspan="2">银行卡号：'+(data['BankAcco']?data['BankAcco']:'-')+'</td>';
	html += '<td width="50%">绑卡时间：'+(data['BindTime']?data['BindTime']:'-')+'</td></tr><input type="hidden" class="uid" value="'+data['id']+'"></table>';

	return html;
}
</script>