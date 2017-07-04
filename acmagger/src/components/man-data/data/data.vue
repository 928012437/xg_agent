<template>
<div>
	<fold-nav :selectedValue='selectedValue'></fold-nav>
	<div id="chart" style="width:100%;height:300px">
	</div>
	<div class="chart-handle">
		<button type="button" class="pull-left button" @click="changeData(++count)">+</button>
		<button type="button" class="pull-right button" @click="changeData(--count)">-</button>
	</div>
	<div class="card data-card">
		    <div class="card-header">跟进情况</div>
		    <div class="card-content">
		      <ul class="card-content-inner card-data-list">
		      	<li>13组<span>新客户来访</span></li>
		      	<li>13组<span>老客户来访</span></li>
		      	<li>30组<span>新客户来电</span></li>
		      	<li>23组<span>去电</span></li>
		      </ul>
		    </div>    
	 </div>

	 <div class="card ">
		    <div class="card-header card-top">认筹与成交情况</div>
		    <div class="card-content">
		     <div class="list-block ">
		         <ul>
		           <li class="item-content item-link">
		             <div class="item-media"><i class="icon icon-f7"></i></div>
		             <div class="item-inner">
		               <div class="item-title">
		               	认筹
		               </div>
		               <div class="item-after">
		               	新增：0套 换房：0套
		               		<br>
		               		退房：0套
		               </div>
		             </div>
		           </li>

		           <li class="item-content item-link">
		             <div class="item-media"><i class="icon icon-f7"></i></div>
		             <div class="item-inner">
		               <div class="item-title">认购</div>
		               <div class="item-after">
		               		新增：0套 换房：0套
		               		<br>
		               		退房：0套
		               </div>
		             </div>
		           </li>
		           <li class="item-content item-link">
		             <div class="item-media"><i class="icon icon-f7"></i></div>
		             <div class="item-inner">
		               <div class="item-title">签约</div>
		               <div class="item-after">
		               			新增：0套 换房：0套
		               		       <br> 
		               		退房：0套
		               </div>
		             </div>
		           </li>
		           
		         </ul>
		        </div>
		    </div>		   
		 </div>
	
	<div class="card data-card">
		     <div class="card-header">当前逾期情况</div>
		     <div class="card-content">
		       <ul class="card-content-inner card-data-list">
		       	
		       <li>13组<span>老客户来访</span></li>
		       <li>30组<span>新客户来电</span></li>
		       <li>23组<span>去电</span></li>
		       </ul>
		     </div>    
	 </div>
	<div class="spacing"></div>
    </div>
</template>
<script type='text/ecmascript-6'>
import API from '../../../api/api.js'
import foldNav from '../fold-nav/fold-nav.vue'
import {initChart} from '../chartMixin.js'


export default{
	data(){
		return{
			 myChart:null,
			 count:0,//跟踪年月日
			 selectedValue:{
			 	value:'预期未跟进'
			 }
		}
	},
	components:{
		foldNav,
		
	},
	created(){
		 
	},
	mounted(){
		var chart=document.getElementById('chart');
		this.myChart = initChart(chart);
		this.myChart.showLoading();
		 this.setChart()//初始化

		//点击更新数据
		this.myChart.on('click',function(params){
			alert(params.name);
		})		
	},

	methods:{
		//设置参数/天/月/年
		setChart(){
			let myChart=this.myChart;
			API.dataGetDay(function(res){
				myChart.hideLoading();
				myChart.setOption({
					xAxis:{
						data:res.date,
					},
					series:[{
						data:res.data,
					}]
			})

			})

			
		},
		
		//0日 1月 2季度 3年  
		changeData(count){
			var vm =this;
			
			switch(this.count){
				case 1:
					// this.myChart.showLoading();
					alert('月')
					
				break;
				case 2:
					// this.myChart.showLoading();
					alert('季度')
				break;
				case 3:
					// this.myChart.showLoading();
					alert('年')
				break;

				default:
					alert(vm.count)
					if(this.count>3){
						this.count=3;
					}else if(this.count<0){
						this.count=0;
						alert(vm.count)
					}
				break;
			}
		}
		
	},
	computed:{
		
	},
	watch:{
		'selectedValue.value'(val){
			alert(val)
		}
	}


}

</script>

<style lang='less' scoped>
@import '../../../common/style/mixin.less';
@import '../../../common/style/mixins.less';
.chart-handle{
	.size(100%,30px);
	padding: 0 5px;
	margin-top: -35px;
}
.spacing{
	height: 50px;
	width: 100%;
	background: transparent;
}
.card{
	margin: 0;
	margin-bottom: 10px;
	box-shadow:none;
	.item-after{
		margin-top: -6px;
	}
.card-content-inner{
	padding: 0.75rem 0;
}

	}
@media only screen and (-webkit-min-device-pixel-ratio: 2){
	.card-header:after{
		transform: scaleY(1);
	}
}

	

.data-card{
	.card-data-list{
		margin-top: 0px;
		.flexbox;
		li{
			.flex(1);
			text-align: center;
			display: inline-block;
			font-size: 18px;
			&+li{
				border-left: 1px solid @color-split;
			}
			span{
				display: block;
				font-size: 14px
			}}}}	
	

</style>