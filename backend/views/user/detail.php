<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'userinfo','uid'=>$uid,'instid'=>$instid]);?>
<section class="mine_section">
	<!--query end--> 
	<div class="wrap userDetail">
		<div class="colT tableDiv">
			
		</div>
	</div>
</section>
<script type="application/javascript">
var instid;
var openStatus;
var name;
var phone;
var card;
var page;
var totalPages;
var totalRecords;
var uid = '<?= $uid?>';
var instid = '<?= $instid?>';

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['user/detail']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
	// var uid = $_GET['uid'];
	// var instid = $_GET['instid'];
    if (uid) {
    	data = {'uid':uid,'instid':instid};

		Ajax(url, data);
    }

	/**
	* 冻结用户（后绑定事件）
	*/
	$('.userDetail').on('click', '.freeze', function(){
		var status = $('.freeze-status').val()==='0'?'-1':'0';

		$.ajax({
	        type: 'POST',
	        async: true,
	        url: "<?= \yii\helpers\Url::to(['user/freeze']);?>",
	        data: {'uid':uid,'status':status},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		if ($('.freeze-status').val()==='0') {
	        			$('.freezeTxt').text('冻结');
	        			$('.freeze').text('解冻');
	        		}else{
	        			$('.freezeTxt').text('正常');
	        			$('.freeze').text('冻结');
	        		}
	        		$('.freeze-status').val(status);
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

	/**
	* 删除用户
	*/
	$('.userDetail').on('click','.remove', function(){
		if (!window.confirm("确定删除用户")) {
			return;
		}
		// var status = $('.open-status').val()==='0'?'-2':'0';

		$.ajax({
	        type: 'POST',
	        async: true,
	        url: "<?= \yii\helpers\Url::to(['user/remove']);?>",
	        data: {'uid':uid,'status':-2},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		/*$('.open-status').val(status);
	        		if ($('.open-status').val()!=='-2') {
	        			$('.remove').text('删除用户');
	        		}else{
	        			$('.remove').text('恢复用户');
	        		}*/
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
        	$('.userDetail .tableDiv').html('');
        },
        complete: function(XMLHttpRequest, textStatus){
        	$('.wrap').show();
        },
        success: function(rs){
        	if (rs.error == 0) {
        		$('.userDetail .tableDiv').append(viewList(rs));
        	}else{
        		$('.userDetail').html(viewEmpty());
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
	
	html += '<ul><li class="w210">姓名</li>';
	html += '<li class="talgL">'+data['Name']+'</li></ul>';
	html += '<ul><li class="w210">身份证</li>';
	html += '<li class="talgL">'+data['CardID']+'</li></ul>';
	html += '<ul><li class="w210">注册时间</li>';
	html += '<li class="talgL">'+(data['SysTime']?data['SysTime']:'-')+'</li></ul>';
	html += '<ul><li class="w210">绑卡时间</li>';
	html += '<li class="talgL">'+(data['BindTime']?data['BindTime']:'-')+'</li></ul>';
	html += '<ul><li class="w210">冻结状态</li>';
	html += '<li class="talgL">';
	html += '<span class="inLine freezeTxt">'+(data['AccountStatus']==='0'?'正常':'冻结')+'</span>';
	html += '<a href="javascript:void(0);" class="buttonB inLine freeze">'+(data['AccountStatus']==='0'?'冻结':'解冻')+'</a>';
	html += '<span class="inLine colU">注：冻结状态下申购、赎回、定投均不可做！</span>';
	html += '</li><input type="hidden" class="freeze-status" value="'+data['AccountStatus']+'"></ul>';
	html += '<ul><li class="w210">是否删除用户</li>';
	html += '<li class="talgL">';
	html += '<a href="javascript:void(0);" class="buttonB inLine remove">'+(data['OpenStatus']==='-2'?'恢复用户':'删除用户')+'</a>';
	html += '<span class="inLine colU">注：请确保用户没有开户或已销户的前提下删除！</span>';
	html += '</li><input type="hidden" class="open-status" value="'+data['OpenStatus']+'"></ul>';
	
	return html;
}
</script>