			<section class="content" style="margin-bottom: 60px;">
				<h2 class="mainT">申购下单</h2>
				<div class="apply">
					<div class="applyStepWarp curStep01">
						<div class="applyBur">
							<div class="applyStep applyStep01">
								<span class="aspT">填写申购信息</span>
								<span class="apysIco">1</span>
							</div>
							<div class="applyStep applyStep02">
								<span class="aspT">申请信息预览</span>
								<span class="apysIco">2</span>
							</div>
							<div class="applyStep applyStep03">
								<span class="aspT">申请申购结果</span>
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
									<li class="label">申请金额：</li>
									<li>
										<div class="itemSize">
											<div class="txtItem">
												<input type="text" placeholder="最低起购金额为<?php echo $data['startbuyline']; ?>元" class="textInput amount" value="" id="" name="">
											</div>
										</div>
										<span>元</span>
									</li>
								</ul>
								<ul class="applyRst">
									<li class="label">&nbsp;</li>
									<li class="count">
										<span class="ctfLb">预估手续费：</span><span class="counterFee">0</span><span class="inline">元</span>
										<a class="rateRules" href="javascript:void(0);">
											费率规则
											<?php if (!empty($data['ratelist'])) {?>
											<div class="countTab">
											<table border="0" cellspacing="1" cellpadding="0" class="table">
											     <tr>
													<th>申购金额（RMB）</th>
													<th>费率</th>
												 </tr>
												 <?php 
												    foreach ($data['ratelist'] as $val)
												    {
												?>
												<tr>
													<td><?php echo $val['divintervaldes']; ?></td>
													<?php if($val['chargerateunit']==6){ ?>
													<?php if($val['minchargerate']>0.6){ ?>
													<td><span class="del"><?php echo $val['chargeratedes']; ?></span><span>0.6%</span></td>
													<?php }else{ ?>
													<td><span><?php echo $val['chargeratedes']; ?></span></td>
													<?php } ?>
													<?php }elseif ($val['chargerateunit']==7){ ?>
													<td><span><?php echo $val['chargeratedes']; ?>/笔</span></td>
													<?php } ?>
												</tr>
												<?php }?>
											</table>
											</div>
											<?php }?>
										</a>
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
									<li class="label">申请金额：</li>
									<li id='applysum'>0元</li>
								</ul>
								<ul>
									<li class="label">预估手续费：</li>
									<li id='poundage'>0元</li>
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
								<span class="applyStateT">恭喜您，您的申购下单成功！</span>
								<div class="applyStateCnt">
									下单时间：<?php echo date('Y-m-d'); ?><br />
									基金代码：<?php echo $data['fundcode']; ?><br />
									基金名称：<?php echo $data['fundname']; ?><br />
									申请金额：<span id='ordermoney'>0</span>元<br />
									关联银行：<?php echo $data['bankname']; ?> | <?php echo $data['bankacco']; ?>
								</div>
								<div class="btn w150"><a href="javascript:history.go(-1)" class="submit2">完成</a></div>
								<div class="applyStateHint col96">
									<span class="fs16 col75">温馨提示：</span><br />
									请务必在T日15：00前将款项汇至我司资金监管账户，若超过T日该笔申请将失效。
								</div>
								<div class="applyActInfo col96">
									<span class="fs16 col75">汇成资金监管账户</span><br />
									银行户名：北京汇成基金销售有限公司<br />
									开户行：中国工商银行国展支行<br />
									银行账户：888888888888
								</div>
							</div>
							<!--applyState_suss end-->
							<div class="applyState_fail applyState">
								<span class="applyStateT">抱歉，您的申购下单失败！</span>
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
//费率json数据
var rateList = <?php echo json_encode($data['ratelist'],JSON_UNESCAPED_UNICODE); ?>;
//最低起购额
var startBuyLine = <?php echo $data['startbuyline']; ?>;
var fundcode = '<?php echo $data['fundcode']; ?>';
var token = '<?php echo $token; ?>';
var flag = 0;//表单提交0可提交1不可提交
$(document).ready(function(){
	showtag = ShowRule(rateList);
	if(showtag)
	{
    	$(".amount").focus(function(){
    		$(this).keyup(function(){
    			var amountVal = $.trim($(".amount").val());//获取输入金额
    			if(amountVal.length >0 && !IsNum(amountVal))
    			{
    				hintPop('申请金额不正确',"hintErrorIco");//金额数字，非空判断
    				return;
    			}
    			fee = GetFee(rateList,amountVal);
    			$(".counterFee").html(fee.toFixed(2));
    		});
    	});
	}else{
		$('.rateRules').hide();
	}
	$(".submit2").on("click",function(){
		if($(this).parents(".applyCnt").hasClass("applyCnt01")){
			var applySum = $(".amount").val();
			if(applySum==""){
				hintPop('申请金额不能为空',"hintErrorIco");
			}else if(applySum<startBuyLine){
				hintPop('申请金额不能小于'+startBuyLine,"hintErrorIco");
			}else if(!IsNum(applySum)){
				hintPop('金额错误',"hintErrorIco");
			}else{
				$('#applysum').html(applySum+'元');
				$('#poundage').html($(".counterFee").text()+'元');
				$(".applyStepWarp").removeClass("curStep01").addClass("curStep02");
			};
		}else if($(this).parents(".applyCnt").hasClass("applyCnt02")){
			if($.trim($(".payPassword").val()) ==''){
				hintPop('请输入密码',"hintErrorIco");
				return;
			}
			var thisdom = $(this);
			if(flag ==0)
			{
				flag = 1;
				str = '已完成';
				//ajax 提交开始
				$.ajax({
					type : "POST",
					url : '/trade/company-purchase',
					data : {applysum:$(".amount").val(),tradepassword:$(".payPassword").val(),fundcode:fundcode,token:token},
					dataType :'json',
					beforeSend : function(){
						//发送请求前，效果
						thisdom.addClass("noClick");
						thisdom.html('提交中...');
					},
					complete : function(){
					    //请求完成后，效果消失
						thisdom.removeClass("noClick");
						thisdom.html(str);
						flag = 0;
					},
					success : function(data){
						//请求成功后，数据处理
						if(data.code==0)
						{
							//成功处理
							$("#ordermoney").html($(".amount").val());
							$(".applyStepWarp").removeClass("curStep02").addClass("curStep03");
							$(".applyState_suss").addClass("show");
						}else{
							if(data.code==-8){
								hintPop('密码错误',"hintErrorIco");
								str = '确认';
								return;
							}
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
/**
 * 根据输入金额，返回手续费
 */
function GetFee(feelist,amount)
{
	var fee;
    $.each(feelist,function(i,item){
    	if(item.divstandunit1==4 || item.divstandunit1==5)
        {
            n = 10000;
        }else{
            n =1;
        }
        if(item.chargeratetype=='19000')
        {
            return 0;
        }
        if(item.endivstand1 ==null)
        {
            if(amount >= item.stdivstand1*n)
            {
                if(item.chargerateunit==7){
                    fee= parseFloat(item.minchargerate);
                }else if(item.chargerateunit==6){
                    fee = amount*item.minchargerate/100;
                }else{
                    fee = 0;
                }
            }else{
            	if(amount >= item.stdivstand1*n && amount <item.endivstand1*n)
                {
                	fee = amount*item.minchargerate/100;
                }
            }
        }else{
        	if(amount >= item.stdivstand1*n && amount <item.endivstand1*n)
            {
                if(item.minchargerate >0.6)
                {
                	fee = amount*0.6/100;
                }else{
                	fee = amount*item.minchargerate/100;
                }
            }
        }
    })
    return fee;
}
/**
 * 收取营销费的不显示费率规则
 */
function ShowRule(feelist)
{
	var k = true;
	$.each(feelist,function(i,item){
		if(item.chargeratetype=='19000')
        {
            k= false;
        }
	});
 	return k;
}
</script>