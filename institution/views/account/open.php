<?php
use yii\helpers\Url;
use institution\components\LeftBarWidget;
$this->title = '汇成基金-机构开户';
?>
<!--登陆后结束-->
<div class="main">
	<div class="Side">
		<?php echo LeftBarWidget::widget();?>
		<!--leftBar end-->
		<div class="rightBar">
			<div class="content">
				<!--
				<div class="newestNotice slideDown">
					<span class="close">X</span>
					<span class="date">2016-02-24</span>
					<span class="horn">最新公告：春节期间基金申购赎回、小金库快赎业务公告</span>
				</div>
				-->
				<!--newestNotice end-->
				<div class="pT10">
					<div class="titBlk borN">
						<div class="titlist">
							<ul>
								<li><span class="tabOption taL">机构开户</span></li>
							</ul>
						</div>
					</div>
					<!--titBlk end-->
					<div class="titBor"></div>
				</div>
				<!--end pT20-->
				<div class="borMain pB40 mB40">
					<div class="cntNotice">
						1、*号为必填项，如果是产品开户，需上传产品批复文件；<br />
						2、请将证件原件复印，然后加盖单位公章后上传，上传格式支持PDF文本，也支持图片JPG/PEG/BMP格式；<br />
						3、温馨提示，您已成功开立账户，新增账户时只需如下信息；
					</div>
					<div class="openForm">
						<form action="<?= \yii\helpers\Url::to(['account/opensubmit']);?>" method="post" id="openform" enctype="multipart/form-data" target="uploadframe">
							<div class="itemLine sltType mB40">
								<label class="fL">企业证件类型</label>
								<div class="itemWarp fL ">
									<div class="item radioInput">
										<label class="rdLabel">
											<input type="radio" name="opType" value="1" class="opRadio" checked="checked" />
											三证一码
										</label>
										<label class="rdLabel">
											<input type="radio" name="opType" value="2" class="opRadio" />
											一证三码
										</label>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL"><i>*</i>企业营业执照</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="businessLicense" type="file" value="" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine oneinthree">
								<label class="fL"><i>*</i>组织机构代码证</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="organizationCode" type="file" value="" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine oneinthree">
								<label class="fL"><i>*</i>税务登记证</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="taxRegistration" type="file" value="" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL"><i>*</i>法人身份证</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="legalPerson" type="file" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL"><i>*</i>经办人身份证</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="operator" type="file" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL"><i>*</i>银行账户文件</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="bankAccount" type="file" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine mB40">
								<label class="fL">产品批复文件</label>
								<div class="itemWarp fL ">
									<div class="item fileItem">
										<input name="productApprovalDoc" type="file" class="fileInput">
										<div class="upFileBtn">未上传文件</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL"><i>*</i>账户全称</label>
								<div class="itemWarp fL ">
									<div class="txtitem">
										<div class="item">
											<input type="text" name="extName" id="" value="" class="txtInput" placeholder="请填写产品/机构全称" />
										</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL"><i>*</i>经办人电话</label>
								<div class="itemWarp fL ">
									<div class="txtitem">
										<div class="item">
											<input type="text" name="operatorPhone" id="" value="" class="txtInput" placeholder="办公室座机号码或手机" />
										</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="itemLine">
								<label class="fL">产品代码</label>
								<div class="itemWarp fL ">
									<div class="txtitem">
										<div class="item">
											<input type="text" name="productcode" id="" value="" class="txtInput" placeholder="如有O3系统请填写对应产品代码" />
										</div>
									</div>
								</div>
							</div>
							<!--itemLine end-->
							<div class="btn openBtn">
								<input name="reset" type="reset" value="重置" id="reset" style="display: none;">
								<a href="javascript:void(0);" class="buttonB openSubmit">申请开户</a>
							</div>
						</form>
					</div>
					<!--openForm end-->
				</div>
				<!--end pT10-->
			</div>
			<!--content end-->
		</div>
		<!--rightBar end-->
	</div>
	<!--Side end-->
</div>
<!--main end-->
<!--导入popUp 开始-->
<div class="popUp popUpImport">
	<div class="popUpWarp">
		<div class="popUpMain">
			<div class="popUpTop">
				<span class="popUpT">提示</span>
				<a href="javascript:void(0);" class="popClose">X</a>
			</div>
			<div class="popUpCnt">
				<div class="popTip">
					<span class="popIco"><img src="../images/ico_Suss.png"/></span>
					<span class="popTipTxt" style="padding: 30px 0 40px;">汇成基金温馨提醒您！</span>
				</div>
			</div>
		</div>
	</div>
</div>
<!--导入popUp 结束-->
<iframe src=""  width="0" height="0" style="display:none;" name="uploadframe"></iframe>
<script>
	function uploadResult(state, msg){
		if (state != 1) {
			hintPop(msg);
			return false;
		}
		popUp(".popUpImport","您的开户申请已提交，正在审核，请稍后查看开户记录。","提示","","ico");
		return true;
	}
	$(document).on("change",".opRadio",function(){
		$(this).parent(".rdLabel").addClass("rdCur").siblings().removeClass("rdCur");
		if($(this).val() == 2) {
			$(".oneinthree").hide();
		} else {
			$(".oneinthree").show();
		}
	});

	$(function(){
		$(".openSubmit").on("click",function(){
			var str=$("input[name=businessLicense]").val();
			if (str == null || str == undefined || str == "") {
				hintPop('企业营业执照请上传');
				$(this).val("");
				return false;
			}
			if ($("input[name=opType]:checked").val() == 1) {
				var str=$("input[name=organizationCode]").val();
				if (str == null || str == undefined || str == "") {
					hintPop('组织机构代码证请上传');
					$(this).val("");
					return false;
				}
				var str=$("input[name=taxRegistration]").val();
				if (str == null || str == undefined || str == "") {
					hintPop('税备登记证请上传');
					$(this).val("");
					return false;
				}
			}

			var str=$("input[name=legalPerson]").val();
			if (str == null || str == undefined || str == "") {
				hintPop('法人身份证请上传');
				$(this).val("");
				return false;
			}
			var str=$("input[name=operator]").val();
			if (str == null || str == undefined || str == "") {
				hintPop('经办人身份证请上传');
				$(this).val("");
				return false;
			}
			var str=$("input[name=bankAccount]").val();
			if (str == null || str == undefined || str == "") {
				hintPop('银行账户文件请上传');
				$(this).val("");
				return false;
			}

			var str=$("input[name=extName]").val();
			if (str == null || str == undefined || str == "") {
				hintPop('账户全称不能为空');
				$(this).val("");
				return false;
			}
			var str=$("input[name=operatorPhone]").val();
			if (str == null || str == undefined || str == "") {
				hintPop('经办人电话不能为空');
				$(this).val("");
				return false;
			}

			$("#openform").submit();
		});
		$('.fileInput').change(function(){
			var str=$(this).val();
			if (str == null || str == undefined) {
				hintPop('文件不存在');
				$(this).val("");
				return false;
			}
			var suffix = str.substr(str.lastIndexOf(".")+1, str.length);
			if (suffix == "jpg" || suffix == "jpeg" || suffix == "png" || suffix == "bmp" || suffix == "pdf" ) {
				var html = '<span style="color:#2c97fb;">'+str+'</span>';
				$(this).siblings('.upFileBtn').html(html)
			} else {
				hintPop('文件格式不正确');
				$(this).val("");
				return false;
			}

		})

	})
</script>