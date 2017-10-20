// JavaScript Document
$(document).ready(function(e){	

var winH = $(window).height();
//全局变量
var startTx;  //开始X坐标
var startTy;  //开始Y坐标
var endTx;    //结束X坐标
var endTy;    //结束Y坐标
var moveTx;   //滑动X轴当前值
var moveTy;   //滑动Y轴当前值
var moveRunY;  //滑动Y轴距离
var moveTyreset;   //新滑动Y轴当前值
var moveTyresetRun;  //新滑动Y轴距离
var newT;     //新的Top值
var upH;     //加载高度
var cntWH;    //滑动范围高度
var cntNum;    //滑动范围高度
var cntL;    //滑动内容Left 值
var curL;    //滑动中Left值
var topMax;    //可滑动最大值
var sclT;      //滚动条Top值
var bodH;
var otherH;

//app tab Nav
var curLi;
$(".app_tab_Type").each(function(){
	var thisTab=$(this).index();
	var tabNum=$(this).children(".appTabItem").length;
	var tabW = $(this).width()/$(this).children(".appTabItem").length;
	$(this).children(".appTabItem").width(tabW);
	$(this).children(".curLine").width(tabW)
	$(this).children(".appTabItem").on("click",function(e){
		var thisLi = $(this).index();
		if(!$(this).hasClass("curItem")){
			subMenuOut(-145);
			$(".subNav").removeClass("subShow")
			curLi = $(this).siblings(".curItem").index();
			if($(this).hasClass("subNav") && !$(this).hasClass("subShow")){
				$(this).addClass("subShow");
				$(this).addClass("curItem").siblings().removeClass("curItem");
				subMenuIn(-145);
				$(this).siblings(".curLine").animate({
					left:tabW*thisLi
				},200);
			}else{
				$(this).addClass("curItem").siblings().removeClass("curItem");
				$(this).siblings(".curLine").animate({
					left:tabW*thisLi
				},200);
			}
			if($(this).parent().hasClass("tabSibling")){
				var $curCnt = $(this).parent(".tabSibling").siblings(".typeContentWarp").children(".curCnt");
				var $thisCnt = $(this).parent(".tabSibling").siblings(".typeContentWarp").children(".typeContent").eq(thisLi);
				$(document).scrollTop(0);
				$(".tabSibling").removeClass("output_app_TopFix")
				if(thisLi<$curCnt.index()){
					$curCnt.animate({
						left:640
					},500,function(){
						$(this).hide();
					});
					$thisCnt.css("display","block").animate({
						left:0
					},500,function(){
						$thisCnt.siblings().slice(thisLi,tabNum).css("left",640);
					});
				}else{
					$curCnt.animate({
						left:-640
					},500,function(){
						$(this).hide();
					});
					$thisCnt.css("display","block").animate({
						left:0
					},500,function(){
						$thisCnt.siblings().slice(0,thisLi).css("left",-640);
					});
				}
				$thisCnt.addClass("curCnt").siblings().removeClass("curCnt");
				
			}
		}else if($(this).hasClass("subSelect") && $(this).hasClass("subShow")){
			$(this).removeClass("subShow");
//			$(this).removeClass("subSelect");
			subMenuOut(-145);
		}else if($(this).hasClass("subSelect")){
			$(this).addClass("subShow");
//			$(this).removeClass("subSelect");
			subMenuIn(-145);
		}else{
			if($(this).hasClass("subNav") && $(this).hasClass("subShow")){
				$(this).removeClass("subShow");
				$(this).removeClass("curItem").siblings().eq(curLi).addClass("curItem");
				console.log(curLi)
				$(this).siblings(".curLine").animate({
					left:tabW*curLi
				},200);
				subMenuOut(-145);
			}
		}
	});
});
$(".subNavlist ul li").on("click",function(){
	$(".submenuTxt").html($(this).html());
	$(".subNav").addClass("subSelect").removeClass("subShow");
	subMenuOut(-145);
});

//同级tab内容

//交易记录
$(document).on("click",".recordAll",function(){
	$(".recordList .record").show();
});
$(document).on("click",".recordGo",function(){
	$(".recordList .record").hide();
	$(".recordList .recordGoing").show();
});
$(document).on("click",".record_Buy_Tag",function(){
	$(".recordList .record").hide();
	$(".recordList .record_Buy").show();
});
$(document).on("click",".record_Sell_Tag",function(){
	$(".recordList .record").hide();
	$(".recordList .record_Sell").show();
});
$(document).on("click",".record_DT_Tag",function(){
	$(".recordList .record").hide();
	$(".recordList .record_DT").show();
});
$(document).on("click",".record_FH",function(){
	$(".recordList .record").hide();
	$(".recordList .record_FH").show();
});
$(document).on("click",".record_Other_Tag",function(){
	$(".recordList .record").hide();
	$(".recordList .record_Other").show();
});

//app tab Nav
$(".app_section .app_tab_nav").each(function(){
	var thisTab=$(this).index();
	//console.log(thisTab);
	var tabW = $(this).width()/$(this).children(".appTabItem").length;
	$(this).children(".appTabItem").width(tabW);
	$(this).children(".curLine").width(tabW)
	var tabCw = $(".drop_content").width();
	var tabCNum = $(this).siblings(".app_tab_content").children(".app_tab_content_column").length;
	$(this).siblings(".app_tab_content").width(tabCw*tabCNum);
	
	$(this).children(".appTabItem").on("click",function(e){
		var ispages =  $(this).parent(".app_tab_nav").siblings(".app_tab_content").width();
		var thisLi = $(this).index();
		var tabCW = $(this).parent(".app_tab_nav").siblings(".app_tab_content").children(".app_tab_content_column").width();
		if(!$(this).hasClass("curItem")){
			if($(this).parents().hasClass("app_top_fid")){
				$(document).scrollTop(0);
			}
			$(this).addClass("curItem").siblings().removeClass("curItem");
			$(this).siblings(".curLine").animate({
				left:tabW*thisLi
			},200);
			if($(this).hasClass("chartTab")){
				$(".chartCntTab").children("ul").children("li").eq(0).children("a").addClass("cur").parent().siblings().children().removeClass("cur");
				$(".chart").children(".chartItem").toggle();
			}
			if(ispages>640){
				$(this).parent(".app_tab_nav").siblings(".app_tab_content").animate({
						left:-thisLi*tabCW
				},200).children(".app_tab_content_column").eq(thisLi).addClass("tab_column_cur").siblings().removeClass("tab_column_cur");
			}else{
				//console.log("就这么宽");
			}
		}
	});
});

//基金Tab
$(".navTouch .app_tab_nav").each(function(){
	var thisTab=$(this).index();
	var tabW = $(this).width()/$(this).children(".appTabItem").length;
	$(this).children(".appTabItem").width(tabW);
	$(this).children(".curLine").width(tabW)
	var tabCw = $(".drop_content").width();
	var tabCNum = $(".app_tab_content").children(".app_tab_content_column").length;
	$(".app_tab_content").width(tabCw*tabCNum);
	
	$(this).children(".appTabItem").on("click",function(e){
		var ispages =  $(".app_tab_content").width();
		var thisLi = $(this).index();
		var tabCW = $(".app_tab_content").children(".app_tab_content_column").width();
		if(!$(this).hasClass("noclick")){
			if(!$(this).hasClass("curItem")){
				if($(this).parents().hasClass("app_top_fid")){
					$(document).scrollTop(0);
				}
				$(this).addClass("curItem").siblings().removeClass("curItem");
				$(".curLine").animate({
					left:tabW*thisLi
				},200);
				if(ispages>640){
					$(".app_tab_content").animate({
							left:-thisLi*tabCW
					},200).children(".app_tab_content_column").eq(thisLi).addClass("tab_column_cur").siblings().removeClass("tab_column_cur");
				}else{
					//console.log("就这么宽");
				}
			}
			if($(this).hasClass("coin")){
				$(".typeHB").show();
				$(".typeJJ").hide();
			}else{
				$(".typeHB").hide();
				$(".typeJJ").show();
			}
			cntL = $(this).parent(".app_tab_nav").position().left;
			if(thisLi>3){
				if(cntL<=-260){
					$(this).parent(".app_tab_nav").animate({
						left:-290
					});
				}else{
					$(this).parent(".app_tab_nav").animate({
						left:(3-thisLi)*tabW
					});
				}
				//console.log(cntL);
			}else{
				if(cntL<0){
					$(this).parent(".app_tab_nav").animate({
						left:(1-thisLi)*tabW
					});
				}else{
					$(this).parent(".app_tab_nav").animate({
						left:0
					});
				}
				//console.log(cntL+"-------"+thisLi);
			}
			var contentH = $(".app_tab_content").children(".tab_column_cur").height();
			$(".app_tab_content").height(contentH+78);
			bodH =$(document).height();
			//console.log(otherH);
		}
	});
});
$(".navTouch").on("touchstart",function(e){
	startTx=e.originalEvent.changedTouches[0].clientX;
	cntL = $(this).children(".app_tab_nav").position().left;
})
$(".navTouch").on("touchmove",function(e){
	moveTx=e.originalEvent.changedTouches[0].clientX;
	moveRunX = moveTx-startTx;
	curL = $(this).children(".app_tab_nav").position().left;
	if(moveRunX<0){
		if(curL>-290){
			$(this).children(".app_tab_nav").css({left:moveRunX+cntL})
		}else{
			$(this).children(".app_tab_nav").css({left:-290})
		}
	}else if(moveRunX>0){
		if(curL<0){
			$(this).children(".app_tab_nav").css({left:moveRunX+cntL})
		}else{
			$(this).children(".app_tab_nav").css({left:0})
		}
	}
	//console.log("Left:"+curL+"++++++"+moveRunX)
})
$(".navTouch").on("touchend",function(e){
	startTx=e.originalEvent.changedTouches[0].clientX;
})

//高度调整

//console.log(bodH);
$(".app_sort_Warp").css({height:winH-224});
$(".subNav_Warp").css({height:winH-145});
$(".drop_content").css({"min-height":winH-224});
$(".pT75 .drop_content,.selectZoneList").css({"min-height":winH-75});
$(".noticePage").css({"min-height":winH-146});
$(".noticePage .drop_content").css({"min-height":winH-146-78});
$(".app_seach_content").css({height:winH-76});
//console.log(winH)
//宽度调整
$(".equalW ul").each(function(){
	var butLiNum=$(this).children("li").length;
	var butLiW=$(this).width()/butLiNum
	$(this).children("li").width(butLiW)
});
//排序
$(".sort").on("click",function(){
	sclT = $(document).scrollTop();
	$(".app_body").addClass("posFix");
	$(".appTabItem").addClass("noclick");
	if(!$(this).hasClass("sortShow")){
		$(".app_sort_Warp").fadeIn();
		$(".app_sort").animate({
			top:223
		})
		$(this).addClass("sortShow");
	}else {
		$(".app_sort_Warp").fadeOut();
		$(".appTabItem").removeClass("noclick");
		$(".app_sort").animate({
			top:-400
		})
		$(this).removeClass("sortShow");
	}
});
$(".app_sort ul li").on("click",function(){
	var sortText = $(this).html();
	$(this).addClass("cur").siblings().removeClass("cur")
	$(".sort").html(sortText).removeClass("sortShow");
	$(".app_sort_Warp").fadeOut();
	$(".app_body").removeClass("posFix");
	$(document).scrollTop(sclT);
	$(".appTabItem").removeClass("noclick");
	$(".app_sort").animate({
		top:-400
	})
});
$(".app_sort_Warp").on("click",function(){
	$(".sort").removeClass("sortShow");
	$(".app_sort_Warp").fadeOut();
	$(".app_body").removeClass("posFix");
	$(document).scrollTop(sclT);
	$(".appTabItem").removeClass("noclick");
	$(".app_sort").animate({
		top:-400
	})
});
//基金超市列表

//搜索
$(".app_seach_content").css({"height":winH-76,"margin-top":winH-76});
$(".app_seach").on("click",function(){
	sclT = $(document).scrollTop();
	$(".app_body").addClass("posFix");
	$(".seach_page").show();
	$(".app_seach_top").animate({
		top:0
	},200);
	$(".app_seach_content").animate({
		marginTop:0
	},200);
	$(".seachInput").focus();
	//console.log(sclT)
})
$(".seach_cancel").on("click",function(){
	$(".app_body").removeClass("posFix");
	$(document).scrollTop(sclT);
	$(".app_seach_top").animate({
		top:-76
	},200);
	$(".app_seach_content").animate({
		marginTop:winH-76
	},200,function(){
		$(".seach_page").hide();
	});
});
//$(".app_seach_content").on("touchmove",function(){
//	e.preventDefault;
//});

//划屏
//alert(111)
$(".drop_content").on("touchstart",function(e){
	//e.preventDefault();
	startTx=e.originalEvent.changedTouches[0].clientX;
	startTy=e.originalEvent.changedTouches[0].clientY;
	cntNum=$(this).children(".list_columnThr_content").children(".lineLink").length;
	bodH =$(document).height();
	//console.log(cntNum+"+"+bodH);
	
})
$(".drop_content").on("touchmove",function(e){
	//底部按钮
	$(".afundBut").css("bottom",-80)

	//加载
	moveTy=e.originalEvent.changedTouches[0].clientY;
	moveRunY = moveTy-startTy;
	sclT = $(document).scrollTop();
	if (sclT==0 && moveRunY>0 && moveRunY<100){
		e.preventDefault();
		$(document.body).css("overflow","hidden");
		$(".drop_down").html("下拉更新");
		$(".drop_down").css({height:moveRunY})
	}else if (sclT==0 && moveRunY<0){
		$(".drop_down").css({height:0})
	}else if (sclT==0 && moveRunY>100 && $(".drop_down").height()>40){
		$(document.body).css("overflow","hidden");
		$(".drop_down").html("释放更新");
		$(".drop_down").css({height:100})
	}else if(sclT >= bodH-winH-10 && sclT-(bodH-winH)<100){
		//e.preventDefault();
		if(bodH>winH){
			$(".drop_up").html("上拉加载");
			$(".drop_up").css({height:100});
		}
	}else if(sclT-(bodH-winH)>=100){
		$(".drop_up").html("释放加载");
	}
	//console.log(bodH+"sfs"+winH);
})

$(".drop_content").on("touchend",function(e){
	//alert(sclT+"-----"+(bodH-winH-10))
	//底部按钮
	$(".afundBut").css("bottom",0);
	
	$(document.body).css("overflow","auto");
	upH=$(".drop_up").height();
	endTx=e.originalEvent.changedTouches[0].clientX;
	if(moveRunY>100 && sclT==0){
		$(".drop_down").html('<div class="loadWarp"><div class="load-container loadPage"><div class="loader">Loading...</div></div><div class="loadText">加载中...</div></div>');
		dropDown_update();
	}else if(upH==100){
		$(".drop_up").html('<div class="loadWarp"><div class="load-container loadPage"><div class="loader">Loading...</div></div><div class="loadText">加载中...</div></div>');
		dropUp_update();
	}else{
		$(".drop_down").animate({
			height:0
		},500);
	}

})
//图表
$(".timeZone").on("click",function(){
	if(!$(this).hasClass("cur")){
		$(this).addClass("cur").parent().siblings().children(".cur").removeClass("cur");
	}
});

//表单
$(".chackBox").on("click",function(){
	if($(this).children("input").is(":checked")){
		$(this).addClass("chacked");
	}else{
		$(this).removeClass("chacked");
	}
});

/*
 * 表单验证
 */
//重置数据
var thisValLen;
$(".appRst").on("click",function(){
	$(this).hide().siblings(".showRst").val("").focus();
	
})

$(".showRst").focus(function(){
	$(this).keyup(function(){
		thisValLen = $(this).val().length;
		if(thisValLen>0){
			$(this).siblings(".appRst").show();
		}
	});
});

//radio 操作
$(".radioInput").each(function(){
	if($(this).attr("checked")){
		$(this).parent("label").addClass("radioSelect");
	}
	$(this).change(function(){
		if(!$(this).parent("label").hasClass("radioSelect")){
			$(this).parent("label").addClass("radioSelect").parent(".radioItem").siblings().children("label").removeClass("radioSelect");
		}
	})
});


$(".pullWarp,.backPsw").on("click",function(){
	$(".pullMine").animate({
			bottom:-790
		},200);
		$(".pullWarp").fadeOut();
})

//温馨提示操作
$(document).on("click",".singleButton .PopSure",function(){
	if($(this).attr("id")=="OPButSureID"){
		var tag = $(this)
		popUpHide(tag)
	}
});

//弹出框操作
$(document).on("click",".PopCancel",function(){
	var tag = $(this)
	popUpHide(tag)
});


//tab 内容展示
$(".showTab").on("click",function(){
	if(!$(this).hasClass("showCur")){
		$(this).addClass("showCur").siblings(".showCnt").slideDown();
		$(this).parent(".listShow").siblings().children(".showTab").removeClass("showCur").siblings(".showCnt").slideUp();
	}else{
		$(this).removeClass("showCur").siblings(".showCnt").slideUp();
	}
});



//jquery结束
});

//交易记录
function subMenuOut(topVal){
		$(".subNavlist").animate({
			top:topVal
		},200);
		$(".subNav_Warp").fadeOut();
}
function subMenuIn(topVal){
		$(".subNavlist").animate({
			top:-topVal
		},200);
		$(".subNav_Warp").fadeIn();
}
//输入密码
function keyPad(){
	$(".pullPassWord").val("");
	$(".pullMine").animate({
		bottom:0
	},200);
	$(".pullWarp").fadeIn();
	$(".pullPassWord").focus();
	setTimeout(function(){
		$(document).scrollTop(0);
	},100)	
}

/*PopUp
 **loadPop(0,"body","正在添加")
 **loadPop(1,".list_columnThr_content","正在加载，请稍后...")
 **使用方法
 **需要加载时调用loadPop(loadType,posTag,intText)
 **loadType：加载类型；0为弹出窗加载；1为内容加载
 **posTag:加载位置；loadType为0时，posTag==“body”；loadType为1时，posTag==“显示加载状态的DIV样式名称”
 **intText:初始文字
 **加载完成时调用removePop(removeType,suessText)
 **removeType:完成的类型；0为弹出窗加载；1为内容加载
 **suessText：加载完成展示的文字，removeType为1时忽略此参数
 */
var loadHtml;
function loadPop(loadType,posTag,intText){
	if(loadType==0){
		loadHtml = '<div class="loadPopUp"><div class="loadIco"><img src="images/ico_suss.png" alt=""> </div><div class="loadWarp"><div class="load-container loadPop"><div class="loader">Loading...</div></div></div><div class="loadText"></div></div>';
	}else{
		loadHtml = '<div class="loadWarp"><div class="load-container loadPage"><div class="loader">Loading...</div></div><div class="loadText"></div></div>'
	}
	$(posTag).append(loadHtml);
	$(".loadText").html(intText);
}
function removePop(loadType,posTag,suessText){
	if(loadType==0){
		$(".loadWarp").hide();
		$(".loadIco").show();
		$(".loadText").html(suessText);
		setTimeout(function(){
			$(".loadPopUp").fadeOut();
		},500)
		setTimeout(function(){
			$(".loadPopUp").remove();
		},700)
	}else{
		$(posTag+" .loadWarp").remove();
	}
}


/*
 * 弹出框可操作
 **使用方法
 ***popUpOP(".popUpOp","测试测试测试测","试测试测试测","确定","verifySure","取消","verifyCancer","zhanghu.html","login.html")
 * 参数设置
 * OPtag——弹出框Tag
 * OPCnt——弹出框内容
 * OPT——弹出框标题（没有标题时传空）
 * OPButSure——弹出框确定按钮文字
 * OPButSureID——弹出框确定按钮ID
 * OPButCancel——弹出框取消按钮文字
 * OPButCancelID——弹出框取消按钮ID
 * sureLink——确认链接（可选）
 * cancelLink——放弃连接（可选）
 */

function popUpOP(OPtag,OPCnt,OPT,OPButSure,OPButSureID,OPButCancel,OPButCancelID,sureLink,cancelLink){
	if(sureLink){
	}else{
		sureLink='javascript:void(0);'
	}
	if(cancelLink){
	}else{
		cancelLink='javascript:void(0);'
	}
	var result='';
		result+='<div class="popUp popUpOp">'
					+'<div class="popUpMain">'
					+	'<div class="popUpCnt">'
					+		'<div class="popUpT">'+OPT+'</div>'
					+		'<div class="popUptext">'+OPCnt+'</div>'
					+	'</div>'
					+	'<div class="popUpButton">'					
					+		'<a href="'+cancelLink+'" class="PopCancel"'+"id="+OPButCancelID+'>'+OPButCancel+'</a>'
					+		'<a href="'+sureLink+'" class="PopSure"'+"id="+OPButSureID+'>'+OPButSure+'</a>'
					+	'</div>'
				+	'</div>'
				+'</div>';
		if(OPtag==".popUpOp"){
			$("body").append(result);
//			if($(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUptext").text().length>14){
//				$(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUptext").css("text-align","left")
//			};
			if(OPT==''){
				$(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUpT").remove();
				$(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUptext").addClass("noT");
			}
		}
	$(OPtag).fadeIn();
	$(OPtag).children(".popUpMain").fadeIn();
	var tagH=$(OPtag).children(".popUpMain").height();
	$(OPtag).children(".popUpMain").css("margin-top",-tagH/2)
	//console.log(tagH)
}
/*
 * 温馨提示框弹出框
 **使用方法
 ***popUpOPSingle(".popUpOp","温馨提示内容信息","标题","确定","verifySure")
 * 参数设置
 * OPtag——弹出框Tag
 * OPCnt——弹出框内容
 * OPT——弹出框标题（没有标题时传空）
 * OPButSure——弹出框确定按钮文字
 * OPButSureID——弹出框确定按钮ID
 * OPLink——弹出框按钮的链接
 */

function popUpOPSingle(OPtag,OPCnt,OPT,OPButSure,OPButSureID,OPLink){
	if(OPButSureID){
	}else{
		OPButSureID='OPButSureID'
	}
	if(OPLink){
	}else{
		OPLink='javascript:void(0);'
	}
	var result='';
		result+='<div class="popUp popUpOp">'
					+'<div class="popUpMain">'
					+	'<div class="popUpCnt">'
					+		'<div class="popUpT">'+OPT+'</div>'
					+		'<div class="popUptext">'+OPCnt+'</div>'
					+	'</div>'
					+	'<div class="popUpButton singleButton">'
					+		'<a href="'+OPLink+'" class="PopSure"'+"id="+OPButSureID+'>'+OPButSure+'</a>'
					+	'</div>'
				+	'</div>'
				+'</div>';
		if(OPtag==".popUpOp"){
			$("body").append(result);
//			if($(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUptext").text().length>14){
//				$(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUptext").css("text-align","left")
//			};
			if(OPT==''){
				$(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUpT").remove();
				$(OPtag).children(".popUpMain").children(".popUpCnt").children(".popUptext").addClass("noT");
			}
		}
	$(OPtag).fadeIn();
	$(OPtag).children(".popUpMain").fadeIn();
	var tagH=$(OPtag).children(".popUpMain").height();
	$(OPtag).children(".popUpMain").css("margin-top",-tagH/2)
	//console.log(tagH)
}

function popUpHide(tag){
	//console.log(tag)
	if(tag.parents().hasClass("popUpOp")){
		tag.parents(".popUpOp").remove();
	}else{
		tag.parents(".popUpMain").fadeOut();
		tag.parents(".popUp").fadeOut();
	}	
}

/*
 * 提示信息框
 * *用法hintPop('测试错误提示',"hintErrorIco")
 * **参数说明
 * hintText——提示文字信息
 * errorIco——是否显示错误ico，显示则传"hintErrorIco"，不显示则不传参数
 */


function hintPop(hintText,errorIco){
	var hintResult='';
		hintResult += '<div class="hint"><div class="hintMain">'
					+		'<div class="hintIco '+errorIco+'"></div>'
					+		'<div class="hinttext">'+hintText+'</div></div></div>';
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
	},1000)
	setTimeout(function(){
		$(".hint").remove();
	},1500)
}


//PopUp 结束

function dropOver(){
	$(".app_tab_content").css("height","auto")
	$(".drop_down").animate({
		height:0
	},200,function(){
		if(!$(".app_tab_content").parents().hasClass("app_section")){
			var contentH = $(".app_tab_content").children(".tab_column_cur").height();
			$(".app_tab_content").height(contentH+78);			
		}
		bodH =$(document).height();
		$(this).html("下拉刷新")
	});
	$(".drop_up").animate({
		height:0
	},200,function(){
		if(!$(".app_tab_content").parents().hasClass("app_section")){
			var contentH = $(".app_tab_content").children(".tab_column_cur").height();
			$(".app_tab_content").height(contentH+78);
		}
		bodH =$(document).height();
		$(this).html("上拉加载")
	});
	
}






