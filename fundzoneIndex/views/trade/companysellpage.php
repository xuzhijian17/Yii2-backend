			<section class="content" style="margin-bottom: 60px;">
				<h2 class="mainT">申购下单</h2>
				<div class="apply">
					<div class="applyStepWarp curStep01">
						<div class="applyBur">
							<div class="applyStep applyStep01">
								<span class="aspT">填写赎回信息</span>
								<span class="apysIco">1</span>
							</div>
							<div class="applyStep applyStep02">
								<span class="aspT">赎回信息预览</span>
								<span class="apysIco">2</span>
							</div>
							<div class="applyStep applyStep03">
								<span class="aspT">赎回下单结果</span>
								<span class="apysIco">3</span>
							</div>
							<div class="progressBar">
								<div class="progress">
									<div class="progressed"></div>
								</div>
							</div>
						</div>
						<div class="applyCnt applyCnt01">
							<div class="col2Table">
								<ul>
									<li class="label">基金代码：</li>
									<li><?php echo $data['fundcode']; ?></li>
								</ul>
								<ul>
									<li class="label">基金名称：</li>
									<li><?php echo $data['fundname']; ?></li>
								</ul>
								<ul>
									<li class="label">收费方式：</li>
									<li><?php echo $data['sharetype']=='A'?'前端收费':'后端收费'; ?></li>
								</ul>
								<ul>
									<li class="label">关联银行：</li>
									<li><?php echo $data['bankname']; ?> | <?php echo $data['bankacco']; ?></li>
								</ul>
								<ul class="itemPrt">
									<li class="label">赎回份额：</li>
									<li>
										<div class="itemSize">
											<div class="txtItem">
												<input type="text" placeholder="可用份额为<?php echo $data['usableshare']; ?>份" class="textInput amount" value="" id="" name="">
											</div>
										</div>
										<span>份</span>
									</li>
								</ul>
								<ul class="applyRst">
									<li class="label">&nbsp;</li>
									<li class="count">
										<span class="ctfLb col09f">最低可赎回份额为<span class="counterFee"><?php echo $data['minredemeshare']; ?></span>份</span>
									</li>
								</ul>
								<ul>
									<li class="label">巨额赎回时剩余部分赎回方式：</li>
									<li>
										<form action="" method="post">
											<label class="ico_radio"><input type="radio" name="redeem"  value="1" class="radio" checked="checked" />继续赎回</label>
											<label class="ico_radio"><input type="radio" name="redeem"  value="0" class="radio" />放弃超额部分</label>
										</form>
									</li>
								</ul>
								<ul class="btnWarp">
									<li class="label">&nbsp;</li>
									<li class="w275">
										<div class="btn w150"><a href="javascript:void(0);" class="submit2">确认</a></div>
									</li>
								</ul>
							</div>
							<!--col2Table end-->
						</div>
						<!--applyCnt end-->
						<div class="applyCnt applyCnt02">
							<div class="col2Table">
								<ul>
									<li class="label">基金代码：</li>
									<li><?php echo $data['fundcode']; ?></li>
								</ul>
								<ul>
									<li class="label">基金名称：</li>
									<li><?php echo $data['fundname']; ?></li>
								</ul>
								<ul>
									<li class="label">收费方式：</li>
									<li><?php echo $data['sharetype']=='A'?'前端收费':'后端收费'; ?></li>
								</ul>
								<ul>
									<li class="label">关联银行：</li>
									<li><?php echo $data['bankname']; ?> | <?php echo $data['bankacco']; ?></li>
								</ul>
								<ul>
									<li class="label">赎回份额：</li>
									<li id='applyshare'></li>
								</ul>
								<ul>
									<li class="label">巨额赎回时剩余部分赎回方式：</li>
									<li><span class="mode"></span></li>
								</ul>
								<ul class="itemPrt">
									<li class="label">交易密码：</li>
									<li>
										<div class="itemSize">
											<div class="txtItem">
												<input type="password" placeholder="请输入交易密码" class="textInput payPassword" value="" id="" name="">
											</div>
										</div>
									</li>
								</ul>
								<ul class="btnWarp">
									<li class="label">&nbsp;</li>
									<li class="w275">
										<div class="btn w150"><a href="javascript:void(0);" class="submit2">确认</a></div>
									</li>
								</ul>
							</div>
							<!--col2Table end-->
						</div>
						<!--applyCnt end-->
						<div class="applyCnt applyCnt03">
							<div class="applyState_suss applyState">
								<span class="applyStateT">恭喜您，您的赎回下单成功！</span>
								<div class="applyStateCnt">
									下单时间：<?php echo date('Y-m-d'); ?><br />
									基金代码：<?php echo $data['fundcode']; ?><br />
									基金名称：<?php echo $data['fundname']; ?><br />
									赎回份额：<span id='ordershare'>0</span>份<br />
									关联银行：<?php echo $data['bankname']; ?> | <?php echo $data['bankacco']; ?><br />
									巨额赎回时剩余部分赎回方式：<span class="mode"></span>
								</div>
								<div class="btn w150"><a href="javascript:history.go(-1)" class="submit2">完成</a></div>
								<div class="applyStateHint col96">
									<span class="fs16 col75">温馨提示：</span><br />
									赎回金额预计在T+2个工作日到关联银行卡，请您注意查看。
								</div>
							</div>
							<!--applyState_suss end-->
							<div class="applyState_fail applyState">
								<span class="applyStateT">抱歉，您的赎回下单失败！</span>
								<div class="col2Table w490">
									<ul>
										<li class="label">失败原因：</li>
										<li id='errmessage'></li>
									</ul>
								</div>
								
								<div class="btn w150"><a href="javascript:history.go(-1)" class="submit2">完成</a></div>
							</div>
							<!--applyState_fail end-->
						</div>
						<!--applyCnt end-->
					</div>
					<!--applyStepWarp end-->
				</div>
				<!--apply end--> 
			</section>

<script type="text/javascript">
var usableshare = <?php echo $data['usableshare'];?>;//可用份额
var minholdshare  =<?php echo $data['minholdshare'];?>;//最低持有
var minredemeshare = <?php echo $data['minredemeshare']; ?>;//最小赎回额
var fundcode = '<?php echo $data['fundcode']; ?>';//基金代码
var token = '<?php echo $token; ?>';
var flag = 0;//表单提交0可提交1不可提交
$(document).ready(function(){
	$(".submit2").on("click",function(){
		if($(this).parents(".applyCnt").hasClass("applyCnt01")){
			var applyShare = $.trim($(".amount").val());
			if(!IsNum(applyShare))
			{
				hintPop('申请份额不正确',"hintErrorIco");//数字判断
				return;
			}
			if(applyShare <minredemeshare)
			{
				hintPop('赎回份额不能小于'+minredemeshare,"hintErrorIco");
				return;
			}
			$('#applyshare').html(applyShare+'元');
			$(".applyStepWarp").removeClass("curStep01").addClass("curStep02");
		}else if($(this).parents(".applyCnt").hasClass("applyCnt02")){
			var thisdom = $(this);
			var mintredeem = $("input[name='redeem']:checked").val();
			if(flag ==0)
			{
				flag = 1;
				//ajax 提交开始
				$.ajax({
					type : "POST",
					url : '/trade/company-sell',
					data : {applyshare:$.trim($(".amount").val()),tradepassword:$(".payPassword").val(),fundcode:fundcode,token:token,mintredeem:mintredeem},
					dataType :'json',
					beforeSend : function(){
						//发送请求前，效果
						thisdom.addClass("noClick");
						thisdom.html('提交中...');
					},
					complete : function(){
					    //请求完成后，效果消失
						thisdom.removeClass("noClick");
						thisdom.html('已完成');
						flag = 0;
					},
					success : function(data){
						//请求成功后，数据处理
						if(data.code==0)
						{
							//成功处理
							$("#ordershare").html($(".amount").val());
							$(".applyStepWarp").removeClass("curStep02").addClass("curStep03");
							$(".applyState_suss").addClass("show");
						}else{
							//失败处理
							$(".applyStepWarp").removeClass("curStep02").addClass("curStep03");
							$(".applyState_fail").addClass("show");
							$("#errmessage").html(data.message);
						}
					},
					error : function(){
					    //请求失败后，数据处理
					},
					statusCode: {
						404: function() {alert('page not found');},
						400: function() {alert('错误请求');},
						500: function() {alert('服务器报错');},
					}
				});
			}
		};
	});
	$('.mode').html($("input[name='redeem']:checked").parent(".ico_radio").text());
	//单选框选中事件
	$("input[name='redeem']").click( function () {
		$('.mode').html($(this).parent(".ico_radio").text());
	});
});
/**
 *判断是否数字
 */
function IsNum(s)
{
    if (s!=null && s!="")
    {
        return !isNaN(s);
    }
    return false;
}
</script>






























