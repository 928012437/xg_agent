<template>
	<div>
		
<!-- <div id="main" style="width: 600px;height:400px;"></div> -->
		<div id="wrapper">
		
		<div id="main" style="width: 1660px;height:400px;"></div>
		<!--  <ul>
					    <li>
					    	<input type="checkbox">
					    </li>
					    <li>
					    	<input type="checkbox">
					    </li>
					    <li>
					    	<input type="checkbox">
					    </li>
					    <li>
					    	<input type="checkbox">
					    </li>
		</ul> -->
		</div>
		<button @click='add'>更新</button>
		<button @click='left' class="left">向左</button>
		<button @click='right' class="right">向右</button>
	<p>sdfsdfsfsdfsdfsdfsfsfsfsdfsdfsdfsdf</p>
	<div>
</template>
<script>
  	 import echarts from 'echarts/lib/echarts'
 	 require('echarts/lib/chart/bar');
	 // 引入提示框和标题组件
	 require('echarts/lib/component/tooltip');
	 require('echarts/lib/component/title');


	// import  IScroll from 'iscroll';
	 import  IScroll from 'iscroll'

	// var echarts = require('echarts/lib/echarts');
// 引入柱状图
/*require('echarts/lib/chart/bar');
// 引入提示框和标题组件
require('echarts/lib/component/tooltip');
require('echarts/lib/component/title');*/

// 基于准备好的dom，初始化echarts实例



export default{
	data(){
		return{
			 data: [10, 10, 20,20,20,60],
			 name:["衫","子","跟鞋","子",'MIG','地方'],
			 myChart:null,
			 myScroll:null,
			 num:100,
			 chart:null
		}
	},
	created(){
		
	},

	mounted(){
		const _this=this;
		this.chart=document.getElementById('main')
		// const wrapper = document.getElementById('wrapper');
		this.char();
		this.load();
	
	},
	/*updated(){
		this.char();
		this.load();
	},*/
	
	methods:{
		char(){
			this.myChart = echarts.init(this.chart);
			// 绘制图表
			this.myChart.setOption({
				color: ['#3398DB'],
			    title: { text: 'ECharts 入门示例' },
			    tooltip: {},
				xAxis: {
				    data: this.name
				},
			    yAxis: {},
			    series: [{
			        name: '销量',
			        type: 'bar',
			        data: this.data
			    }]
			})
		},

		load(){				
			this.myScroll = new IScroll('#wrapper',{
				// mouseWheel: true,
		   		scrollX: true,
		   		
			})
		
		},
		add(){	

			let w=parseInt(this.chart.style.width);
			this.chart.style.width=(w+460)+'px'
			let n=["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"];
			let d=[6,9,6,4,8,2];
			let _this=this;
			this.myChart.showLoading();
			setTimeout(function(){
				 _this.myChart.hideLoading();
				_this.data=_this.data.concat(d);
				_this.name=_this.name.concat(n);
				_this.char();
				_this.load()
			},3000)
		},
		
	}
}
</script>
<style lang='less'scoped>
	#wrapper {
			background: green;
			overflow: hidden;
			width: 100%;	
			height: 400px;		
			border:2px solid red;
			padding: 20px;
			ul{
				border:2px solid orange;
				height: 900px;
				width: 3400px
			}

		li{
			display: inline-block;
			width: 80px;
			border:1px solid red;
		}
	}
	.left,.right{
		display: inline-block;
	}
	.right{float: right;}
</style>