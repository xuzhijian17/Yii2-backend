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
						<form action="<?= \yii\helpers\Url::to(['account/open2submit']);?>" method="post" id="openform" enctype="multipart/form-data" target="uploadframe">
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

				<div class="pT10">
					<div class="titBlk">
						<div class="titlist tabTag">
							<ul>
								<li class="cur"><span class="tabOption">开户记录</span></li>
								<li><span class="tabOption">机构信息</span></li>
							</ul>
						</div>
						<i class="curLine"></i>
					</div>
					<!--titBlk end-->
				</div>
				<!--end pT10-->
				<div class="newAccount tableWarp tabContent">
					<div class="tabCnts">
						<div class="tabCnt">
							<table border="0" cellspacing="1" cellpadding="0" class="table">
								<thead>
								<tr>
									<th align="left" style="padding-left: 20px;">账户全称</th>
									<th>开户状态</th>
									<th>开户申请</th>
									<th>确认日期</th>
									<th>凭证</th>
									<th>开户文件</th>
								</tr>
								</thead>
								<tbody class="noanim">
								<?php foreach($openlist as $key=>$value){?>
									<tr>
										<td align="left" style="padding-left: 20px;"><?php echo isset($value['extName'])?$value['extName']:'';?></td>
										<td align="center"><?php echo isset($value['status'])?$value['status']:'';?></td>
										<td align="center"><?php echo isset($value['cdate'])?$value['cdate']:'';?></td>
										<td align="center"><?php echo isset($value['confirmDate'])?$value['confirmDate']:'';?></td>
										<td align="center">
											<?php if($value['status'] == '开户成功'){?>
												<a href="javascript:;" onclick="viewAttach('<?php echo $value['appFile'];?>');" class="linkT1 pLR5">受理单</a>
												<a href="javascript:;" onclick="viewAttach('<?php echo $value['confirmFile'];?>');" class="linkT1 pLR5">确认单</a>
											<?php }?>
										</td>
										<td align="center">
											<div class="linkT1 linkSub">
												<span>附件<i class="ico icoBlue"></i></span>
												<div class="linkSubMain">
													<a href="javascript:;" onclick="viewAttach('<?php echo $value['operators'];?>');" class="linkT1">经办人</a>
													<a href="javascript:;" onclick="viewAttach('<?php echo $value['bankAccount'];?>');" class="linkT1">银行账户</a>
													<a href="javascript:;" onclick="viewAttach('<?php echo $value['productApprovalDoc'];?>');" class="linkT1">产品批复</a>
												</div>
											</div>
										</td>
									</tr>
								<?php }?>
								</tbody>
							</table>
							<?php if(empty($openlist)) {?>
							<div class="nodata">
								<span>暂无数据</span>
							</div>
							<?php }?>
						</div>
						<div class="tabCnt">
							<div class="jgInfo">
								<dl class="fixed">
									<dt>企业名称：</dt>
									<dd>
										<span><?php echo isset($openinfo['name']) ? $openinfo['name']: '--';?></span>
										<a href="javascript:;" onclick="viewAttach('<?php echo $openinfo['businessLicense'];?>');" class="ico icoEdit"></a>
									</dd>
								</dl>
								<dl class="fixed">
									<dt>证件类型：</dt>
									<dd><span><?php echo isset($openinfo['certificateType']) ? $openinfo['certificateType']: '--';?></span></dd>
								</dl>
								<dl class="fixed">
									<dt>证件号码：</dt>
									<dd><span><?php echo isset($openinfo['businessLicenseCode']) ? $openinfo['businessLicenseCode']: '--';?></span></dd>
								</dl>
								<dl class="fixed">
									<dt>法人信息：</dt>
									<dd>
										<span><?php echo isset($openinfo['legalPersonRealName']) ? $openinfo['legalPersonRealName']: '--';?></span>
										<a href="javascript:;" onclick="viewAttach('<?php echo $openinfo['legalPerson'];?>');" class="ico icoEdit"></a>
									</dd>
								</dl>
								<!--<dl class="fixed">
                                    <dd><span>备注：如果企业注册信息发生变更，请<a href="javascript:void(0);" class="linkT1">重新上传</a></span></dd>
                                </dl>-->
							</div>
						</div>
					</div>
				</div>

				<!--tableWarp end-->
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
<script type="text/javascript">
	function uploadResult(state, msg){
		if (state != 1) {
			hintPop(msg);
			return false;
		}
		popUp(".popUpImport","您的开户申请已提交，正在审核，请稍后查看开户记录。","提示","","ico");
		return true;
	}

	//查看附件
	function viewAttach(attach)
	{
		if (attach == undefined ||attach == null || attach == "") {
			hintPop('不存在附件');
			return false;
		}
		window.open("<?= \yii\helpers\Url::to(['account/attach']);?>?id="+attach);
	}

	$(document).ready(function(e){
		$(".openSubmit").on("click",function(){
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
			var html = '<span style="color:#2c97fb;">'+str+'</span>';
			$(this).siblings('.upFileBtn').html(html)
		})
	});
</script>