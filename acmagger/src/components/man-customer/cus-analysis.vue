<template>
    <div >
    <tab>
	      <tab-item  v-on:click.native="demo1 = '问询客户'">问询客户</tab-item>
	      <tab-item  v-on:click.native="demo1 = '来访客户'">来访客户</tab-item>
	      <tab-item  v-on:click.native="demo1 = '认购客户'">认购客户</tab-item>
	    	 
	    <tab-item  v-on:click.native="demo1 = '签约客户'">签约客户</tab-item>
	    </tab>
    	
		<div id="chart" style="width:100%;height:300px">
		
		 </div>

    </div>
</template>

<script type='text/ecmascript-6'>
import echarts from 'echarts'
import tab from '../tab/tab.vue';
import tabItem from '../tab/tab-item.vue';

export default{
	data(){
		return{
			myChart:null,
			data:{
				data:[56,85,31,14,36,12],
				date:[0,2,5,3,7,4]
			},
			cusType:'问询客户'
			
		}
	},
	components:{
		tab,
		tabItem
	},
	created(){

	},
	mounted(){
		var chart = document.getElementById('chart');
		this.myChart=echarts.init(chart);
		// 初始 option
		var option = {
			backgroundColor:['#fff'],
		    tooltip: {},
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
		//初始化图标
		if (option && typeof option === "object") {
		    this.myChart.setOption(option, true);
		};
		this.setChart(this.data)
	},
	methods:{
		setChart(data){
			this.myChart.setOption({
				xAxis: {
				    data: data.date
				},
				series: [{
				    type: 'bar',
				    data: data.data
				}]
			})
		},
		changeType(event){
			this.cusType=event.target.innerHTML;
			alert(this.cusType)
		}
		
	}
}


</script>

<style lang='less' scoped>
@import '../../common/style/mixins.less' ;
.ana-nav{
	background: #fff;
	&>ul{
		.flexbox;
		border-bottom: 1px solid @color-split;
		margin: 0;
		li{
			padding: 8px 0;
			text-align: center;
			display: inline-block;
			.flex(1)
		}
		.active{
			border-bottom:2px solid @color-primary;
		}
	}
	
}
.infor-nav{
	margin-top: 20px;
	background: red;
	height: 800px;
	width: 100%;
}

</style>