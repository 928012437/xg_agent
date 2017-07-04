<template>
 <div class="content">
	<shop-header></shop-header>
	 <div class="card">
	   	 <div class="card-header">
	   	 	<div class="card-header-text">累计佣金</div>
	   	 	<div>{{leiji}}元</div>
	   	 </div>
    </div>
    
	<div class="home-nav">
	<tab >
		<tab-item @click.native='index=1'>全部</tab-item>
		<tab-item @click.native='index=2'>待审核</tab-item>
		<tab-item @click.native='index=3'>待打款</tab-item>
		<tab-item @click.native='index=4'>已打款</tab-item>
		<tab-item @click.native='index=5'>无效</tab-item>
	</tab>
	</div>
	<div class="content goods-wrap ">
		
				<pay-cell :payData='payData' :payCount='payCount' :index='index'></pay-cell>
	    	    <div v-show='isHave' >暂无数据</div>

	</div>
	
</div>
</template>
<script type='text/ecmascript-6'>
import shopHeader from '../components/header.vue'
import tab from '../components/tab/tab.vue'
import tabItem from '../components/tab/tab-item.vue'
import payCell from '../components/pay-cell.vue'
import axios from 'axios';

export default{
	data(){
		return{
			payData:[
				{}
			],
			payCount:{
				allCount:0,
				pendingCount:0,
				alreadyCount:0,
				invalidCount:0
			},
			index:1,
			leiji:''
		}
	},
	created(){
		 this.$store.commit('footer',false);
		status=-1;
		this.gettixianlist();
	},
	components:{
		shopHeader,
		tabItem,
		tab,
		payCell
		
	},
	methods:{
		gettixianlist(){
			var that = this;
			axios.post(urlstr + 'qmjjr.gettixianlist&status=' + status).then(function (res) {
				if (res.data.code == 3) {
					that.leiji = res.data.leiji;
					that.payData=res.data.data;
				}
			});
		}
		,
	    onIndexChange(val){
	    	this.index=val
	    }

	},
	computed:{
		isHave(){
			 return this.payData.length>0 ? false : true 
		}
	},
	watch:{
		index(val){
			status =val-2;
			this.gettixianlist();
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

.home-nav{
	margin-top: 0px;
	background: red;
	position: relative;
	z-index: 9
}
.goods-wrap{
	margin-top: 120px;
	height: 504px;
	background: #fff;
	padding:  0;	
}
.card{
	margin: 44px 0 0;
	.card-header-text{
		color: @color-primary
	}
}

</style>