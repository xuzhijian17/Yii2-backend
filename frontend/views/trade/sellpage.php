	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title"><?php echo $data['fundname']; ?></div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="item_section">
			<div class="item_warp">
				<form>
					<div class="itemColTwo">
						<label class="textLabel">卖出份额</label>
						<div class="item_input">
							<input type="text" class="text_Input w380" id="fund_sell" placeholder="最多可卖出<?php echo $data['usableremainshare']; ?>份" />	<!--份数需要动态取-->
							<a class="itemFillIn" href="javascript:void(0);">全部</a><!--清空 fillRst-->
						</div>
					</div>
				</form>
			</div>
			<!--item_warp end-->
			<div class="care_line careIco">
				卖出费率<span class="rateBuy rise"><?php echo empty($data['minfree'])?0:sprintf('%01.2f',$data['minfree']*100); ?>%-<?php echo empty($data['maxfree'])?0:sprintf('%01.2f',$data['maxfree']*100); ?>%</span>
				<a href="javascript:void(0);" class="sell colA fR">费率详情</a>
			</div>
			<div class="error_line visible" style="display: none;"><!--起购金额小于最小值时添加样式visible显示错误-->
				所剩份额不能小于最低持有份额，需将所剩份额一起卖出
			</div>
			<div class="sellRemind">
				<ul>
					<li>
						15:00后卖出，将按<?php echo $data['nextday']; ?>的净值计算金额，并收取手续费	
					</li>
				</ul>
			</div>
		</div>
		<!--item_section end-->
		<div class="button_section">
			<a class="buttonA buttonOFF" href="javascript:void(0);">
				确认卖出
			</a>
		</div>
	</div>

<!--费率详情-->
<div class="ratePop">
	<div class="ratePopCnt">
		<div class="ratePopT">费率的高低根据基金持有时间长短来决定。基金卖出时一般按照先进先出原则，实际费用收取以基金公司计算为准。</div>
		<div class="listRL">
			<ul class="textCnt">
			    <?php if (!empty($data['section'])){ foreach ($data['section'] as $val){ ?>
			    <li><span class="fL"><?php echo empty($val['IntervalDescription'])?'此基金无赎回费':$val['IntervalDescription']; ?></span>
			    <span class="fR"><?php echo $val['ChargeRateDesciption']; ?></span></li>
			    <?php }} ?>
			</ul>
		</div>
	</div>
</div>
<!--密码-->
<div class="pullMine">
	<div class="pullT">
		<h2 class="pullH2"><a class="backPsw" href="javascript:void(0);">返回</a>输入交易密码</h2>
	</div>
	<div class="pullC">
		<div class="passWord">
		<form>
			<div class="passWordItem">
				<input type="password" name="password" class="pullPassWord pullPassWordhidden" id="passwordHidden" value="" autofocus="autofocus" />
				<!--<input type="password" class="pullPassWord" id="password" value="" />
				<div class="passwordCover"></div>-->
			</div>
		</form>
			<div class="stateWarp">
				<div class="stateSubmit turn"></div><!--校验成功将turn 替换为 checkSuss-->
				<div class="stateText">校验中</div>
			</div>
		</div>
	</div>
</div>
<input type='hidden' name='minshare' value='<?php echo $data['minshare']; ?>' />
<input type='hidden' name='tradeacco' value='<?php echo $data['tradeacco']; ?>' />
<input type='hidden' name='sharetype' value='<?php echo $data['sharetype']; ?>' />
<input type='hidden' name='fundcode' value='<?php echo $data['fundcode']; ?>' />
<input type='hidden' name='fundname' value='<?php echo $data['fundname']; ?>' />
<div class="pullWarp"></div>
<script src="/js/jquery.min.js"></script>
<script src="/js/mine.js"></script>
<script type="text/javascript">
var postflag = true;
$(document).ready(function(e){
	//购买验证
var thisVal;
var maxNum = parseFloat(<?php echo $data['usableremainshare']; ?>);
var minshare = parseFloat($("input[name='minshare']").val());

//份额输入框
$("#fund_sell").focus(function(){
	$(this).keyup(function(){
		thisVal=parseFloat($.trim($(this).val()));
		if(isNaN(thisVal)){
			$(this).val("");
			$(".buttonA").addClass("buttonOFF");
			$(".itemFillIn").removeClass("fillRst");
		}else if(thisVal<minshare){
			$(this).parents(".item_warp").siblings(".error_line").slideDown();
			$(".buttonA").addClass("buttonOFF");
			$(".itemFillIn").addClass("fillRst");
		}else if(thisVal>maxNum){
			$(this).val(maxNum);
			$(".itemFillIn").addClass("fillRst");
		}else if(thisVal==""){
			$(this).parents(".item_warp").siblings(".error_line").slideUp();
			$(".buttonA").addClass("buttonOFF");
			$(".itemFillIn").removeClass("fillRst");
		}else if(thisVal==0){
			$(this).val("");
			$(".buttonA").addClass("buttonOFF");
			$(".itemFillIn").removeClass("fillRst");
		}else{
			$(this).parents(".item_warp").siblings(".error_line").slideUp();
			$(".buttonA").removeClass("buttonOFF");
			$(".itemFillIn").addClass("fillRst");
		}
		
	});
});
//密码键盘
$(".pullPassWord").focus(function(){
	$(this).keyup(function(){
		thisVal=$(this).val();
		thisValLen=$(this).val().length;
		if(thisValLen==6 && postflag){
			postflag = false;
			//出发请求后台提交
			$.ajax({
				type : "POST",
				url : '/trade/sell',
				data : {applysum:$('#fund_sell').val(),password:$("input[name='password']").val(),tradeacco:$("input[name='tradeacco']").val(),
					fundcode:$("input[name='fundcode']").val(),sharetype:$("input[name='sharetype']").val(),fundname:$("input[name='fundname']").val()},
				dataType :'json',
				beforeSend : function(){
					//发送请求前，效果
					$(".stateWarp").show();
				},
				complete : function(){
				    //请求完成后，效果消失
					$(".stateWarp").hide();
				},
				success : function(data){
					//请求成功后，数据处理
					if(data.code =='0'){
						$(".stateSubmit").removeClass("turn").addClass("checkSuss");
						$(".stateText").html("申请成功");
						if(data.data != '0'){
							setTimeout(function(){
								window.location.replace("/trade/order-detail?orderno="+data.data);
							},1000)
						}
					}else{
						hintPop(data.msg);
					}
				},
				error : function(){
				    //请求失败后，数据处理
					$(".stateWarp").hide();
					hintPop("处理失败");
				},
				statusCode: {
					404: function() {alert('page not found');},
					400: function() {alert('错误请求');},
					500: function() {alert('服务器报错');},
				}
			});
		}
	});
});
//按钮
$(".buttonA").on("click",function(){
	if(!$(this).hasClass("buttonOFF")){
		passwordHidden
		$(".pullPassWord").val("");
		$(".pullMine").animate({
			bottom:0
		},200);
		$(".pullWarp").fadeIn();
		$(".pullPassWord").focus();
		setTimeout(function(){
			$(document).scrollTop(0);
		},100)
	}
});
//费率详情
var popH = $(".ratePop").height();
$(".ratePop").css({
	"margin-top":-popH/2,
	top:-popH
})
$(".sell").on("click",function(){
	//console.log(popH);
	$(".ratePop").animate({
		top:"50%"
	},500);
	$(".pullWarp").fadeIn();
})
//关闭弹出
$(".pullWarp,.backPsw").on("click",function(){
	$(".pullMine").animate({
		bottom:-790
	},200);
	$(".ratePop").animate({
		top:-popH
	},500);
	$(".pullWarp").fadeOut();
})

$(".itemFillIn").on("click",function(){
	if($(this).hasClass("fillRst")){
		$("#fund_sell").val("");
	}else{
		$("#fund_sell").val(maxNum);
		$(".buttonA").removeClass("buttonOFF");
	}
	$(this).toggleClass("fillRst");
	$(this).parents(".item_warp").siblings(".error_line").slideUp();
});

})
</script>
