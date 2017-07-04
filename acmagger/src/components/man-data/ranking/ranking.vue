<template>
    <div>
    <fold-nav :selectedValue='selectedValue'></fold-nav>
	<div id="chart" style="width:100%;height:300px">
	<div class="chart-handle">
		<button type="button" class="pull-left button" @click="changeData(++count)">+</button>
		<button type="button" class="pull-right button" @click="changeData(--count)">-</button>
	</div>
	
    </div>
</template>
<script type='text/ecmascript-6'>
import API from '../../../api/api.js'
import {initChart} from '../chartMixin.js'
import foldNav from '../fold-nav/fold-nav.vue'

export default{
	data(){
		return{
			myChart:null,
			data:null,
			selectedValue:{
			 	value:'预期未跟进'
			 }
		}
	},
	components:{
		foldNav

	},
	created(){
		// this.getDay();

	},
	mounted(){
		var chart= document.getElementById('chart');
		this.myChart=initChart(chart);
		this.myChart.showLoading();
		this.setChart()		
	
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
	watch:{
		'selectedValue.value'(val){
			alert(val)
		}
	}

}
</script>

<style lang='less' scoped>
@import '../../../common/style/mixin.less';
	#wrapper{
		background: orange;
		.size(100%,400px)
	}

</style>