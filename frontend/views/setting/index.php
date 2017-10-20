<div class="app_page pT75">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
			<div class="app_title">设置</div>
			<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
		</div>
	</div>
	<div class="app_section">
		<a class="lineLink" href="javascript:void(0);">
			<div class="setList">
				<span class="icoSet setName">实名认证</span>
				<span class="setVal icoR"><?= isset($rs['customerappellation']) ? $rs['customerappellation'] : '';?></span>
			</div>
		</a>
		<a class="lineLink" href="javascript:void(0);">
			<div class="setList">
				<span class="icoSet setMob">绑定手机</span>
				<span class="setVal icoR"><?= isset($rs['handset']) ? substr_replace($rs['handset'],'****',3,4) : '';?></span>
			</div>
		</a>
		<a class="lineLink" href="javascript:void(0);">
			<div class="setList">
				<span class="icoSet setBank">绑定银行</span>
				<span class="setVal icoR">尾号<?= isset($rs['bankacco']) ? substr($rs['bankacco'],-4) : '';?></span>
			</div>
		</a>
	</div>
	<!--app_section end-->
	<div class="app_section">
		<a class="lineLink" href="<?= \yii\helpers\Url::to(['setting/set-password']);?>">
			<div class="setList">
				<span class="icoSet setPsw">交易密码</span>
				<span class="setVal icoR">&nbsp;</span>
			</div>
		</a>
	</div>
	<!--app_section end-->
	<div class="app_section">
		<a class="lineLink" href="<?= \yii\helpers\Url::to(['account/queryrisk']);?>">
			<div class="setList">
				<span class="icoSet setRisk">风险测评</span>
				<span class="setVal icoR"><?= isset($rs['riskabilityStr']) ? $rs['riskabilityStr'] : '';?></span>
			</div>
		</a>
		<a class="lineLink" href="<?= \yii\helpers\Url::to(['setting/help-center']);?>">
			<div class="setList">
				<span class="icoSet setHelp">帮助中心</span>
				<span class="setVal icoR">&nbsp;</span>
			</div>
		</a>
		<a class="lineLink" href="<?= \yii\helpers\Url::to(['setting/feedback']);?>">
			<div class="setList">
				<span class="icoSet setfeedBack">意见反馈</span>
				<span class="setVal icoR">&nbsp;</span>
			</div>
		</a>
		<a class="lineLink" href="<?= \yii\helpers\Url::to(['setting/about-us']);?>">
			<div class="setList">
				<span class="icoSet setAbout">关于我们</span>
				<span class="setVal icoR">&nbsp;</span>
			</div>
		</a>
	</div>
	<!--app_section end-->
	<div class="app_section">
		<a class="lineLink" href="tel:4003349999">
			<div class="setList">
				<span class="icoSet setTel">客户服务</span>
				<span class="setVal icoR">400-334-9999</span>
			</div>
		</a>
	</div>
	<!--app_section end-->
	<div class="button_section">
		<a class="buttonC" href="<?= \yii\helpers\Url::to(['setting/logout']);?>">安全退出</a>
	</div>
</div>
