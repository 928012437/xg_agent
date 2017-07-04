<template>
   <div>
		<div id="chart" style="width:100%;height:300px"></div>

		<div id="rateChart " style="width:100%;height:300px"></div>

   </div>
</template>
<script type='text/ecmascript-6'>
import echarts from 'echarts'
export default{
	data(){
		return{
			myChart:null,
			rateChart:null,
			data:{
				data:[23,54,65,14,31,1],
				date:[1,2,3,4,6,7]
			}
		}
	},
	created(){

	},
	mounted(){
		var chart=document.getElementById('chart'),
			rateChart=document.getElementById('rateChart');
		this.myChart = echarts.init(chart);
		// this.rateChart=echarts.init(rateChart);	
		// 初始 option
		var option = {
			title : {
			       text: '转化率',
			       subtext: '纯属虚构',
			      
			   },
		    tooltip: {},
		    backgroundColor:['#fff'],
		    legend: {
		        data:['销量']
		    },
		    xAxis: {
		        data: []
		    },
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
		};
		if (option && typeof option === "object") {
		    this.myChart.setOption(option, true);
		};
		this.myChart.showLoading();

		this.setChart(this.data)
	},
	methods:{
		setChart(data){
			this.myChart.hideLoading();
			this.myChart.setOption({
				xAxis:{
					data:data.date,
				},
				series:[{
					data:data.data,
					
				}]
			})
		}	
	}

}

</script>

<style lang='less' scoped>


</style>