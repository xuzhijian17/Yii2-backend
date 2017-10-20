function trendChart(url,args){
	$('#chart').empty();
	$.getJSON(url, args, function(data){
		args['type'] == 0 ? trendSyChart(data) : trendJzChart(data, args['typeCode']);
	});
}
//净值走势图
function trendJzChart(data, typeCode){
	var chart = echarts.init(document.getElementById("chart"));
	console.log(typeCode)
	var time = []; line = []; tline = []; hsline = [];
	for(var i = 0; i < data.length; i++){
		time.push(data[i][0]);
		line.push(data[i][2]);
		if(typeCode != '1106' || typeCode != '1109'){
			tline.push(data[i][4]);
			//hsline.push(data[i][6]);
		}
	}
	
	chart.setOption({
		animation : false,
		title : {
			show : false,
		},
		tooltip : {
			trigger: 'axis',
			//position : ["40%", '0'],
			formatter: function(data) { 
				var bjj = data[0]['value'];
				if(bjj > 0){
					$('#bjj').removeClass('fall').addClass('rise');
					$('#bjj').text("+"+bjj.toString()+"%");
				}else{
					$('#bjj').removeClass('rise').addClass('fall');
					$('#bjj').text(bjj.toString()+"%");
				}
				var str = '';
				if(bjj > 0){
					str += ('累计收益：+'+bjj.toString()+'%</br>');
				}else{
					str += ('累计收益：'+bjj.toString()+'%</br>');
				}
				if(typeCode == '1106' || typeCode == '1109'){
					return  str;
				}
				var tjj = data[1]['value'];
				if(tjj > 0){
					$('#tjj').removeClass('fall').addClass('rise');
					$('#tjj').text("+"+tjj.toString()+"%");
				}else{
					$('#tjj').removeClass('rise').addClass('fall');
					$('#tjj').text(tjj.toString()+"%");
				}
				if(tjj > 0){
					str += ('同类收益：+'+tjj.toString()+'%</br>');
				}else{
					str += ('同类收益：'+tjj.toString()+'%</br>');
				}
				// var hsjj = data[2]['value'];
				// if(hsjj > 0){
					// $('#hsjj').removeClass('fall').addClass('rise');
					// $('#hsjj').text("+"+hsjj.toString()+"%");
				// }else{
					// $('#hsjj').removeClass('rise').addClass('fall');
					// $('#hsjj').text(hsjj.toString()+"%");
				// }
				// if(hsjj > 0){
					// str += ('沪深指数：+'+hsjj.toString()+'%');
				// }else{
					// str += ('沪深指数：'+hsjj.toString()+'%');
				// }
				return  str;
			}	
		},
		grid :{
			//top : -20,
		},
		xAxis : [
			{
				data : time,
				nameLocation : 'end',
				boundaryGap : false,
				splitNumber:5,
				axisLine :{
					show:false
				},
				axisTick :{
					show:false
				},
				
				axisLabel : {
					formatter: function (value, index) {
						var date = new Date(value);
						var texts = [(date.getMonth() + 1), date.getDate()];
						return texts.join('-');
					}
				},
				splitLine:{
					show:false
			　　},
			}
		],
		yAxis : [
			{
				type : 'value',
				splitNumber : 5,
				scale : true,
				axisLabel : {
					formatter: '{value}%'
				},
				axisLine :{
					show:false
				},
				axisTick :{
					show:false
				},
			}
		],
		series : [
			{
				name:'累计收益:',
				type:'line',
				symbol:'none',
				hoverAnimation : false,
				data:line,
				lineStyle:{
					normal:{
						width :2,
						color:"#d782d3",
					}
				}
			},
			{
				name:'同类收益:',
				type:'line',
				symbol:'none',
				hoverAnimation : false,
				data:tline,
				lineStyle:{
					normal:{
						width :1.5,
						color:"#ffd43e",
					}
				}
			},
			// {
				// name:'沪深指数:',
				// type:'line',
				// symbol:'none',
				// hoverAnimation : false,
				// data:hsline,
				// lineStyle:{
					// normal:{
						// width :1,
						// color:"#98c7ff",
					// }
				// }
			// }
		]
	});
}


//收益走势图
function trendSyChart(data){
	var chart = echarts.init(document.getElementById("chart"));
	var time = []; line = [];
	for(var i = 0; i < data.length; i++){
		time.push(data[i][0]);
		line.push(data[i][1]);
	}
	var yline = line.slice(0);
	yline = yline.sort(); var ymin = yline[0]; 
	yline = yline.reverse(); var ymax = yline[0];
	console.log(ymax+'---'+ymin);
	chart.setOption({
		animation : false,
		title : {
			show : false,
		},
		tooltip : {
			trigger: 'axis',
			//position : ["40%", '0'],
			formatter: function(data) { 
				params = data[0]; var str = '';
				str += ('净值：'+params['value'].toString());
				return  str;
			}
		},
		 
		xAxis : [
			{
				data : time,
				//nameLocation : 'end',
				boundaryGap : false,
				axisLine :{
					show:false
				},
				axisTick :{
					show:false
				},
				axisLabel : {
					formatter: function (value, index) {
						var date = new Date(value);
						var texts = [(date.getMonth() + 1), date.getDate()];
						return texts.join('-');
					}
				},
				splitLine:{
					show:false
			　　},
			}
		],
		yAxis : [
			{
				type : 'value',
				//splitNumber : 2,
				// min : ymin,
				// max : ymax,
				scale : true,
				axisLabel : {
					formatter: '{value}'
				},
				axisLine :{
					show:false
				},
				axisTick :{
					show:false
				},
				splitLine:{
					interval:3,
			　　},
			}
		],
		series : [
			{
				name:'累计收益:',
				type:'line',
				symbol:'none',
				hoverAnimation : false,
				data:line,
				lineStyle:{
					normal:{
						color:"#0394ff",
						width:0.5,
					}
				}
			}
		]
	});
}
