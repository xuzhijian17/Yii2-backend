<div class="app_page">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:history.go(-1);" class="prevBak">返回</a></div>
			<div class="app_title">修改密码</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<form>
	<div class="form_section">
		<div class="appItem">
			<label class="appLabel">当前密码</label>
			<div class="appTextItem">
				<input type="password" class="appTextInput showRst pswOld" placeholder="请输入当前交易密码" />
				<a href="javascript:void(0)" class="appRst">清空</a>
			</div>
		</div>
	</div>
	<div class="form_section">
		<div class="appItem">
			<label class="appLabel">新密码</label>
			<div class="appTextItem">
				<input type="password" class="appTextInput showRst pswNew" placeholder="请输入新的交易密码" />
				<a href="javascript:void(0)" class="appRst">清空</a>
			</div>
		</div>
		<div class="appItem">
			<label class="appLabel">确认密码</label>
			<div class="appTextItem">
				<input type="password" class="appTextInput showRst pswNewSure" placeholder="请再次输入新的交易密码" />
				<a href="javascript:void(0)" class="appRst">清空</a>
			</div>
		</div>
	</div>
	<div class="next_section">
		<a class="buttonA buttonOFF pswOver" href="javascript:void(0);">
			完成
		</a>
	</div>
	<!--form_section end-->
	</form>
</div>
<!--psw_page end-->
<script src="<?= Yii::getAlias('@web');?>/js/mine.js"></script>
<script type="text/javascript">
$(document).ready(function(e){

//修改密码设置新密码
	var pswVal;
	var pswSureVal;
	
	$(".pswNew").focus(function(){
		$(this).keyup(function(){
			pswVal = $(this).val().length;
			pswSureVal = $(".pswNewSure").val().length;
			if(pswVal==6 && pswSureVal==6 && $(this).val()==$(".pswNewSure").val()){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else if(pswVal>6){
				hintPop('密码必须是六位数字');
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}else {
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
		});
	});

	$(".pswNewSure").focus(function(){
		$(this).keyup(function(){
			pswVal = $(".pswNew").val().length;
			pswSureVal = $(this).val().length;
			if(pswVal==6 && pswSureVal==6 && $(this).val()==$(".pswNew").val()){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else if(pswSureVal==6 || pswSureVal>6 && $(this).val()!==$(".pswNew").val()){
				hintPop('密码不一致');
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}else{
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
		});
	});
//修改密码设置新密码
	$(".pswOver").on("click",function(){
		if(!$(this).hasClass("buttonOFF")){
			$.ajax({
		        type: 'POST',
		        async: true,
		        url: '<?php echo \yii\helpers\Url::to(['setting/modify-password']);?>',
		        data: {"oldPassword":$(".pswOld").val(),"newPassword":$(".pswNew").val(),"repeatPassword":$(".pswNewSure").val()},
		        dataType: 'json',
		        beforeSend: function(XMLHttpRequest){
		        },
		        complete: function(XMLHttpRequest, textStatus){
		        },
		        success: function(rs){
		        	if (rs.error == 0) {
		        		popUpOPSingle(".popUpOp","恭喜您，新的密码设置成功，请妥善保管您的新密码。","提示","重新登录","Relogin",rs.redict_url);
		        	}else{
		        		popUpOPSingle(".popUpOp","修改密码失败，请联系客服","提示","确定");
		        	}
		        	// console.log(rs);
		        },
		        error:function(XMLHttpRequest, textStatus, errorThrown){
		            console.log(errorThrown);
		        }
		    });
		}

	});
});
</script>
