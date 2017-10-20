<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
		<tr>
			<td width="250" class="talC" align="center">汇款凭证</td>
			<td class="editTitle">
				<div class="upLoad">
					<!-- <input type="file" name="files[]" id="fileupload" value="" /> -->
					<a href="javascript:void(0);" class="buttonFile">浏览<input type="file" name="files[]" id="fileupload" value="" class="file" /></a>
					<span class="upFileName"><?= isset($remitImageData['Pic'])?basename($remitImageData['Pic']):'未上传图片';?></span>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<a href="javascript:void(0);" class="buttonA sub-remit">提交</a>
			</td>
		</tr>
	</table>
</section>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/js/jquery.fileupload.js"></script>
<script type="text/javascript">
var id;
var Theme;
var Describe;
var RemitImage;
var Content;

$(function () {
	var url = "<?= \yii\helpers\Url::to(['business/upload-remit']);?>";
	var data = {};

    $('#fileupload').fileupload({
        url: '<?= \yii\helpers\Url::to(['business/upload-remit-image']);?>',
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('.upFileName').text(file.name).appendTo('#files');
                RemitImage = file.url;
            });
        },
    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');

	$('.sub-remit').on('click', function(){
		id = $_GET['id'];
		if (id) {
			data.id = id;
		}

		if (RemitImage) {
			data.pic = RemitImage;
		}
		
		Ajax(url,data);
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data) {

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
        	if (rs.error == 0) {
        		// window.location.reload(true);
        		window.location.href = "<?= \yii\helpers\Url::to(['business/trade-remit']);?>";
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