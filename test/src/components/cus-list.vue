<template>
<div class="list-block media-list">
<mt-loadmore :bottom-method="loadBottom" :bottom-all-loaded="allLoaded" ref="loadmore" :bottomPullText='statue'  :autoFill=false >
<ul>
  <li class="label-checkbox item-content" v-for='cus in cusData'> 
     <div class="item-inner">
        <div class="item-title-row">
        	
          <div class="item-title">
			<router-link to='/customer/customerinfo'>
          	{{cus.name}}
          	<span class="tel">{{cus.tel}}</span>
          	 </router-link>
          </div>
          <div class="item-after">
         	<router-link to='' class="iconfont ">&#xe607;</router-link>
         	<router-link to='' class="iconfont ">&#xe684;</router-link>
          </div>
         
        </div>
         <div class="item-title-row">
          <div class="item-title">{{cus.area}}</div>
          <div>剩余时间{{cus.day}}天</div>
          <div class="item-after">交易</div>
        </div>
        
      </div>
   
  </li>
  </ul>
  </mt-loadmore>
</div>

</template>
<script type='text/ecmascript-6'>
import Vue from 'vue';
import { Loadmore } from 'mint-ui';
Vue.component(Loadmore.name, Loadmore);

export default{
	data(){
		return{
			
			cusData:[
			{
					name:'程璧',
					tel:158466845,
					area:'我想和你虚度着时光',
					day:23
				},
				{
					name:'程璧',
					tel:158466845,
					area:'我想和你虚度着时光',
					day:23
				},
				
			]
		}
	},
	mounted(){
		this.$refs.loadmore.bottomStatus='pull'
		// alert(this.$refs.loadmore.bottomStatus)
		
	},
	created(){

	},
	computed:{
		statue(){
			var state=''
			if(this.cusData.length==3){
				state='已加载完成'
			}else{
				state='上拉刷新'
			}
			return state
		}
	},
	methods:{
		loadBottom(){
			alert('sdfs');
			if(this.cusData.length>=10){
				alert('sdfs');
				this.allLoaded = true;// if all data are loaded
	  			this.$refs.loadmore.onBottomLoaded();
  			}
		},
		loadData(){

		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.list-block{
	margin-top: 0px;
	margin-bottom: 10px;
}
.item-title-row{
	font-size: 14px;
	color: @color-text-gray;
	&:first-child{
		color:@color-text-secondary;
		margin-bottom: 15px;
		font-size: 16px;
	};
	.item-after{
		color: @color-text-gray;
		.iconfont{
			margin: 0 8px;
		}
	}
}
.item-title{

	.tel{
		display: block;
	}
}


</style>