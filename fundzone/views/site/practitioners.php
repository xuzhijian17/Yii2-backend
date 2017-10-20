<?php
use yii\helpers\Url;
?>
	<section class="banner cantactBanner"><!--banner--></section>
	<section class="main maxW fixed">
		<div class="main_left">
			<ul class="subMenu">
	        	<li><a href="<?= Url::to(['site/about']);?>" class="subLink">公司介绍</a></li>
				<li><a href="<?= Url::to(['site/culture']);?>" class="subLink">企业文化</a></li>
				<li><a href="<?= Url::to(['site/practitioners']);?>" class="subLink cur">从业信息</a></li>
	            <li><a href="<?= Url::to(['site/aptitude']);?>" class="subLink">资质证明</a></li>
				<li><a href="<?= Url::to(['site/contact']);?>" class="subLink">联系方式 </a></li>
				<li><a href="<?= Url::to(['site/recruit']);?>" class="subLink">诚聘英才</a></li>
      		</ul>
		</div>
		<div class="main_right">
			<h2 class="cntT">基金从业信息</h2>
			<div class="newsCnt about">

<!--<table width="100%" border="0" cellspacing="1" cellpadding="0" class="table2">
  <tr>
    <td class="th">机构名称</td>
    <td colspan="3">北京汇成基金销售有限公司</td>
    <td align="center" class="th">更新时间</td>
    <td>2016/12</td>
  </tr>
  <tr>
    <td class="th">联系人</td>
    <td colspan="3">李瑞真</td>
    <td align="center" class="th">联系电话</td>
    <td>010-62680527</td>
  </tr>
  <tr>
    <td class="th">基金销售网点</td>
    <td align="center" class="th">地　址</td>
    <td align="center" class="th">客服电话</td>
    <td align="center" class="th">基金销售人员</td>
    <td align="center" class="th">资格类型</td>
    <td align="center" class="th">资格证书编号</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>陈博</td>
    <td>基金从业资格</td>
    <td>基金法律法规、职业道德与业务规范<br>
      201509111635664011<br>
      证券投资基金基础知识<br>
      201509111537213011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>丁向坤</td>
    <td>基金从业资格</td>
    <td>证券市场基础知识<br>
      20141011556605011<br>
      证券投资基金<br>
      20141011626627011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>纪美玲</td>
    <td>基金从业资格</td>
    <td>证券市场基础知识<br>
      2012031100451601<br>
      证券投资基金<br>
      2012031100451505</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>李瑞真</td>
    <td>基金从业资格</td>
    <td>证券市场基础知识<br>
      20071100729101<br>
      证券投资基金<br>
      20081101429605</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>宋静</td>
    <td>基金从业资格</td>
    <td>基金法律法规、职业道德与业务规范<br>
      201509111624842011<br>
      证券投资基金基础知识<br>
      201509111508689011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>王伟刚</td>
    <td>基金从业资格</td>
    <td>证券市场基础知识<br>
      20161100072701<br>
      证券投资基金<br>
      201504111828210011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>熊小满</td>
    <td>基金从业资格</td>
    <td>基金法律法规、职业道德与业务规范<br>
      20150911111706907011<br>
      证券投资基金基础知识<br>
      201509111501101011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>袁俊</td>
    <td>基金从业资格</td>
    <td>基金法律法规、职业道德与业务规范<br>
      201509111431195011<br>
      证券投资基金基础知识<br>
      201509111386090011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>张涛</td>
    <td>基金从业资格</td>
    <td>基金法律法规、职业道德与业务规范<br>
      201509111636539011<br>
      证券投资基金基础知识<br>
      201509111538000011</td>
  </tr>
  <tr>
    <td>基金销售中心</td>
    <td>北京市海淀区中关村e世界A座1108室</td>
    <td>010-62680527</td>
    <td>周健</td>
    <td>基金从业资格</td>
    <td>基金法律法规、职业道德与业务规范<br>
      201509111603566011<br>
      证券投资基金基础知识<br>
      201509111424822011</td>
  </tr>
</table>-->				
<style type="text/css">
table { background:#eee;}
table tr td { background:#FFF; text-align:center; height:30px;}
</style>				
<table width="100%" cellpadding="0" cellspacing="1">
  <tr>
    <td colspan="3"><strong>北京汇成基金销售有限公司基金从业人员公示</strong></td>
  </tr>
  <tr>
    <td>姓名</td>
    <td>证书编号</td>
    <td>资格</td>
  </tr>
  <tr>
    <td>宋静</td>
    <td>F2590000000001</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>纪美玲</td>
    <td>F2590000000002</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>王伟刚</td>
    <td>F2590000000003</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>周健</td>
    <td>F2590000000004</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>王立明</td>
    <td>F2590000000005</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>丁向坤</td>
    <td>F2590000000006</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>张涛</td>
    <td>F2590000000007</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>熊小满</td>
    <td>F2590000000008</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>袁俊</td>
    <td>F2590000000009</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>陈博</td>
    <td>F2590000000010</td>
    <td>基金从业资格</td>
  </tr>
  <tr>
    <td>李瑞真</td>
    <td>F2590000000011</td>
    <td>基金从业资格</td>
  </tr>
</table>
			</div>
		</div>
	</section>

