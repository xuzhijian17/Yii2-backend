<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

// AppAsset::register($this);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=0.7,maximum-scale=0.7, minimum-scale=0.7,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?= Url::base();?>/css/admin.css" rel="stylesheet">
    <script type="text/javascript" src="<?= Url::base();?>/js/jquery.min.js"></script>
    <!-- <script type="text/javascript" src="<?= Url::base();?>/js/vue.min.js"></script> -->
</head>
<body>
	<div class="container">
	<header class="header">
		<div class="topbar fixed">
			<h1 class="logo"><a href="<?= Url::base();?>" class="logoLink">基金汇</a></h1>
			<div class="userInfo">
				<div class="drop_down_menu">
					<div class="hover">
						<?php
							foreach (Yii::$app->admin->optionMenu as $key => $value) {
								echo '<a href="'.($value['Route']?:'javascript:void(0);').'" class="top'.$key.'"><span class="ico '.$value['IcoClass'].'">'.$value['Menu'].'</span></a>';
							}
						?>
						<a href="<?= Url::to(['site/logout']);?>" class="logOut top<?= count(Yii::$app->admin->optionMenu);?>"><span class="ico icoLogOut">退出</span></a>
						<!-- <a href="javascript:void(0);" class="top0"><span class="ico icoSet">商户设置</span></a>
						<a href="javascript:void(0);" class="top1"><span class="ico icoFixPsd">修改密码</span></a>
						<a href="<?= Url::to(['site/logout']);?>" class="top2"><span class="ico icoLogOut">退出</span></a> -->
					</div>
				</div>
				<a href="javascript:void(0);" class="user"><span class="ico icoUser">您好,<?= Yii::$app->admin->instInfo['InstName'];?></span></a>
			</div>			
		</div>
		<nav>
			<ul>
				<?php foreach(Yii::$app->admin->getMenus() as $menu):?>
					<li><a href="<?= Url::to([$menu['Route']]);?>" class="<?= strstr($menu['Route'],Yii::$app->controller->id) ? 'cur' : ''; ?>"><?= $menu['Flag']?></a></li>
				<?php endforeach;?>
			</ul>
		</nav>
	</header>
	
	<div class="popUp fixPop">
		<h2 class="popH2">
			修改密码
			<a class="closePop" href="javascript:void(0);">&#10005</a>
		</h2>
		<div class="popCnt">
			<table border="0" cellspacing="0" cellpadding="0" class="popTable">
				<tr>
					<td width="80" height="40">原密码:</td>
					<td>
						<div class="item mB0">
							<div class="itemText">
								<input type="password" class="textInput" value="" id="oldPwd" name="" placeholder="请输入原密码">
							</div>
						</div>	
					</td>
				</tr>
				<tr>
					<td width="80" height="40">新密码:</td>
					<td>
						<div class="item mB0">
							<div class="itemText">
								<input type="password" class="textInput" value="" id="newPwd1" name="" placeholder="请输入新密码">
							</div>
						</div>	
					</td>
				</tr>
				<tr>
					<td width="80" height="40">确认密码:</td>
					<td>
						<div class="item mB0">
							<div class="itemText">
								<input type="password" class="textInput" value="" id="newPwd2" name="" placeholder="请再次输入新密码">
							</div>
						</div>	
					</td>
				</tr>
			</table>
		</div>
		<div class="popBut">
			<a href="javascript:void(0);" class="buttonA pwd-set">提交</a>
		</div>
	</div>
	<script>
		$(function(){
			$(".pwd-set").on("click",function(){
				var oldPwd = $('#oldPwd').val();
				var newPwd1 = $('#newPwd1').val();
				var newPwd2 = $('#newPwd2').val();
				var t = $(this);

				if (!oldPwd || !newPwd1 || !newPwd2) {
					alert('密码不能为空');
					return;
				}

				if (newPwd1 !== newPwd2) {
					alert('2次密码输入不一致');
					return;
				}

				// 请求参数
				var data = {'oldPwd':oldPwd,'newPwd1':newPwd1,'newPwd2':newPwd2};
				var url = "<?= \yii\helpers\Url::to(['setting/modify-pwd']);?>";

				// Ajax处理函数
				$.ajax({
			        type: 'GET',
			        async: true,
			        url: url,
			        data: data,
			        dataType: 'json',
			        beforeSend: function(XMLHttpRequest){
			        },
			        complete: function(XMLHttpRequest, textStatus){
			        	t.parents(".popUp").hide();
			        },
			        success: function(rs){
			        	if (rs.error == 0) {
			        		if(t.parents().hasClass("popUp")){
								alertBox("密码修改成功","我知道了","alertBtnId");
								t.parents(".popUp").hide();
							}
			        	}else{
			        		alert(rs.message);
			        		// t.parents(".popUp").hide();
			        	}
			        	// console.log(rs);
			        },
			        error:function(XMLHttpRequest, textStatus, errorThrown){
			            console.log('Ajax request error!');
			        }
			    });

				/*if($(this).parents().hasClass("popUp")){
					$(this).parents(".popUp").hide();
					alertBox("商户设置完成","我知道了","alertBtnId")
				}*/
			});
			$(".icoFixPsd").on("click",function(){
				$(".fixPop").show();
			});
		});
	</script>

	<div class="popUp adminPop">
		<h2 class="popH2">
			商户设置
			<a class="closePop" href="javascript:void(0);">&#10005</a>
		</h2>
		<div class="popCnt">
			<table border="0" cellspacing="0" cellpadding="0" class="popTable">
				<tr>
					<td width="80" height="40">商户名:</td>
					<td>
						<?php if(Yii::$app->admin->isSuperAdmin):?>
							<div class="itemSelect w160">
								<select class="asideSelect" id="insitChose" name="">
									<?php foreach(Yii::$app->admin->getInstList() as $inst):?>
										<option value="<?= $inst['Instid'];?>"><?= $inst['InstName'];?></option>
									<?php endforeach;?>
								</select>	
							</div>
						<?php else:?>
							<?= Yii::$app->admin->instInfo['InstName'];?>
						<?php endif;?>
					</td>
					<td width="80" align="right"></td>
				</tr>
				<?php if(Yii::$app->admin->isSuperAdmin):?>
					<tr>
						<td width="80" height="40">上线状态:</td>
						<td>
							<div class="itemSelect w160 status">
								<select class="asideSelect" id="insitStatus" name="">
									<option value="0" <?= Yii::$app->admin->instInfo['Status']=='0'?'selected':'';?>>已上线</option>
									<option value="-1" <?= Yii::$app->admin->instInfo['Status']=='-1'?'selected':'';?>>未上线</option>
								</select>	
							</div>
						</td>
						<td width="80" align="right"></td>
					</tr>
				<?php endif;?>
				<tr>
					<td width="80" height="40">分成比例:</td>
					<td>
						<?php if(Yii::$app->admin->isSuperAdmin):?>
							<div class="item mB0 w160">
								<div class="itemText">
									<input type="text" class="textInput" value="<?= Yii::$app->admin->instInfo['Divide'];?>" id="insitDivide" name="">
								</div>
							</div>
						<?php else:?>
							<?= Yii::$app->admin->instInfo['Divide'];?>
						<?php endif;?>
					</td>
					<td width="80" align="right"></td>
				</tr>
				<?php if(Yii::$app->admin->isSecretKeySet):?>
					<tr>
						<td width="80" height="40">商户秘钥:</td>
						<td>
							<div class="item mB0">
								<div class="itemText">
									<input type="text" class="textInput" value="<?= Yii::$app->admin->instInfo['PassWord'];?>" id="secretKey" name="secretKey" maxlength="50">
								</div>
							</div>	
						</td>
						<td width="80" align="right"></td>
					</tr>
					<tr>
						<td height="30"></td>
						<td style="color: #666666;">请输入50字以内秘钥</td>
					</tr>
				<?php endif;?>
			</table>
		</div>
		<div class="popBut">
			<a href="javascript:void(0);" class="buttonA insit-set">提交</a>
		</div>
		<script>
		$(function(){
			var instid;
			var status;
			var divide;
			var secretkey;

			var data,url;

			// 商户设置选择
			$('#insitChose').on('change', function(){
		    	instid = $(this).val();

		    	// 请求参数
				data = {'Instid':instid};
				url = "<?= \yii\helpers\Url::to(['setting/partner']);?>";

		    	// Ajax处理函数
				$.ajax({
			        type: 'GET',
			        async: true,
			        url: url,
			        data: data,
			        dataType: 'json',
			        beforeSend: function(XMLHttpRequest){
			        },
			        complete: function(XMLHttpRequest, textStatus){
			        },
			        success: function(rs){
			        	if (rs.error == 0) {

			        		var html = '';
			        		html += '<select class="asideSelect" id="insitStatus" name="">';
							html += '<option value="0" '+(parseInt(rs.Status)=='0'?'selected':'')+'>已上线</option>';
							html += '<option value="-1" '+(parseInt(rs.Status)=='-1'?'selected':'')+'>未上线</option>';
							html += '</select>';

							$(".status").html(html);
							$("#insitDivide").val(rs.Divide);
							$('#secretKey').val(rs.PassWord);
			        	}
			        	// console.log(rs);
			        },
			        error:function(XMLHttpRequest, textStatus, errorThrown){
			            console.log('Ajax request error!');
			        }
			    });
			});

			// 商户设置提交处理
			$('.insit-set').on('click', function(){
				instid = $('#insitChose').val();
				status = $('#insitStatus').val();
				divide = $('#insitDivide').val();
				secretkey = $('#secretKey').val();
				var t = $(this);

				if (secretkey.length>50) {
					alert('商户密匙不能大于50字符');
					return;
				}

				// 请求参数
				data = {'Instid':instid,'Status':status,'Divide':divide,'PassWord':secretkey};
				url = "<?= \yii\helpers\Url::to(['setting/secrent-key']);?>";

				// Ajax处理函数
				$.ajax({
			        type: 'GET',
			        async: true,
			        url: url,
			        data: data,
			        dataType: 'json',
			        beforeSend: function(XMLHttpRequest){
			        },
			        complete: function(XMLHttpRequest, textStatus){
			        	t.parents(".popUp").hide();
			        },
			        success: function(rs){
			        	if (rs.error == 0) {
			        		if(t.parents().hasClass("popUp")){
								alertBox("设置成功","我知道了","alertBtnId");
								t.parents(".popUp").hide();
							}
			        	}else{
			        		alert(rs.message);
			        		// t.parents(".popUp").hide();
			        	}
			        	// console.log(rs);
			        },
			        error:function(XMLHttpRequest, textStatus, errorThrown){
			            console.log('Ajax request error!');
			        }
			    });
			});
		});
		</script>
	</div>
	<div class="main container">
	    <?= $content ?>
	</div> <!-- /container -->
	</div>
	<footer>
		<a href="http://www.51jijinhui.com/about.html">公司介绍</a><a href="http://www.51jijinhui.com/cantact.html">联系我们</a>北京汇成基金销售有限公司版权所有©2005-2014   [京ICP备15048098号-1] 
	</footer>
<script type="text/javascript" src="<?= Url::base();?>/js/spin.min.js"></script>
<script type="text/javascript" src="<?= Url::base();?>/js/admin.js"></script>
</body>
</html>
