
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">国泰行业混合</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="item_section">
			<div class="item_warp">
				<form>
					<div class="itemColTwo">
						<label class="textLabel">购买金额</label>
						<div class="item_input">
							<input type="tel" class="text_Input" id="fund_buy" placeholder="10.00元起购" />		
						</div>
					</div>
				</form>
			</div>
			<!--item_warp end-->
			<div class="care_line careIco">
				费率<span class="del">1.20%</span><span class="rateBuy rise">0.12%</span><span class="remind col9">(估算费用<span class="cost"></span>元，省<span class="save"></span>元)</span>
			</div>
			<div class="error_line"><!--起购金额小于最小值时添加样式visible显示错误-->
				起购金额10.00元
			</div>
		</div>
		<!--item_section end-->
		<div class="item_section">
			<h2 class="item_Title">支付方式</h2>
			<a class="payWarp lineLink" href="javascript:void(0);">
				<div class="payIco"><img src="/images/bank_ny.png" ></div>
				<div class="payName">
					<span class="payNameT">中国农业银行(尾号8769)</span>
					<span class="payNameC">单笔最大50万，单日无限额</span>
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
		<div class="itemCheckbox mLR30">
			<label class="chackBox chacked"><input name="" type="checkbox" value="" checked="checked" class="checkboxIpt" />我的风险测评结果为保守型，我同意购买超出我风险偏好的
基金。</label>
		</div>
	</div>
	
<div class="pullWarp">
	<div class="pullMine">
		<div class="pullT">
			<h2 class="pullH2">输入交易密码</h2>
		</div>
		<div class="pullC">
			<div class="passWord">
			<form>
				<div class="passWordItem">
					<input type="password" class="pullPassWord" />
				</div>
			</form>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo \yii\helpers\Url::base()//Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo \yii\helpers\Url::base()//Yii::getAlias('@web');?>/js/mine.js"></script>
<script type="text/javascript">
$(document).ready(function(e){
	//购买验证
var thisVal;
var cost;
var save;
$("#fund_buy").focus(function(){
	$(this).keyup(function(){
		thisVal=$(this).val();
		cost=(0.12*thisVal).toFixed(2);
		save=((0.12-0.11)*thisVal).toFixed(2);
		if(thisVal<100 && thisVal>0){
			$(this).parents(".item_warp").siblings(".error_line").addClass("visible").html("起购金额100.00元");
			$(".buttonA").addClass("buttonOFF");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").addClass("visible");
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").children(".cost").html(cost);
			$(this).parents(".item_warp").siblings(".care_line").children(".remind").children(".save").html(save);
		}else if(thisVal>50000){
			$(this).parents(".item_warp").siblings(".error_line").addClass("visible").html("限购金额50000.00元");
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
		thisVal=$(this).val().length;
		if(thisVal==6){
			
		}
		console.log(thisVal);
	});
});

})
</script>
