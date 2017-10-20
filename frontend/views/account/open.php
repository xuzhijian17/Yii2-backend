<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?php echo Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>开户</title>
</head>
<body class="app_body">
	<div class="open_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_title">开户</div>
				<a href="javascript:void(0);" class="closeOpen">关闭</a>
			</div>
		</div>
		<div class="openTop clearfix">
			<div class="openStep openStep01 openFinish">
				<div class="openIco openIco01"></div>
				<div class="openStepText">身份验证</div>
			</div>
			<div class="openStep openStep02">
				<div class="centerLine"></div>
				<div class="openIco openIco02"></div>
				<div class="openStepText">设置密码</div>
			</div>
			<div class="openStep openStep03">
				<div class="centerLine"></div>
				<div class="openIco openIco03"></div>
				<div class="openStepText">添加银行卡</div>
			</div>
		</div>
		<!--openTop end-->
		<div class="openStepCnt openStepCnt01 stepCur"><!--当前步骤添加stepCur；结束步骤添加stepFinish；-->
			<form>
			<div class="form_section">
				<div class="appItem labelThr">
					<label class="appLabel">手机号</label>
					<div class="appTextItem">
						<input type="tel" class="appTextInput showRst telephone" placeholder="11位手机号">
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
			</div>
			<!--form_section end-->
			<div class="next_section">
				<a class="buttonA buttonOFF"  id="openStep01But" href="javascript:void(0);">
					下一步
				</a>
			</div>
			<!--button_section end-->
			<div class="openNotice">
				<span class="bankControl">民生银行全程保障交易资金安全</span>
			</div>
			</form>
		</div>
		<!--openStepCnt 01 end-->
		<div class="openStepCnt openStepCnt02">
			<form id="step01">
			<div class="form_section">
				<div class="appItem">
					<label class="appLabel">真实姓名</label>
					<div class="appTextItem">
						<input type="text" class="appTextInput showRst openName" name='name' placeholder="请输入真实姓名" />
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
				<div class="appItem">
					<label class="appLabel">身份证号</label>
					<div class="appTextItem">
						<input type="text" class="appTextInput showRst openId" name='cardid' placeholder="请输入真实身份证号" />
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
			</div>
			<!--form_section end-->
			<div class="form_section">
				<div class="appItem">
					<label class="appLabel">绑定手机</label>
					<div class="appTextItem">
						<input type="text" class="appTextInput showRst bdphone" name='phone1' value="" />
						<a href="javascript:void(0)" class="mobRight">清空</a>
					</div>
				</div>
			</div>
			<input type="hidden" name='phone' value="" />
			<input type="hidden" name='step' value="1" />
			<!--form_section end-->
			<div class="next_section">
				<a class="buttonA buttonOFF" id="openStep02But" href="javascript:void(0);">
					确认
				</a>
			</div>
			<!--button_section end-->
			<div class="openNotice">
				<span class="bankControl">民生银行全程保障交易资金安全</span>
			</div>
			</form>
		</div>
		<!--openStepCnt 02 end-->
		<div class="openStepCnt openStepCnt03">
			<form id="step02">
			<div class="form_section">
				<div class="appItem">
					<label class="appLabel">交易密码</label>
					<div class="appTextItem">
						<input type="password" class="appTextInput showRst openPassword" name='passwd' placeholder="6位数字密码" />
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
				<div class="appItem">
					<label class="appLabel">确认密码</label>
					<div class="appTextItem">
						<input type="password" class="appTextInput showRst openPasswordSure" name='passwd1' placeholder="请再次输入密码" />
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
			</div>
			<input type="hidden" name='step' value="2" />
			<!--form_section end-->
			<div class="next_section">
				<a class="buttonA buttonOFF" id="openStep03But" href="javascript:void(0);">
					确认
				</a>
			</div>
			<!--button_section end-->
			</form>
		</div>
		<!--openStepCnt 03 end-->
		<div class="openStepCnt openStepCnt04">
			<form id="step03">
			<div class="form_section">
				<div class="selectItem selectBank">
					<label class="appLabel">银行名称</label>
					<div class="appSelectItem">
						<input type="text" class="appTextInput openBank" disabled="disabled" placeholder="请选择发卡银行" />
						<input type="hidden" name='bankno' value="" />
						<a href="javascript:void(0)" class="selectR">选择</a>
					</div>
					<a class="selectA" href="javascript:void(0);"></a>
				</div>
				<div class="appItem">
					<label class="appLabel">银行卡号</label>
					<div class="appTextItem">
						<input type="number" class="appTextInput showRst openBankNum" name="bankcard" placeholder="请输入储蓄卡号" />
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
				<div class="selectItem selectSeat">
					<label class="appLabel">所在地</label>
					<div class="appSelectItem">
						<input type="text" class="appTextInput openBankSeat" disabled="disabled" placeholder="请选择所在地" />
						<input type="hidden" name='branchbank' value="" />
						<a href="javascript:void(0)" class="selectR">选择</a>
					</div>
					<a class="selectA" href="javascript:void(0);"></a>
				</div>
			</div>
			<!--form_section end-->
			<div class="form_section">
				<div class="appItem">
					<label class="appLabel">手机号</label>
					<div class="appTextItem">
						<input type="tel" class="appTextInput showRst openBankMob" name="bankphone" placeholder="11位手机号">
						<a href="javascript:void(0)" class="appRst">清空</a>
					</div>
				</div>
			</div>
			<input type="hidden" value="" name="bankname" />
			<input type="hidden" name='step' value="3" />
			<!--form_section end-->
			<div class="next_section">
				<a class="buttonA buttonOFF" id="openStep04But" href="javascript:void(0);">
					确认
				</a>
			</div>
			<!--button_section end-->
			</form>
		</div>
		<!--openStepCnt 04 end-->
	</div>
    <!--app_page end-->
<!--短信验证-->
<div class="popUp popUpVerify">
	<div class="popUpMain">
		<div class="popUpCnt">
			<div class="popUpT">短信验证码</div>
			<div class="popUptext">请输入手机尾号<i>0000</i>收到的短信验证码</div>
			<div class="popUpForm">
				<form>
					<input type="text" pattern="[0-9]*" class="popVerifyCode" id="popVerifyCode" />
					<a href="javascript:void(0);" attr='0' class="rpCode rpCodephone">重新发送</a><!--计时添加样式popSeconds-->
				</form>
			</div>
		</div>
		<div class="popUpButton">
			<a href="javascript:void(0);" class="PopCancel" id="verifyCancer">取消</a>
			<a href="javascript:void(0);" class="PopSure" id="verifySure">确定</a>
		</div>
	</div>
</div>

<!--短信验证银行余留-->
<div class="popUp popUpBankMob">
	<div class="popUpMain">
		<div class="popUpCnt">
			<div class="popUpT">短信验证码</div>
			<div class="popUptext">请输入手机尾号<i id="bankphone">0000</i>收到的短信验证码</div>
			<div class="popUpForm">
				<form>
					<input type="text" pattern="[0-9]*" class="popVerifyCode" id="BankMobCode" />
					<a href="javascript:void(0);" class="rpCode bankrpCode">重新发送</a><!--计时添加样式popSeconds-->
				</form>
			</div>
		</div>
		<div class="popUpButton">
			<a href="javascript:void(0);" class="PopCancel" id="BankMobCancer">取消</a>
			<a href="javascript:void(0);" class="PopSure" id="BankMobSure">确定</a>
		</div>
	</div>
</div>
<!--选择银行-->

<div class="bankPage selectPage">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:void(0);" class="pageBack">返回</a></div>
			<div class="app_title">请选择开户银行</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<div class="bankNotice">
		<span class="bankControl">民生银行全程保障交易资金安全</span>
	</div>
	<div class="selectBankList">
		
	</div>
</div>


<!--选择所在省-->
<div class="zonePage selectPage">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:void(0);" class="pageBack">返回</a></div>
			<div class="app_title">省</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<div class="selectZoneList province">
		
	</div>
</div>

<!--选择所在市-->
<div class="cityPage selectPage">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:void(0);" class="pageBack">返回</a></div>
			<div class="app_title">市</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<div class="selectZoneList city">
		<!--zone over-->
	</div>
</div>
<!--选择所属分行-->
<div class="branchPage selectPage">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:void(0);" class="pageBack">返回</a></div>
			<div class="app_title">所属分行</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<div class="selectZoneList bankList">
		
	</div>
</div>
<!--开户完成-->
<div class="successPage selectPage">
	<div class="app_top_fid">
		<div class="app_topbar">
			<!--<div class="app_back"><a href="javascript:void(0);" class="pageBack">返回</a></div>-->
			<div class="app_title">开户完成</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<div class="selectZoneList">
		<div class="openSuccWarp">
			<span class="openOver"></span>
			恭喜，您已完成基金开户！			
		</div>
		<div class="next_section">
			<a class="buttonA" id="openStep04But" href="<?=Url::to(['fund-market/index'])?>">
				开始选购基金
			</a>
		</div>
		<!--button_section end-->
	</div>
</div>
<script id="bank" type="text/html">
{{each list as value key}}
	<a class="lineLink bankList" href="javascript:void(0);">
		<div class="bankListCnt">
			<div class="bankIco"><img src="{{value.logo}}" /></div>
			<div class="bankInfo">
				<span class="bankName" no="{{key}}">{{value.name}}</span>
				<span class="bankSpec">单笔最大20万,每日无限额</span>
			</div>				
		</div>
	</a>
{{/each}}
</script>
<script id="province" type="text/html">
{{each list as value key}}
	<div class="zone">
		<h2 class="zoneSort">{{key}}</h2>
		{{each value as val k}}
			{{if value.length != k+1}}
				<a class="lineLink zoneList" href="javascript:void(0);" pcid="{{val.id}}"><span>{{val.name}}</span></a>
			{{else}}
				<a class="lineLink zoneList" href="javascript:void(0);" pcid="{{val.id}}">{{val.name}}</a>
			{{/if}}
		{{/each}}
	</div>
{{/each}}
</script>
<script id="city" type="text/html">
{{each list as value key}}
	<div class="zone">
		<h2 class="zoneSort">{{key}}</h2>
		{{each value as val k}}
			{{if value.length != k+1}}
				<a class="lineLink cityList" href="javascript:void(0);" pcid="{{val.id}}"><span>{{val.name}}</span></a>
			{{else}}
				<a class="lineLink cityList" href="javascript:void(0);" pcid="{{val.id}}">{{val.name}}</a>
			{{/if}}
		{{/each}}
	</div>
{{/each}}
</script>
<script id="bank_list" type="text/html">
{{each list as val k}}
	<div class="zone">
		{{if list.length != k+1}}
			<a class="lineLink branchList" href="javascript:void(0);" bankid="{{val.branchbank}}"><span>{{val.name}}</span></a>
		{{else}}
			<a class="lineLink branchList" href="javascript:void(0);" bankid="{{val.branchbank}}">{{val.name}}</a>
		{{/if}}
	</div>
{{/each}}
</script>
</body>
</html>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script type="text/javascript">
$(document).ready(function(e){
	//popUpOP(".popUpOp","测试测试测试测","试测试测试测","确定","verifySure","取消","verifyCancer")
	var telephoneVal;
	var nameVal;
	var idVal;
	var pswVal;
	var pswSureVal;
	var openBankVal;
	var openBankNumVal;
	var openBankSeatVal;
	var openBankMobVal;
	//第一步电话验证
	$(".telephone").focus(function(){
		$(this).keyup(function(){
			telephoneVal = $(this).val().length;
			if(telephoneVal==11){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else{
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
		});
	});

	//身份认证-手机认证
	$("#openStep01But").on("click",function(){
		if(!$(this).hasClass("buttonOFF")){
			$('.popUptext i').text($(".telephone").val().substr(7, 4));
			if($(".rpCode").attr('attr') == 1){return;}
			$.get('<?=Url::to('sendmsg')?>', {phone:$(".telephone").val()}, function(data){
				if(data.error == 0){
					hintPop(data.msg); return;
				}
				popUpOP(".popUpVerify");
				sendShow();
			});
		}
	});
	$(".rpCodephone").on('click', function(){
		if($(".rpCode").attr('attr') == 1){return;}
		$.get('<?=Url::to('sendmsg')?>', {phone:$(".telephone").val()}, function(){
			sendShow();
		});
	})
	function sendShow(){
		var step = 59;
		$('.rpCode').text('重新发送60');
		$(".rpCode").addClass('popSeconds');
		$(".rpCode").attr('attr', 1);
		var _res = setInterval(function()
		{   
			$('.rpCode').text('重新发送'+step);
			step-=1;
			if(step <= 0){
				$('.rpCode').text('获取验证码');
				$(".rpCode").removeClass('popSeconds');
				$(".rpCode").attr('attr', 0);
				clearInterval(_res);//清除setInterval
			}
		},1000);
	}
	////验证码确认按钮
	$("#verifySure").on("click",function(){
		var tag = $(this);
		$.get('<?=Url::to('sendmsg')?>', {code:$("#popVerifyCode").val(), type:1}, function(data){
			if(data.error == 1){
				popUpHide(tag);
				$(".openStepCnt01").animate({
					left:-640
				},500,function(){
					$(this).removeClass("stepCur").addClass("stepFinish");
				});
				$(".openStepCnt02").animate({
					left:0
				},500,function(){
					$(this).addClass("stepCur");
				});
				//$(".openStep02").addClass("openFinish");
				$('.bdphone').val($(".telephone").val());
				$('input[name="phone"]').val($(".telephone").val());
				$('.bdphone').prop('disabled', true);
			}else{
				hintPop('验证码错误');
			}
		});
	})
	////验证码取消按钮
	$("#verifyCancer").on("click",function(){
		var tag = $(this);
		popUpHide(tag)
	})
	
	//第二步姓名和身份证验证
	$(".openName").focus(function(){
		$(this).keyup(function(){
			nameVal = $(this).val().length;
			idVal = $(".openId").val().length;
			if(nameVal>=2 && idVal==18){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else{
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
		});

	$(".openId").focus(function(){
		$(this).keyup(function(){
			nameVal = $(".openName").val().length;
			idVal = $(this).val().length;
			if(nameVal>=2 && idVal==18){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else{
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
		});
	});
	
	//身份认证-确认
	$("#openStep02But").on("click",function(){
		if(!$(this).hasClass("buttonOFF")){
			popUpOP(".popUpOp","您以后添加的银行卡，需与您所填写的身份认证信息一致","确认身份信息","确定","idSure","修改","idModify")
		}
	});
	////确认按钮
	$(document).on("click","#idSure",function(){
		var tag = $(this);
		$.post('<?=Url::to('open')?>', $("#step01").serialize(), function(data){
			//alert(data.error);
			popUpHide(tag);
			$(".openStepCnt02").animate({
				left:-640
			},500,function(){
					$(this).removeClass("stepCur").addClass("stepFinish");
				});
			$(".openStepCnt03").animate({
				left:0
			},500,function(){
					$(this).addClass("stepCur");
				});
			$(".openStep02").addClass("openFinish");
		})
	})
	////取消按钮
	$(document).on("click","#idModify",function(){
		var tag = $(this);
		popUpHide(tag)
	})

	});
	//第三步密码输入
	$(".openPassword").focus(function(){
		$(this).keyup(function(){
			pswVal = $(this).val().length;
			pswSureVal = $(".openPasswordSure").val().length;
			if(pswVal==6 && pswSureVal==6 && $(this).val()==$(".openPasswordSure").val()){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else{
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
		});
	});

	$(".openPasswordSure").focus(function(){
		$(this).keyup(function(){
			pswVal = $(".openPassword").val().length;
			pswSureVal = $(this).val().length;
			if(pswVal==6 && pswSureVal==6 && $(this).val()==$(".openPassword").val()){
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").removeClass("buttonOFF");
			}else{
				$(this).parents(".form_section").siblings(".next_section").children(".buttonA").addClass("buttonOFF");
			}
			if(pswSureVal>=6 && $(this).val()!==$(".openPassword").val()){
				hintPop('两次输入密码不一致');
			}
		});
	});
	
	//设置密码完成
	$("#openStep03But").on("click",function(){
		$.post('<?=Url::to('open')?>', $("#step02").serialize(), function(data){
			if(data.error == 0){hintPop(data.msg); return;}
			if(!$(this).hasClass("buttonOFF")){
				$(".openStepCnt03").animate({
					left:-640
				},500,function(){
					$(this).removeClass("stepCur").addClass("stepFinish");
				});
				$(".openStepCnt04").animate({
					left:0
				},500,function(){
					$(this).addClass("stepCur");
				});
				$(".openStep03").addClass("openFinish");
			}
		})
	});
	
//	第四步 银行卡验证
	$(".openBankNum").focus(function(){
		$(this).keyup(function(){
				bankverify()
		});
	});

	$(".openBankMob").focus(function(){
		$(this).keyup(function(){
				bankverify()
		});
	});

	//第四步:添加银行卡银行
//打开银行选项	
	$(".selectBank").on("click",function(){
		//selectBankList
		$.get("<?=Url::to('getbank')?>", function(data){
			var html = template('bank', data);
			//console.log(html);
			$(".selectBankList").append(html);
		})
		$(".bankPage").css("display","block").animate({
			left:0
		},500);
		//$(".openBank").val("123");
	})

	
//返回按钮
	$(".pageBack").on("click",function(){
		$(this).parents(".selectPage").animate({
			left:640
		},500,function(){
			$(this).css("display","none")
		});
	});
//选择银行
	$(".selectBankList").delegate(".bankList", "click",function(){
		var sltBank=$(this).children().children().children(".bankName").html();
		var no=$(this).children().children().children(".bankName").attr('no');
		$(".openBank").val(sltBank); $("input[name='bankno']").val(no);
		bankverify()
		$(this).parents(".selectPage").animate({
			left:640
		},500,function(){
			$(this).css("display","none")
		});
	});

	//选择地区
//打开省级选项	
	$(".selectSeat").on("click",function(){
		$.get("<?=Url::to('getprovince')?>",{type:1}, function(data){
			var html = template('province', data);
			//console.log(html);
			$(".province").empty().append(html);
		})
		$(".zonePage").css("display","block").animate({
			left:0
		},500);
		//$(".openBank").val("123");
	})
//选择省级
	$(".selectZoneList").delegate('.zoneList', "click",function(){
		if($(this).children("span").length>0){
			var sltZone=$(this).children("span").html();
		}else{
			var sltZone=$(this).html();
		}
		$.get("<?=Url::to('getprovince')?>",{type:2, pid:$(this).attr('pcid')}, function(data){
			var html = template('city', data);
			//console.log(html);
			$(".city").empty().append(html);
		})
		$(".cityPage .app_title").html(sltZone);
		$(".cityPage").css("display","block").animate({
			left:0
		},500);
	});
	
//选择市级
	$(".selectZoneList").delegate('.cityList', "click",function(){
		if($(this).children("span").length>0){
			var sltCity=$(this).children("span").html();
		}else{
			var sltCity=$(this).html();
		}
		var bankno = $("input[name='bankno']").val();
		$.get("<?=Url::to('getprovince')?>",{type:3, cid:$(this).attr('pcid'), bankno:bankno}, function(data){
			var html = template('bank_list', data);
			//console.log(html);
			$(".bankList").empty().append(html);
		})
		$(".cityPage .app_title").html(sltCity);
		$(".branchPage").css("display","block").animate({
			left:0
		},500);
	});

//选择银行
	$(".selectZoneList").delegate('.branchList', "click",function(){
		if($(this).children("span").length>0){
			var sltbranch=$(this).children("span").html();
		}else{
			var sltbranch=$(this).html();
		}
		$('input[name="branchbank"]').val($(this).attr('bankid'))
		$('input[name="bankname"]').val(sltbranch)
		$(".openBankSeat").val(sltbranch);
		$(".cityPage,.zonePage,.branchPage").animate({
			left:640
		},500,function(){
			$(this).css("display","none")
		});
		bankverify();
	});
	$(".bankrpCode").on('click', function(){
		if($(".rpCode").attr('attr') == 1){return;}
		$.post('<?=Url::to('open')?>', {step:5}, function(){
			sendShow();
		});
	})
	function sendBankShow(){
		var step = 59;
		$('.rpCode').text('重新发送60');
		$(".rpCode").addClass('popSeconds');
		$(".rpCode").attr('attr', 1);
		var _res = setInterval(function()
		{   
			$('.rpCode').text('重新发送'+step);
			step-=1;
			if(step <= 0){
				$('.rpCode').text('获取验证码');
				$(".rpCode").removeClass('popSeconds');
				$(".rpCode").attr('attr', 0);
				clearInterval(_res);//清除setInterval
			}
		},1000);
	}
	//完成
	$("#openStep04But").on("click",function(){
		$("#bankphone").text($('input[name="bankphone"]').val().substr(7,4));
		if(!$(this).hasClass("buttonOFF")){
			$.post('<?=Url::to('open')?>', $("#step03").serialize(), function(data){
				sendBankShow();
			})
			popUpOP(".popUpBankMob");
		}
	});
	////验证码确认按钮
	$("#BankMobSure").on("click",function(){
		var tag = $(this);
		if(!$(this).hasClass("buttonOFF")){
			$.post('<?=Url::to('open')?>', {step:4, code:$("#BankMobCode").val()}, function(data){
				if(data.error == 1){
					popUpHide(tag);
					$(".successPage").css("display","block").animate({
						left:0
					},500,function(){
						$(this).addClass("stepCur");
					});
					//$(".openStep02").addClass("openFinish");
				}else{
					hintPop(data.msg);
				}
			})
		}
	})
	
	//完成
	// $("#openStep04But").on("click",function(){
		// if(!$(this).hasClass("buttonOFF")){
			// $.post('<?=Url::to('open')?>', $("#step03").serialize(), function(data){
				// $(".successPage").css("display","block").animate({
					// left:0
				// },500);
			// })
		// }
	// });

// 退出开户
	$(".closeOpen").on("click",function(){
		popUpOP(".popUpOp","您确定要放弃开户吗？","","继续开户","signOutSure","放弃","signOutCancer","javascript:void(0);","login.html")
	});
	$(document).on("click","#signOutSure",function(){
		var tag = $(this);
		popUpHide(tag)
	})


function bankverify(){
	openBankVal = $(".openBank").val();
	openBankNumVal = $(".openBankNum").val();
	openBankSeatVal = $(".openBankSeat").val();
	openBankMobVal = $(".openBankMob").val();
	if(openBankVal !=="" && openBankNumVal.length!='' && openBankSeatVal !=="" && openBankMobVal.length ==11){
		$("#openStep04But").removeClass("buttonOFF");
	}else{
		$("#openStep04But").addClass("buttonOFF");
	}
}
});
</script>


