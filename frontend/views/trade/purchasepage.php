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
						<label class="textLabel">购买金额</label>
						<div class="item_input">
							<input type="tel" class="text_Input" id="fund_buy" placeholder="<?php echo sprintf('%01.2f',$data['minvalue']);?>元起购" <?php if(isset($param['applyAmount'])){echo "value='{$param['applyAmount']}' readonly=\"true\" ";} ?> />		
						</div>
					</div>
				</form>
			</div>
			<!--item_warp end-->
			<div class="care_line careIco">
				费率<span class="del">
				<?php echo empty($data['feerate'])?'--':sprintf('%01.2f',$data['feerate']*100); ?>%
				</span>
				<span class="rateBuy rise">
				<?php echo empty($data['discountrate'])?'--':sprintf('%01.2f',$data['discountrate']*$data['feerate']*100); ?>%
				</span>
				<span class="remind col9">(估算费用<span class="cost"></span>元，省<span class="save"></span>元)</span>
			</div>
			<div class="error_line"><!--起购金额小于最小值时添加样式visible显示错误-->
			</div>
		</div>
		<!--item_section end-->
		<div class="item_section">
			<h2 class="item_Title">支付方式</h2>
			<a class="payWarp lineLink" href="javascript:void(0);">
				<div class="payIco"><img src="<?php echo empty($data['paylist']['icno'])?'':$data['paylist']['icno']; ?>" ></div>
				<div class="payName">
					<span class="payNameT"><?php echo empty($data['paylist']['bankname'])?'':$data['paylist']['bankname']; ?>(尾号<?php echo empty($data['paylist']['bankacco'])?'':$data['paylist']['bankacco']; ?>)</span>
					<span class="payNameC">单笔最大<?php echo empty($data['paylist']['onceLimit'])?'':$data['paylist']['onceLimit']; ?>，单日<?php echo empty($data['paylist']['dayLimit'])?'无限额':$data['paylist']['dayLimit'] ?></span>
				</div>
				<span class="linkIcoR"></span>
			</a>
			<!--item_warp end-->
			<div class="tar fzS20 pTB15 mLR30">
				<a href="#" class="colA">查看帮助</a>
			</div>
		</div>
		<!--item_section end-->
		<div class="button_section">
			<a class="buttonA buttonOFF" href="javascript:void(0);">
				确认购买
			</a>
		</div>
		<?php if (!in_array($data['riskflag'],['0000','00','01'],true)){?>
		<div class="itemCheckbox mLR30">
			<label class="chackBox chacked">
			<input name="risklevel" type="checkbox" value="" checked="checked" class="checkboxIpt" /><?php echo $data['riskmsg']; ?></label>
		</div>
		<?php } ?>
	</div>
	
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
<div class="pullWarp"></div>
<form name="createOrderForm">
    <input type="hidden" name="fname" value="<?php echo $data['fundname']; ?>" /><!-- 基金名称 -->
    <input type="hidden" name="bname" value="<?php echo empty($data['paylist']['bankname'])?'':$data['paylist']['bankname']; ?>" /><!-- 银行名称 -->
    <input type="hidden" name="bacco" value="<?php echo empty($data['paylist']['bankacco'])?'':$data['paylist']['bankacco']; ?>" /><!-- 卡号 -->
    <input type="hidden" name="fundcode" value="<?php echo $data['fundcode']; ?>" /><!--基金代码 -->
    <input type="hidden" name="orderno" value="" /><!--生成订单号-->
    <input type="hidden" name=tradeacco value="<?php echo empty($data['paylist']['tradeacco'])?'':$data['paylist']['tradeacco']; ?>" />
</form>
<script src="/js/jquery.min.js"></script>
<script src="/js/mine.js"></script>
<script type="text/javascript">
$(document).ready(function(e){
//页面参数
var minvalue = <?php echo $data['minvalue']; ?>; //最小起购金额
var maxvalue = <?php echo $data['maxvalue']; ?>;//最大购买金额
var feerate = <?php echo $data['feerate']; ?>;//费率
var discountrate = <?php echo $data['discountrate']; ?>;//折扣
var postflag = true;
//购买验证
var thisVal;
var cost;
var save;
$("#fund_buy").focus(function(){
	$(this).keyup(function(){
		thisVal=$(this).val();
		cost=(feerate*thisVal).toFixed(2);
		save=(feerate*(1-discountrate)*thisVal).toFixed(2);
		if(thisVal<minvalue && thisVal>0){
			$(this).parents(".item_warp").siblings(".error_line").addClass("visible").html("起购金额"+minvalue+"元");
			$(".buttonA").addClass("buttonOFF");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").addClass("visible");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").children(".cost").html(cost);
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").children(".save").html(save);
		}else if(thisVal>maxvalue){
			$(this).parents(".item_warp").siblings(".error_line").addClass("visible").html("限购金额"+maxvalue+"元");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").removeClass("visible");
			$(".buttonA").addClass("buttonOFF");
		}else if(thisVal==""){
			$(this).parents(".item_warp").siblings(".error_line").removeClass("visible");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").removeClass("visible");
			$(".buttonA").addClass("buttonOFF");
		}else if(thisVal==0){
			$(this).val("");
			$(".buttonA").addClass("buttonOFF");
		}else{
			$(this).parents(".item_warp").siblings(".error_line").removeClass("visible");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").addClass("visible");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").children(".cost").html(cost);
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").children(".save").html(save);
			$(".buttonA").removeClass("buttonOFF");
		}
		
	});
});
$(".pullPassWord").focus(function(){
	$(this).keyup(function(){
		thisVal=$(this).val();
		thisValLen=$(this).val().length;
		if(thisValLen==6 && postflag){
			postflag = false;
			//出发请求后台提交
			$.ajax({
				type : "POST",
				url : '/trade/buy',
				data : {applyamount:$('#fund_buy').val(),password:$("input[name='password']").val(),tradeacco:$("input[name='tradeacco']").val(),
					fundcode:$("input[name='fundcode']").val(),orderno:$("input[name='orderno']").val()},
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
						setTimeout(function(){
							window.location.replace("/trade/order-detail?orderno="+$("input[name='orderno']").val());
						},1000)
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
$(".buttonA").on("click",function(){
	if(typeof($("input[name='risklevel']").prop('checked')) =='boolean' && !$("input[name='risklevel']").prop('checked'))
	{
		hintPop("您需了解此基金的风险等级");
		return;
	}
	if(!$(this).hasClass("buttonOFF")){
		keyPad();
		$.post("/trade/create-order",{fname:$("input[name='fname']").val(),bname:$("input[name='bname']").val(),
			bacco:$("input[name='bacco']").val(),fundcode:$("input[name='fundcode']").val(),applyamount:$('#fund_buy').val(),tradeacco:$("input[name='tradeacco']").val()},
			function(data){
				if(data.code =='0'){
					$("input[name='orderno']").val(data.data);
				}else{
				    alert(data.msg);
				}
			},'json'
		);
	}
});
$(".pullWarp,.backPsw").on("click",function(){
	$(".pullMine").animate({
			bottom:-790
		},200);
		$(".pullWarp").fadeOut();
})
})
</script>
