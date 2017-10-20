$(document).ready(function(e){
	$(".sort").on("click",function(){
		if($(this).hasClass("sortDn")){
			$(this).removeClass("sortDn").addClass("sortUp")
		}else if($(this).hasClass("sortUp")){
			$(this).removeClass("sortUp").addClass("sortDn")
		}else{
			$(this).addClass("sortUp")
		}
	});
	
	$(".file").on("change",function(){
		$(this).parent("a").siblings(".upFileName").html($(this).val());
	});
	
	$(".icoSet").on("click",function(){
		$(".adminPop,.selfPop").show();
	});
	
	$(".closePop").on("click",function(){
		$(this).parents(".popUp").hide();
		$(".mask").hide();
	});
	
	$(document).on("click",".buttonC",function(){
		if($(this).parents().hasClass("popUp")){
			$(this).parents(".popUp").remove();
		}
	});
	
	$(".p0").each(function(){
		$(this).children(".tdRow:first").css("border",0)
	})
	//$(".p0 .tdRow:first").css("border",0)
	


});



/*
 * 提示弹框
 * 使用说明：alertBox(alertCntText,alertBtnText,alertBtnId)
 * 参数说明：
 * alertCntText：提示文本；
 * alertBtnText：提示按钮文本；
 * alertBtnId：提示按钮ID
 * 
 */
function alertBox(alertCntText,alertBtnText,alertBtnId,callback){
	var resultAlertBox="";
		resultAlertBox+='<div class="popUp singlePop">'
			+'<div class="popText">'
			+alertCntText
			+'</div>'
			+'<a class="popBut buttonC" href="javascript:void(0);" id='
			+alertBtnId
			+'>'
			+alertBtnText
			+'</a></div>';
	$("body").append(resultAlertBox);

	if (typeof callback != "undefined") {
		$('#'+alertBtnId).on('click',function(){
			callback(alertBtnId);
		});
	}
}

/**
* 暂无数据页面
*/
function viewEmpty(data) {
	var html = '';

	html += '<div class="">暂无数据</div>';

	return html;
}

/**
* Ajax处理函数
*/
/*function Ajax(url, data, type) {
	var spinner = new Spinner().spin();

	data['type'] = type;	// 交易类型
	$.ajax({
        type: type || 'GET',
        async: true,
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        	$('.tableList .tableDiv').get(0).appendChild(spinner.el);
        },
        complete: function(XMLHttpRequest, textStatus){
        	$('.pageNum').val(page);
        	$('.totalPages').text(totalPages);
        	$('.wrap').show();
        	spinner.stop();
        },
        success: function(rs){
        	if (rs.error == 0) {
                if (rs.list.length > 0) {
                	var list = '';
	        		$.each(rs.list, function(i, data){
	        			list += viewList(data)
	                });
	                var tableList = viewThead()+list;
	                $('.tableList .tableDiv').html(tableList);

                	page = rs.page;
	                totalPages = rs.totalPages;
	                totalRecords = rs.totalRecords;
                }else{
                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 1;
	                
	                $('.tableList .tableDiv').html(viewEmpty());
	                $('.tableList .pages').hide();
                }
        	}else{
        		console.log(rs);
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}*/

var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();
