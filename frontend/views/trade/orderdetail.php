	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">交易详情</div>
				<?php if($data['status'] =='1' && $data['tradeType'] =='0'){ ?>
				<div class="app_Rlink"><a href="javascript:void(0);" class="cancelOrder">撤单</a></div><!--能否撤单分别用 cancelOrder unCancelOrder-->
				<?php } ?>
			</div>
		</div>
		<!-- 未付款 start-->
		<?php if ($data['status'] ==0){ ?>
		<div class="app_section">
			<div class="rstWarp">
				<div class="payIco">
					<span class="icoBuy">买入</span>
				</div>
				<div class="payName">
					<span class="payNameT fzS28 mB20"><?php echo $data['fname']; ?></span>
					<span class="payNameC"><?php echo $data['bname']; ?>(尾号<?php echo $data['bacco']; ?>)</span>
				</div>
				<span class="buyMoney"><?php echo sprintf('%01.2f',$data['applyAmount']); ?>元</span>
			</div>
			<!--rstWarp end-->
		</div>
		<!--app_section end-->
		<div class="rstOrder">订单号：<?php echo $data['orderNo']; ?></div>
		<div class="botFix">
			<div class="rstOrder">请于<?php echo substr($data['sysTime'],5,5); ?>日15点之前完成支付，否则交易将自动关闭</div>
			<div class="button_section">
				<a class="buttonA" href="javascript:$('form[name=\'payform\']').submit();">
					立即支付
				</a>
			</div>
		</div>
		<form action="/trade/purchase-page" method='post' name='payform'>
		<input type='hidden' name='applyAmount' value="<?php echo $data['applyAmount']; ?>"><!-- 购买金额 -->
		<input type='hidden' name='fundcode' value="<?php echo $data['fundCode']; ?>">
		</form>
		<!-- 未付款 end-->
		<!-- 已付款 start-->
		<?php }else { ?>
		<div class="app_section">
			<?php if ($data['tradeType'] == '0'){ ?>
			<div class="rstWarp">
				<div class="payIco">
				    <span class="icoBuy">买入</span>
				</div>
				<div class="payName">
					<span class="payNameT fzS28 mB20"><?php echo $data['fname']; ?></span>
					<span class="payNameC"><?php echo $data['bname']; ?>(尾号<?php echo $data['bacco']; ?>)</span>
				</div>
				<span class="buyMoney"><?php echo sprintf('%01.2f',$data['applyAmount']); ?>元</span>
			</div>
			<?php if($data['status'] >=0 && $data['status'] <=2){ ?>
			<div class="rstStepWarp">
				<div class="rstStepCnt">
					<div class="rstStep rstStep01 <?php if(!empty($data['stepTime'][0]['on'])) echo 'rstStepOver'; ?>"><!--步骤完成后添加样式 rstStepOver-->
						<div class="rstIco01 rstIco"></div>
						<div class="rstT"><span class="rstInfo">申请已受理，等待确认</span><span class="rstDate"><?php echo empty($data['stepTime'][0]['day'])?'':$data['stepTime'][0]['day']; ?></span></div>
					</div>
					<!--rstStep over-->
					<div class="rstStep rstStep02 <?php if(!empty($data['stepTime'][1]['on'])) echo 'rstStepOver'; ?>">
						<div class="rstIco02 rstIco"></div>
						<div class="rstT"><span class="rstInfo">确认份额，开始计算收益</span><span class="rstDate"><?php echo empty($data['stepTime'][1]['day'])?'':$data['stepTime'][1]['day']; ?></span></div>
					</div>
					<!--rstStep over-->
					<div class="rstStep rstStep03 <?php if(!empty($data['stepTime'][2]['on'])) echo 'rstStepOver'; ?>">
						<div class="rstIco03 rstIco"></div>
						<div class="rstT"><span class="rstInfo">查看收益</span><span class="rstDate"><?php echo empty($data['stepTime'][2]['day'])?'':$data['stepTime'][2]['day']; ?></span></div>
					</div>
					<!--rstStep over-->
				</div>
			</div>
			<?php }elseif ($data['status'] ==3){ ?>
			<div class="canceOrderWarp">
				<div class="rstStepCnt">
					<div class="rstStep rstStep01 rstStepOver"><!--步骤完成后添加样式 rstStepOver-->
						<div class="rstIco01 rstIco"></div>
						<div class="rstT">
							<span class="rstInfo">交易已撤销</span>
							<span class="rstDate"><?php echo date('m-d H:i:s'); ?></span>
							<span class="col9 mT25 fzS20">资金将在T+2日退回银行卡</span>
						</div>
					</div>
					<!--rstStep over-->
				</div>
			</div>
			<?php }else { ?>
			<!-- 已过期状态 -->
			<?php } ?>
			<?php }elseif ($data['tradeType'] == '1'){ ?>
			<div class="rstWarp">
				<div class="payIco">
					<span class="icoSell">卖出</span>
				</div>
				<div class="payName">
					<span class="payNameT fzS28"><?php echo $data['fname']; ?></span>
				</div>
				<span class="buyMoney"><?php echo $data['applyShare']; ?>份</span>
			</div>
			<!--rstWarp end-->
			<?php if($data['status'] ==2){ ?>
			<div class="rstInfoList">
				<div class="borBNoL column">
					<span class="fL w130 col9">确认金额</span>
					<span class="fL col3"><?php echo sprintf('%01.2f',$data['confirmAmount']); ?>元</span>
				</div>
				<div class="borBNoL column">
					<span class="fL w130 col9">确认份额</span>
					<span class="fL col3"><?php echo sprintf('%01.2f',$data['confirmShare']); ?>元</span>
				</div>
				<div class="borBNoL column">
					<span class="fL w130 col9">确认净值</span>
					<span class="fL col3"><?php echo $data['confirmNetValue']; ?></span>
					<span class="fR col9">（<?php echo substr($data['confirmTime'], 0,10); ?>基金净值）</span>
				</div>
				<div class="borBNoL column">
					<span class="fL w130 col9">手续费</span>
					<span class="fL col3"><?php echo sprintf('%01.2f',$data['poundage']); ?>元</span>
				</div>
			</div>
			<!--rstInfoList end-->
			<?php } ?>
			<div class="rstStepWarp" style="height: 120px;">
				<div class="rstStepCnt">
					<div class="rstStep rstStep01 <?php if(!empty($data['stepTime'][0]['on'])) echo 'rstStepOver'; ?>"><!--步骤完成后添加样式 rstStepOver-->
						<div class="rstIco01 rstIco"></div>
						<div class="rstT"><span class="rstInfo">申请已受理，等待确认</span><span class="rstDate"><?php echo empty($data['stepTime'][0]['day'])?'':$data['stepTime'][0]['day']; ?></span></div>
					</div>
					<!--rstStep over-->
					<div class="rstStep rstStep03 <?php if(!empty($data['stepTime'][1]['on'])) echo 'rstStepOver'; ?>">
						<div class="rstIco03 rstIco"></div>
						<div class="rstT"><span class="rstInfo">查看收益</span><span class="rstDate"><?php echo empty($data['stepTime'][1]['day'])?'':$data['stepTime'][1]['day']; ?></span></div>
					</div>
					<!--rstStep over-->
				</div>
			</div>
			<?php } ?>
		</div>
		<!--app_section end-->
		<div class="rstOrder">订单号：<?php echo $data['orderNo']; ?></div>
		<div class="button_section">
			<a class="buttonA" href="javascript:void(0);">
				完成
			</a>
		</div>
		<?php } ?>
		<!-- 已付款 end-->
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
<div class="pullWarp"></div>
<script src="/js/jquery.min.js"></script>
<script src="/js/mine.js"></script>
<script type="text/javascript">
var orderno = '<?php echo $data['orderNo']; ?>';
$(document).ready(function(){
	var postflag = true;
	$(".unCancelOrder").on("click",function(){
		popUpOPSingle(".popUpOp","已超过02月26日 15:00，订单不能撤销。","撤单提示","我知道了")
	})
	
	$(".cancelOrder").on("click",function(){
		keyPad();
	})
	$(".pullPassWord").focus(function(){
		$(this).keyup(function(){
			thisVal=$(this).val();
			thisValLen=$(this).val().length;
			if(thisValLen==6 && postflag){
				postflag = false;
				//出发请求后台提交(撤单)
				$.ajax({
					type : "POST",
					url : '/trade/with-draw',
					data : {orderno:orderno,password:$("input[name='password']").val()},
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
								window.location.replace("/trade/order-detail?orderno="+orderno);
							},1000)
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
			}
		});
	});
})
	
</script>
