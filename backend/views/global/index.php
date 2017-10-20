<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section box">
	<form action="#" id="news-box">
		<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
			<tr>
				<td width="100" class="talC" align="center">网站标题</td>
				<td class="editTitle"><input type="" name="title" id="title" value="<?= isset($queryconfig['Title'])?$queryconfig['Title']:'';?>" placeholder="请输入标题" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">网站关键字</td>
				<td class="editTitle"><input type="" name="keywords" id="keywords" value="<?= isset($queryconfig['Keywords'])?$queryconfig['Keywords']:'';?>" placeholder="请输入关键字" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">网站描述</td>
				<td class="editTitle"><input type="" name="descriptions" id="descriptions" value="<?= isset($queryconfig['Descriptions'])?$queryconfig['Descriptions']:'';?>" placeholder="请输入描述" class="inputTitle" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<a href="javascript:void(0);" class="buttonA news-btn">提交</a>
				</td>
			</tr>
		</table>
	</form>
</section>
<script type="text/javascript">
var title;
var keywords;
var descriptions;

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['global/index']);?>";
	var data = {};
	$('#news-box .news-btn').on('click', function(){
		title = $("#title").val();
		keywords = $("#keywords").val();
		descriptions = $("#descriptions").val();

		if (!title) {
			alert("标题不能为空");
			return;
		}
		data = {title:title,keywords:keywords,descriptions:descriptions};
		var spinner = new Spinner().spin();
		$.ajax({
			type: 'POST',
			async: true,
			url: url,
			data: data,
			dataType: 'json',
			beforeSend: function(XMLHttpRequest){
				$('.box').get(0).appendChild(spinner.el);
			},
			complete: function(XMLHttpRequest, textStatus){
				spinner.stop();
			},
			success: function(rs){
				if (rs.error == 0) {
					window.location.href = url;
				}else{
					alert(rs.message);
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
