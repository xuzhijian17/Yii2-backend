	<div class="app_page pT75 fixBut">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">定投详情</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="cancelOrder">撤单</a></div><!--能否撤单分别用 cancelOrder unCancelOrder-->-->
			</div>
		</div>
		<div class="app_section">
			<div class="rstWarp">
				<div class="payName">
					<span class="payNameT fzS28 mB20"><?php echo $data['fundname']; ?></span>
					<span class="payNameC"><?php echo $data['bankname']; ?>(尾号<?php echo $data['bankacco']; ?>)</span>
				</div>
				<?php if ($data['state'] == 'A'){ ?>
				<span class="fR fall">正常</span>
				<?php }elseif ($data['state'] == 'P'){ ?>
				<span class="fR rise">暂停</span>
				<?php }elseif ($data['state'] == 'H'){ ?>
				<span class="fR flat">终止</span>
				<?php } ?>
				<!--正常fall 暂停rise 终止 flat-->
			</div>
			<!--rstWarp end-->
			<div class="surelySuss">
				<ul class="w50">
					<li><span class="col6 pR5">每期定投</span><span class="col3"><?php echo $data['applysum']; ?>元</span></li>
					<li><span class="col6 pR5">累计定投</span><span class="col3"><?php echo $data['totalcfmmoney']; ?>元</span></li>
					<li><span class="col6 pR5">扣款时间</span><span class="col3">每月<?php echo $data['jyrq']; ?>日</span></li>
					<li><span class="col6 pR5">已投期数</span><span class="col3"><?php echo $data['totalsucctime']; ?></span></li>
					<li><span class="col6 pR5">签约日期</span><span class="col3"><?php echo date('Y-m-d',strtotime($data['signday'])); ?></span></li>
					<li><span class="col6 pR5">下次扣款</span><span class="col3"><?php echo date('Y-m-d',strtotime($data['nextdate'])); ?></span></li>
				</ul>
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section surely">
			<h2 class="section_Title"><span class="title_ico ico_dt_jl">定投记录</span></h2>
			<div class="surelyRecord">
			<?php if(empty($data['tradelist'])){ ?>
			<div class="no_data"></div>
			<?php }else{ 
			    foreach ($data['tradelist'] as $value) {
			        if(in_array($value['confirmflag'],[0,4]))
			        {
			            $style = 'surelySB';
			        }elseif (in_array($value['confirmflag'],[1,3])){
			            $style = 'surelyCG';
			        }else{
			            $style = 'surelyQR';
			        }
			?>
			<a class="lineLink <?php echo $style; ?>" href="javascript:void(0);">
					<div class="columnS">
						<span class="fL w180 tal"><?php echo $value['applydate']; ?></span>
						<span class="fL w220 tac"><?php echo $value['applysum']; ?>元</span>
						<span class="fR w180 fzS20 tar state"><?php echo $value['confirmstat']; ?></span>
					</div>
				</a>
			<?php } } ?>
				<!--<div class="no_data"></div>没有数据的时候用这个div-->
			</div>
		</div>
		<!--app_section end-->
		<div class="rstOrder">协议号：<?php echo $data['xyh']; ?></div>
		<div class="equalW botButton">
			<ul>
			<?php if($data['state'] == 'A'){ ?>
				<li><a href="javascript:void(0);" class="lineLink colA revise">修改</a></li>
				<li><a href="javascript:void(0);" class="lineLink colA pause">暂停</a></li>
				<li><a href="javascript:void(0);" class="lineLink colA restop">终止</a></li>
				<?php }elseif ($data['state'] =='H'){ ?>
				<li><a href="javascript:void(0);" class="lineLink colA revise unClick">修改</a></li>
				<li><a href="javascript:void(0);" class="lineLink colA regain unClick">暂停</a></li>
				<li><a href="javascript:void(0);" class="lineLink colA restop unClick">终止</a></li>
				<?php }elseif ($data['state']=='P'){ ?>
				<li><a href="javascript:void(0);" class="lineLink colA revise">修改</a></li>
				<li><a href="javascript:void(0);" class="lineLink colA regain">恢复</a></li>
				<li><a href="javascript:void(0);" class="lineLink colA restop">终止</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<form name='hiddenform' method="post" action="/trade/valuavgr-page" >
	<input type="hidden" name="xyh" value="<?php echo $data['xyh']; ?>"><!-- 协议号 -->
	<input type="hidden" name="jyrq" value="<?php echo $data['jyrq']; ?>"><!-- 交易日期 -->
	<input type="hidden" name="cycleunit" value="<?php echo $data['cycleunit']; ?>"><!-- 周期单位 -->
	<input type="hidden" name="jyzq" value="<?php echo $data['jyzq']; ?>"><!-- 交易周期 -->
	<input type="hidden" name="tradeacco" value="<?php echo $data['tradeacco']; ?>"><!-- 交易账号 -->
	<input type="hidden" name="zzrq" value="<?php echo $data['zzrq']; ?>"><!-- 终止日期 -->
	<input type="hidden" name="applysum" value="<?php echo $data['applysum']; ?>"><!-- 申请金额 -->
	<input type="hidden" name="fundcode" value="<?php echo $data['fundcode']; ?>"><!-- 基金代码 -->
	<input type="hidden" name="state" value="<?php echo $data['state']; ?>"><!-- 基金状态 -->
	</form>
<script src="/js/jquery.min.js"></script>
<script src="/js/mine.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var state;
	$(".regain,.pause,.revise,.restop").on("click",function(){
		if(!$(this).hasClass("unClick")){
			if($(this).hasClass('restop')){//终止
				popUpOP(".popUpOp","定投终止后，将不再进行定投交易，且该定投计不能恢复","交易提示","确认暂停","stopSure","取消","stopCancer")
			}else if($(this).hasClass('pause')){//暂停
				popUpOP(".popUpOp","确认暂停此定投吗","交易提示","确认暂停","pauseSure","取消","pauseCancer")
			}else if($(this).hasClass('regain')){//恢复
				popUpOP(".popUpOp","确认恢复此定投吗","交易提示","确认恢复","regainSure","取消","regainCancer")
			}else if($(this).hasClass('revise')){//修改
				$("form[name='hiddenform']").submit();
			}
// 			popUpOPSingle(".popUpOp","定投扣款日0:00-15:00不能进行相关操作","操作提示","我知道了")
		}else{
			hintPop('此单不能修改')
		}
	})
	//确定操作(停止、暂停，恢复)
	$(document).on("click","#stopSure,#pauseSure,#regainSure",function(){
		if($(this).attr("id") =='stopSure'){
			state = 'H';
		}else if($(this).attr("id") =='pauseSure'){
			state = 'P';
		}else if($(this).attr("id") =='regainSure'){
			state = 'A';
		}else{
			hintPop('操作无效');
			return false;
		}
		//出发请求后台提交
		$.ajax({
			type : "POST",
			url : '/trade/valuavgr-change',
			data : {xyh:$("input[name='xyh']").val(),jyrq:$("input[name='jyrq']").val(),cycleunit:$("input[name='cycleunit']").val(),
				jyzq:$("input[name='jyzq']").val(),tradeacco:$("input[name='tradeacco']").val(),zzrq:$("input[name='zzrq']").val(),
				state:state,applysum:$("input[name='applysum']").val()},
			dataType :'json',
// 			beforeSend : function(){
// 				//发送请求前，效果
// 				$(".stateWarp").show();
// 			},
// 			complete : function(){
// 			    //请求完成后，效果消失
// 				$(".stateSubmit").removeClass("turn").addClass("checkSuss");
// 				$(".stateText").html("申请成功");
// 			},
			success : function(data){
				//请求成功后，数据处理
				if(data.code =='0'){
					hintPop('操作成功');
				}else{
					hintPop(data.msg);
				}
			},
			error : function(){
			    //请求失败后，数据处理
				hintPop("处理失败");
			},
			statusCode: {
				404: function() {alert('page not found');},
				400: function() {alert('错误请求');},
				500: function() {alert('服务器报错');},
			}
		});
		popUpHide($(this));//弹框消失
	})
})
	
</script>
