$(document).ready(function(e){
/*
 * li 等宽
 */
$(".equal").each(function(){
	var thisW = $(this).width();
	var liTotal = $(this).children("ul").children("li").length;
	$(this).children("ul").children("li").width(thisW/liTotal);
	//console.log(thisW+"-"+liTotal);
});

/*
 * li自排默认样式名
 */
$("ul").each(function(){
	$(this).children("li:first").addClass("first");
	$(this).children("li:last-child").addClass("last");
	$(this).children("li:odd").addClass("odd");
	$(this).children("li:even").addClass("even");
	
});
/*
 * dl自排默认样式名
 */
$(".dlBlk").each(function(){
	$(this).children("dl:first").addClass("first");
	$(this).children("dl:last-child").addClass("last");
});

/*
 * table
 */
$("table").each(function(){
	$(this).children("tbody").children("tr:odd").addClass("odd");
	$(this).children("tbody").children("tr:even").addClass("even");
});
$("tr").each(function(){
	$(this).children("td:first").addClass("first");
});

/*
 * 数据加载时特效
 */
var treq;
$("table tbody").each(function(){
	if(!$(this).hasClass("noanim")){
		$(this).children("tr").each(function(){
			treq = $(this).index();
			if($(this).hasClass("hddenFields")){
				treq=treq-1
			}else{
				
				$(this).addClass("animation animation-delay-"+treq)
			}
		});
	}
});

/*
 * input
 */
$(".txtInput").focus(function(){
	$(this).parents(".txtitem").addClass("focus");
	$(this).keyup(function(){
		var thisVal =$(this).val();
		if(thisVal!=""){
			$(this).siblings(".empty").addClass("dpyB");
		}else{
			$(this).siblings(".empty").removeClass("dpyB");
		}
	});
}).blur(function(){
	$(this).parents(".txtitem").removeClass("focus");
});

$(".seachBtn").on("click",function(){
	var seachVal = $(this).siblings(".seachInput").val();
	if(seachVal==""){
		$(this).siblings(".seachInput").focus();
	}
})


$(document).on("click",".empty",function(){
	$(this).removeClass("dpyB").siblings(".txtInput").val("").focus();
});

/*导航*/
$(".nav ul li").hover(function(){
	if($(this).hasClass("dropMenu")){
		$(this).addClass("hover");
	}
},function(){
	$(this).removeClass("hover");
});

/*
 * 选择账户
 */
$(".select").hover(function(){
	$(this).addClass("sltShow").children(".sltOption").slideDown();
},function(){
	$(this).removeClass("sltShow").children(".sltOption").hide();
});

/*
 * 排序
 */
/*$(document).on("click",".s_click",function(){
	if($(this).hasClass("sort_down")){
		$(this).removeClass("sort_down");
		$(this).addClass("sort_up");
	}else if($(this).hasClass("sort_up")){
		$(this).removeClass("sort_up");
		$(this).addClass("sort_down");
	}else{
		$(this).addClass("sort_up");
	}
	$(this).parent("th").siblings().children(".s_click").removeClass("sort_up").removeClass("sort_down");
});*/


/*
 * 选择标题
 */
$(".allType").hover(function(){
	$(this).children(".typeList").show();
},function(){
	$(this).children(".typeList").hide();
});

/*
 * select
 * drop box
 */

$(".dbSlted").on("click",function(e){
	e.stopPropagation();
	$(".options").hide()
	$(this).siblings(".options").toggle();
});

$(document).on("click",".s_select",function(e){
	e.stopPropagation();
	$(".sWarp").removeClass("s_show");
//	$(".s_opts").hide()
	$(this).siblings(".s_opts").toggle().parent(".sWarp").toggleClass("s_show");
	$(this).parents(".sItem").siblings(".sItem").children(".sWarp").children(".s_opts").hide();
	$(this).parents(".sItem").siblings(".fxTpyeWarp").children(".fxTpye").children(".sItem").children(".sWarp").children(".s_opts").hide();
	$(this).parents(".fxTpyeWarp").siblings(".sItem").children(".sWarp").children(".s_opts").hide();
});





$(document).on("click",function(){
	if($(".options").is(':visible')){
		$(".options").hide()
	}
	//
})


/*
 * radio
 */
$(".opRadio").each(function(){
	if($(this).is(":checked")){
		$(this).parent(".rdLabel").addClass("rdCur");
	}
});


/*
 * checkbox
 */
$(".ckInput").each(function(){
	if($(this).is(":checked")){
		$(this).parent(".ckLabel").addClass("ckLabeled");
	}
});
$(document).on("change",".ckInput",function(){
	if($(this).is(":checked")){
		$(this).parent(".ckLabel").addClass("ckLabeled");
		var chsub = $("input[type='checkbox'][name='orderCk']").length;
		var chedsub = $("input[type='checkbox'][name='orderCk']:checked").length;
		if(chsub==chedsub){
			$(".ckAllInput").prop("checked",true).parent(".ckLabel").addClass("ckLabeled");
		}
		//console.log(chsub+"*"+chedsub);
	}else{
		$(this).parent(".ckLabel").removeClass("ckLabeled");
		$(".ckAllInput").prop("checked",false).parent(".ckLabel").removeClass("ckLabeled");
	}
});

$(document).on("change",".ckAllInput",function(){
	if($(this).is(":checked")){
		$(".ckInput").each(function(){
			$(this).prop("checked",true).parent(".ckLabel").addClass("ckLabeled");
		});		
	}else{
		$(".ckInput").each(function(){
			$(this).prop("checked",false).parent(".ckLabel").removeClass("ckLabeled");
		});	
	}
});

/*
 *字段展开 
 */
$(document).on("click",".icoShow",function(){
	if(!$(this).hasClass("subOpen")){
		$(this).addClass("subOpen").parents("tr").addClass("open").next(".hddenFields").css("display","");
	}else{
		$(this).removeClass("subOpen").parents("tr").removeClass("open").next(".hddenFields").css("display","none");
	}
});


/*
 * tab
 */
var cntTotal;
var cntW;
var tabTagIndex;
var thisLi_xL;
$(".tabCnts").each(function(){
	cntTotal = $(this).children(".tabCnt").length;
	cntW = $(this).parent(".tabContent").width();
	$(this).children(".tabCnt").width(cntW);
	$(this).width(cntW*cntTotal);
});

$(".tabTag ul li").on("click",function(){
	tabTagIndex =$(this).index();
	thisLi_xL = $(this).position().left;
	console.log(thisLi_xL);
	$(this).addClass("cur").siblings().removeClass("cur");
	$(".tabCnts").animate({
		left:-tabTagIndex*cntW
	})
	$(this).parents(".tabTag").siblings(".curLine").animate({
		left:thisLi_xL
	},300);
});



/*
 * 默认动作
 */
//setTimeout(function(){slideDown(".newestNotice",1000)},1000);弹出的公告
//setTimeout(function(){slideDown(".importResult",500)},2000);导入成功提示



/*
 * 关闭
 */
$(document).on("click",".close,.close2",function(){
	if($(this).parents().hasClass("slideDown")){
		$(this).parents(".slideDown").slideUp();
	}
})

$(document).on("click",".popClose",function(){
	closePopUp();
	if($(".openForm").length>0){
		location.reload();
//		$(".openForm input").each(function(){
//			console.log($(this).val())
//			$(this).val("");
//		});
	}
});

});
//jq end






function slideDown(tag,timer){
	$(tag).slideDown(timer);
	if($(tag).hasClass("vanish")){
		setTimeout(function(timer){
			$(tag).remove();
		},2000+timer);
	}
}

/*
 * 弹出框
 * popUp(".popUp","请输入您的提示内容","提示标题","warn","ico")
 * 参数设置：
 * tag：（".className"）popUp最外层样式名称
 * tipTxt:("文本内容")提示内容
 * popT:("文本内容")popUp标题
 * warn：("warn")是否属于警告类型
 * ico：("ico")是否存在ico图标
 */
function popUp(tag,tipTxt,popT,warn,ico){
	if(tipTxt){
		$(tag).children(".popUpWarp").children(".popUpMain").children(".popUpCnt").children(".popTip").children(".popTipTxt").html(tipTxt);
	}
	if(popT){
		$(tag).children(".popUpWarp").children(".popUpMain").children(".popUpTop").children(".popUpT").html(popT)
	}
	if(warn){
		$(tag).children(".popUpWarp").children(".popUpMain").children(".popUpCnt").children(".popTip").addClass(warn)
	}
	if(ico){}else{
		$(tag).children(".popUpWarp").children(".popUpMain").children(".popUpCnt").children(".popTip").children(".popIco").hide();
	}
	
	$(tag).fadeIn();
	var popUpTop = ($(tag).children(".popUpWarp").children(".popUpMain").width()+10)/2;
	var popUpLeft = ($(tag).children(".popUpWarp").children(".popUpMain").height()+10)/2;
	$(tag).children(".popUpWarp").css({
		"margin-top":-popUpTop,
		"margin-left":-popUpLeft
	});
	//console.log("宽："+popUpW+"高："+popUpH);
}

/*
 * 提示信息框
 * *用法hintPop('测试错误提示')
 * **参数说明
 * hintText——提示文字信息
 */
function hintPop(hintText){
	var hintResult='';
		hintResult += '<div class="hint"><div class="hintMain">'
					+ '<div class="hinttext">'+hintText+'</div></div></div>';
	$("body").append(hintResult);
	$(".hint").fadeIn();
	var hintTagH = $(".hint").height();
	var hintTagW = $(".hint").width();
	//console.log(hintTagH);
	$(".hint").css({
		"margin-top":-hintTagH/2,
		"margin-left":-hintTagW/2
	});
	setTimeout(function(){
		$(".hint").fadeOut();
	},2000)
	setTimeout(function(){
		$(".hint").remove();
	},2500)
}

function closePopUp(tag){
	$(".popUp").hide();
}






