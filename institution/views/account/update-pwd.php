<?php
use yii\helpers\Url;
use institution\components\LeftBarWidget;

$this->title = '设置密码';
?>
<!--登陆后结束-->
<div class="main">
	<div class="Side">
		<?php echo LeftBarWidget::widget();?>
		<!--leftBar end-->
		<div class="rightBar">
			<div class="content">
				<div class="newestNotice slideDown">
					<span class="close">X</span>
					<span class="date">2016-02-24</span>
					<span class="horn">最新公告：春节期间基金申购赎回、小金库快赎业务公告</span> 
				</div>
				<!--newestNotice end-->
				<div class="pT10">
					<div class="titBlk borN">
						<div class="titlist">
							<ul>
								<li><span class="tabOption taL">设置密码</span></li>
							</ul>
						</div>
					</div>
					<!--titBlk end-->
					<div class="titBor"></div>
				</div>
				<!--end pT20-->
				<div class="borMain pB40 mB40">
					<div class="cntNotice">
						<div class="tableUser">
							<table border="0" cellspacing="0" cellpadding="0" class="equal">
								<tbody class="noanim">
									<tr>
										<td align="left" width="33%">当前登录账户：<?= isset($userData['userName'])?$userData['userName']:'';?></td>
										<td align="center" width="33%">当前登录角色：<?= '交易员';?></td>
										<td align="right">当前登录姓名：<?= isset($userData['orgName'])?$userData['orgName']:'';?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!--cntNotice end-->
					<div class="openForm">
						<form action="" method="post">
						<div class="itemLine">
							<label class="fL">原密码</label>
							<div class="itemWarp fL ">
								<div class="txtitem">
									<div class="item">
										<input type="text" name="" id="oldPwd" value="" class="txtInput" placeholder="请输入原密码" maxlength="10" />
									</div>
								</div>
							</div>
						</div>
						<!--itemLine end-->
						<div class="itemLine">
							<label class="fL">设置新密码</label>
							<div class="itemWarp fL ">
								<div class="txtitem">
									<div class="item">
										<input type="password" name="" id="pwd1" value="" class="txtInput" placeholder="建议使用6位数字密码" maxlength="6" />
									</div>
								</div>
							</div>
						</div>
						<!--itemLine end-->
						<div class="itemLine">
							<label class="fL">再次输入新密码</label>
							<div class="itemWarp fL ">
								<div class="txtitem">
									<div class="item">
										<input type="password" name="" id="pwd2" value="" class="txtInput" placeholder="请再次输入新密码" maxlength="6" />
									</div>
								</div>
							</div>
						</div>
						<!--itemLine end-->
						
						<div class="btn openBtn">
							<a href="javascript:void(0);" class="buttonB pswSubmit">保存</a>
						</div>
						</form>
					</div>
					<!--openForm end-->
				</div>
				<!--borMain end-->
			</div>
			<!--content end-->
		</div>
		<!--rightBar end-->
	</div>
	<!--Side end-->
</div>
<!--main end-->
<script type="text/javascript">
$(document).ready(function(e){
	var url = '<?php Url::to(["account/update-pwd"]);?>';
	var data = {};

	$(".pswSubmit").on("click",function(){
		var oldPwd = $("#oldPwd").val();
		var pwd1 = $("#pwd1").val();
		var pwd2 = $("#pwd2").val();

		if (!oldPwd || !pwd1 || !pwd2) {
			hintPop('密码不能为空');
			return;
		}else if (pwd1 !== pwd2) {
			hintPop('2次密码输入不一致');
			return;
		}else if (oldPwd === pwd1) {
			hintPop('新旧密码不能一样');
			return;
		}else{
			data = {password:pwd1,oldPassword:oldPwd};
		}
		
		$.ajax({
	        type: 'POST',
	        async: true,
	        url: url,
	        data: data,
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.code == '111') {
	                hintPop('密码设置成功');
	        	}else{
	        		hintPop(rs.desc);
	        	}
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});
});
</script>


























