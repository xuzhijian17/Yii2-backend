<div class="bannerLogin">
			<div class="maxW">
				<div class="loginWarp">
					<h2 class="loginT"><img src="/images/loginTop.jpg"/></h2>
					<div class="p3020">
						<div class="form loginForm">
							<div class="itemWarp">
								<div class="txtitem icoLabel">
									<label class="txtLabel userLabel">
										<span></span>
									</label>
									<div class="item">
										<a href="javascript:void(0);" class="empty">X</a>
										<input type="text" name="" id="account" value="" placeholder="请输入登录账号" class="txtInput" />
									</div>
								</div>
								<!--txtitem end-->
								<div class="error" id="account_err"><!--显示错误添加样式 show-->
									*请输入登录账号
								</div>
							</div>
							<!--itemWarp end-->
							<div class="itemWarp">
								<div class="txtitem icoLabel">
									<label class="txtLabel pswLabel">
										<span></span>
									</label>
									<div class="item">
										<input type="password" name="" id="password" value="" placeholder="请输入登录密码" class="txtInput" />
									</div>
								</div>
								<!--txtitem end-->
								<div class="error" id ="password_err"><!--显示错误添加样式 show-->
									*密码错误
								</div>
							</div>
							<!--itemWarp end-->
							<div class="btn">
								<a href="javascript:void(0);" class="buttonA">登录</a>
								<!--<input type="button" name="" id="" value="登录" class="buttonA" />-->
							</div>
						</div>
						<!--form end-->
					</div>
				</div>
				<!--loginForm end-->
			</div>
		</div>
<script type="text/javascript">
$(document).ready(function(){
	var flag = 0;//表单提交0可提交1不可提交
	$(".buttonA").on("click",function(){
		$('#account_err,#password_err').removeClass('show');
		var account = $.trim($('#account').val());
		var password = $.trim($('#password').val());
		if(account.length==0)
		{
			$('#account_err').addClass('show').html('账号不能为空');
			return false;
		}
		if(password.length==0)
		{
			$('#password_err').addClass('show').html('密码不能为空');
			return false;
		}
		var thisdom = $(this);
		if(flag ==0)
		{
			flag = 1;
			$.ajax({
				type : "POST",
				url : '<?= \yii\helpers\Url::to("@web/account/dologin");?>',
				data : {account:account,password:password},
				dataType :'json',
				beforeSend : function(){
					//发送请求前，效果
					thisdom.addClass("noClick");
					thisdom.html('提交中...');
				},
				complete : function(){
				    //请求完成后，效果消失
					thisdom.removeClass('noClick');
					thisdom.html('登录');
					flag = 0;
				},
				success : function(data){
					//请求成功后，数据处理
					if(data.code == '111')
					{
						//成功处理..跳转
						location.href = '/record/position';
					}else{
						if(data.code==201){
							//用户不存在
							$('#account_err').addClass('show').html('账号不存在');
						}else if(data.code==202){
							//密码错误
							$('#password_err').addClass('show').html('密码错误');
						}else{
							//其他错误
							alert(data.desc);
						}
					}
				},
				error : function(){
				    //请求失败后，数据处理
				},
				statusCode: {
					404: function() {alert('page not found');},
					400: function() {alert('错误请求');},
					500: function() {alert('服务器开小差');},
					302: function() {alert('服务器302了');},
				}
			});
		}
	});
});
</script>