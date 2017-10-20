<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section box">
	<form action="#" id="theme-box">
		<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
			<tr>
				<td width="100" class="talC" align="center">主题名称</td>
				<td class="editTitle"><input type="" name="" id="Theme" value="<?= isset($themeData['Theme'])?$themeData['Theme']:'';?>" placeholder="请输入主题名称" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">主题简介</td>
				<td class="editTitle">
					<div class="textArea">
						<textarea name="Describe" id="Describe" rows="" cols="" class=""><?= isset($themeData['Describe'])?$themeData['Describe']:'';?></textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">主题图片</td>
				<td class="editTitle">
					<div class="fileInput">
						<a href="javascript:void(0);" class="buttonFile">浏览<input type="file" name="files[]" id="fileupload" value="<?= isset($themeData['Image']) && !empty($themeData['Image'])?$themeData['Image']:'';?>" class="file" /></a>
						<span class="upFileName"><?= isset($themeData['ThumbnailImage']) && !empty($themeData['ThumbnailImage'])?'<img src="'.\yii\helpers\Url::to($themeData['ThumbnailImage']).'" height="60px">':'未上传图片';?></span>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center">正文</td>
				<td class="editTD">
					<textarea name="" rows="" cols="" id="editor_id"><?= isset($themeData['Content'])?$themeData['Content']:'';?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<a href="javascript:void(0);" class="buttonA sub-theme">提交</a>
				</td>
			</tr>
		</table>
	</form>
</section>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/kindeditor/kindeditor-all-min.js"></script>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/kindeditor/lang/zh-CN.js"></script>
<script type="text/javascript">
var id;
var Theme;
var Describe;
var ThemeImage;
var Content;

$(function () {
	var url = "<?= !isset($themeData)&&empty($themeData)?\yii\helpers\Url::to(['business/add-theme']):\yii\helpers\Url::to(['business/edit-theme']);?>";
	var data = {};

    KindEditor.ready(function(K){
		window.editor=K.create("#editor_id",{
		    width : "100%", //编辑器的宽度为70%
		    height : "330px", //编辑器的高度为100px
		    filterMode : false, //不会过滤HTML代码
		    resizeMode : 0, //编辑器只能调整高度
		    resizeType : 1, //编辑器只能调整高度
		    items:[ 'undo', 'redo', 'print', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', '|', 'selectall', '|','title', 'fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold','italic', 'underline', 'strikethrough', 'removeformat', '|', 'hr', 'link', 'unlink', '|','source'],
		    cssPath:'<?= \yii\helpers\Url::base();?>/kindeditor/plugins/code/prettify.css',
		})

		// 主题图片上传
		$('#fileupload').fileupload({
	        url: '<?= \yii\helpers\Url::to(['business/upload-theme']);?>',
	        dataType: 'json',
	        done: function (e, data) {
	            $.each(data.result.files, function (index, file) {
	                $('.upFileName').text(file.name).appendTo('#files');
	                ThemeImage = file.name;
	            });
	        },
	    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

		$('#theme-box .sub-theme').on('click', function(){
			Theme = $("#Theme").val();
			Describe = $("#Describe").val();
			Content = editor.html();

			if (!Theme) {
				alert("标题不能为空")
				return;
			}

			data = {Theme:Theme,Describe:Describe,Content:Content};

			id = $_GET['id'];
			if (id) {
				data.id = id;
			}

			if (ThemeImage) {
				data.Image = ThemeImage;
			}
			
			Ajax(url,data);
		});
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data) {
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
        		window.location.href = "<?= \yii\helpers\Url::to(['business/theme']);?>";
        	}else{
        		alert(rs.message);
        	}
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}
</script>
