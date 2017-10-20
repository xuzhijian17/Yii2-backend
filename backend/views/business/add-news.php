<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section box">
	<form action="#" id="news-box">
		<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
			<tr>
				<td width="100" class="talC" align="center">标题</td>
				<td class="editTitle"><input type="" name="title" id="title" value="<?= isset($newsData['Title'])?$newsData['Title']:'';?>" placeholder="请输入标题" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">关键字</td>
				<td class="editTitle"><input type="" name="keywords" id="keywords" value="<?= isset($newsData['Keywords'])?$newsData['Keywords']:'';?>" placeholder="请输入关键字" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">描述</td>
				<td class="editTitle"><input type="" name="descriptions" id="descriptions" value="<?= isset($newsData['Descriptions'])?$newsData['Descriptions']:'';?>" placeholder="请输入描述" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="100" class="talC" align="center">链接</td>
				<td class="editTitle"><input type="" name="link" id="link" value="<?= isset($newsData['Link'])?$newsData['Link']:'';?>" placeholder="标题链接地址" class="inputTitle" /></td>
			</tr>
			<tr>
				<td align="center">正文</td>
				<td class="editTD">
					<textarea name="content" rows="" cols="" id="editor_id"><?= isset($newsData['Content'])?$newsData['Content']:'';?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<a href="javascript:void(0);" class="buttonA news-btn">提交</a>
				</td>
			</tr>
		</table>
	</form>
</section>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/kindeditor/kindeditor-all-min.js"></script>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/kindeditor/lang/zh-CN.js"></script>
<script type="text/javascript">
var cid;
var id;
var title;
var keywords;
var descriptions;
var link;
var content;
var excerpt;
var feature_image;

$(document).ready(function(){
	var url = "<?= !isset($newsData)&&empty($newsData)?\yii\helpers\Url::to(['business/add-news']):\yii\helpers\Url::to(['business/edit-news']);?>";
	var data = {};

	KindEditor.ready(function(K){
		window.editor=K.create("#editor_id",{
		    width : "100%", //编辑器的宽度为70%
		    height : "330px", //编辑器的高度为100px
		    filterMode : false, //不会过滤HTML代码
		    resizeMode : 0, //编辑器只能调整高度
		    resizeType : 1, //编辑器只能调整高度
		    urlType : "domain",	// relative为相对路径，absolute为绝对路径，domain为带域名的绝对路径
		    cssPath:'<?= \yii\helpers\Url::base();?>/kindeditor/plugins/code/prettify.css',
		    items:['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'cut', 'copy', 'paste',
		        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
		        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
		        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
		        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
		        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'table', 'hr', 'link', 'unlink', '|', 'about'
        	],
		})

		$('#news-box .news-btn').on('click', function(){
			title = $("#title").val();
			link = $("#link").val();
			keywords = $("#keywords").val();
			descriptions = $("#descriptions").val();
			content = editor.html();
			cid = $_GET['cid'];
			
			if (!title) {
				alert("标题不能为空");
				return;
			}

			if (!cid) {
				alert("缺少基金分类cid");
				return;
			}

			data = {cid:cid,title:title,keywords:keywords,descriptions:descriptions,link:link,content:content};

			id = $_GET['id'];
	        if (id) {
	        	data.id = id;
	        	// data.push({'name':'id','value':id});
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
        		window.location.href = "<?= \yii\helpers\Url::to(['business/news','cid'=>$cid]);?>";
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
