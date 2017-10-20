$(document).ready(function(e){

	/*
	 * table表格寄偶行,首尾样式
	 */
	$(".table,.tableB,.tableC").each(function(){
		$(this).children("thead").children("tr").children("th:first").addClass("first");
		$(this).children("thead").children("tr").children("th:last-child").addClass("last");
		$(this).children().children("tr").children("td:first-child").addClass("first");
		$(this).children().children("tr").children("td:last-child").addClass("last");
		$(this).children("tbody").children("tr:odd").addClass("odd");
		$(this).children("tbody").children("tr:even").addClass("even");
	});
	
	/*
	 * Tab
	 * Tab list 宽度
	 */
	$(".tabList").each(function(){
		var lisum = $(this).children("ul").children("li").length;
		var thisW = $(this).width();
		var liW = thisW/lisum;
		//console.log(lisum)
		$(this).children("ul").children("li").width(liW);
	});
	
	/*
	 * Tab
	 * Tab cnt 宽度
	 */
	$(".tabCnts").each(function(){
		var cntsum = $(this).children(".tabCnt").length;
		var cntW = $(this).parent(".tabWarp").width();
		var cntsW = cntW*cntsum;
		//console.log(cntsW);
		$(this).width(cntsW);
		$(this).children(".tabCnt").width(cntW);
	});
	
	/*
	 * Tab
	 * Tab 点击展示
	 */
	$(document).on("click",".tabList ul li",function(){
		var curLi = $(this).index();
		var cntMove = $(this).parents(".tabList").siblings(".tabCnts").children(".tabCnt").width();
		if(!$(this).hasClass("tabCur")){
			$(this).addClass("tabCur").siblings("li").removeClass("tabCur");
			$(this).parents(".tabList").siblings(".tabCnts").animate({
				left:-cntMove*curLi
			});
		}
	});

	
	/*
	 * content show
	 */
	$(document).on("click",".textShow",function(){
		$(this).parents("dl").siblings("dl").children(".showCnt").removeClass("show").siblings("dd").children(".textShow").html("展开").removeClass("icoUp");
		if($(this).hasClass("icoUp")){
			$(this).html("展开").removeClass("icoUp").parent().siblings(".showCnt").toggleClass("show");
		}else{
			$(this).html("收起").addClass("icoUp").parent().siblings(".showCnt").toggleClass("show");
		}
	})
	
	/*
	 * 首次登陆弹出框
	 */
	//popUpOPSingle(".popUpOp","初次登录，为了您账户的安全，建议您修改初始密码。","提示","立即修改","verifySure","../html/change-password.html");

	//关闭popUp
	$(document).on("click",".close",function(){
		var tag = $(this)
		popUpHide(tag);
	});
	
	//radio
	$(".radio").each(function(){
		//console.log($(this).attr("checked"));
		if($(this).attr("checked")){
			$(this).parent("label").addClass("checked");
		}
		$(this).change(function(){
			if(!$(this).parent("label").hasClass("checked")){
				$(this).parent("label").addClass("checked").siblings("label").removeClass("checked");
			}
		})
	});
});
//jq Over


var num= /^[0-9]*$/;
/*
 * 滚动展示
 */
function scroll(tag,rows,unitW){
	var pages;
	var scrollSum;
	var pageW;
	var pageSum;
	var scrollCntW;
	var curPage=1;
	var moveW;
	$(tag).each(function(){
		scrollSum=$(this).children(".scrollUnit").length;//元素个数
		pageW=$(this).parent().width();//容器宽度
		pageSum=Math.floor(pageW/unitW)*rows;//每页个数
		pages=Math.ceil(scrollSum/pageSum);//总页数
		scrollCntW = Math.ceil(scrollSum/rows)*unitW //pageW*pages;
		//var pageSum = scrollSum/rows
		console.log(Math.ceil(scrollSum/2));
		$(this).width(scrollCntW);
	});
	$(".prevPage").on("click",function(){
		if(curPage!==1){
			curPage=curPage-1;
			$(this).parent().children(".prevPage").html(curPage);
			$(this).parent(".scrollPage").siblings(".scrollWarp").children(".scrollCnt").animate({
				left:(curPage-1)*-pageW
			});
		}
	});
	$(".nextPage").html(pages).on("click",function(){
		if(curPage!==pages){
			curPage=curPage+1
			$(this).parent().children(".prevPage").html(curPage);
			$(this).parent(".scrollPage").siblings(".scrollWarp").children(".scrollCnt").animate({
				left:(curPage-1)*-pageW
			});
		}	
	});
}

/*
 * 循环滚动展示
 */

function scrollLoop(tag,rows,unitW){
	var pages;
	var scrollSum;
	var pageW;
	var pageSum;
	var scrollCntW;
	var curPage=2;
	var moveW;
	$(tag).each(function(){
		scrollSum=$(this).children(".scrollUnit").length;//元素个数
		pageW=$(this).parent().width();//容器宽度
		pageSum=Math.floor(pageW/unitW)*rows;//每页个数
		pages=Math.ceil(scrollSum/pageSum);//总页数
		scrollCntW = Math.ceil(scrollSum/rows)*unitW //pageW*pages;
		//var pageSum = scrollSum/rows
		$(this).width(scrollCntW);
	});
	console.log("当前"+curPage);
	$(".prevPage").on("click",function(){
		if(curPage!==1){
			curPage=curPage-1;
			$(this).parent(".scrollPage").siblings(".scrollWarp").children(".scrollCnt").animate({
				left:(curPage-1)*-pageW
			},function(){
				if(curPage==1){
					curPage=3
					$(this).css("left",curPage*-pageW)
					curPage=4
				}
			console.log("执行完成"+curPage);
			});
		}
//		console.log("上一页"+curPage);
	});
	$(".nextPage").html(pages).on("click",function(){
		if(curPage!==pages){
			curPage=curPage+1
			$(this).parent(".scrollPage").siblings(".scrollWarp").children(".scrollCnt").animate({
				left:(curPage-1)*-pageW
			},function(){
				if(curPage==pages){
					curPage=2
					$(this).css("left",curPage*-pageW)
					curPage=3
//					console.log("执行完成"+curPage);
				}
			});
		}	
//		console.log("下一页"+curPage);
	});
}

/*
 * 提示信息框
 * *用法hintPop('测试错误提示',"hintErrorIco")
 * **参数说明
 * hintText——提示文字信息
 * errorIco——是否显示错误ico，显示则传"hintErrorIco"，不显示则不传参数
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
	},1000)
	setTimeout(function(){
		$(".hint").remove();
	},1500)
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
		result+='<div class="popUp popUpOp fZone">'
					+'<div class="popUpMain">'
					+	'<div class="popUpCnt">'
					+		'<div class="popUpT">'+OPT+'<a href="javascript:void(0);" class="close">&Chi;</a></div>'
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
