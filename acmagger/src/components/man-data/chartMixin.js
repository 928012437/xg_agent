 import echarts from 'echarts';

 // 设置初始的option 

 export function initChart(ele){
 	var myChart =echarts.init(ele);
 	var option = {
 			    tooltip: {},
 			    xAxis: {
 			        data: []
 			    },
 			    backgroundColor:['#fff'],
 			    yAxis: {
 				     type: 'value',
 	       			 boundaryGap: [0, '100%']
 				    },
 				dataZoom: [{
 			        type: 'inside',		        
 			        //窗口显示的个数 end-start
 			        start: 0,
 			        end: 60
 	   			 }, {//设置图标
 			        start: 0,
 			        end: 15,
 			        handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
 			        handleSize: '100%',
 			        handleStyle: {
 			            color: '#0894ec',
 			            shadowBlur: 3,
 			            shadowColor: 'rgba(0, 0, 0, 0.6)',
 			            shadowOffsetX: 2,
 			            shadowOffsetY: 2
 			        }
 			    }],
 			    series: [{ 			        
 			        type: 'bar',
 			        data: []
 			    }]
 			}
 	if (option && typeof option === "object") {
 			 myChart.setOption(option, true);
 			   
 		};

 	return myChart;

 }


 

 


 
	
