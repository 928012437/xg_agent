<template>
    <div class="content">
	
		<cus-search></cus-search>
		 <div class="list-block">
		    <ul>
		      <router-link to='/customer/assign/overdue' tag="li" class="item-content item-link">
		     
		        <div class="item-media"><i class="icon icon-f7"></i></div>
		        <div class="item-inner">
		          <div class="item-title">逾期客户</div>
		          <div class="item-after">1人</div>
		        </div>
		       
		      </router-link>
		      <router-link to='/customer/assign/share' tag="li" class="item-content item-link">
		      
		        <div class="item-media"><i class="icon icon-f7"></i></div>
		        <div class="item-inner">
		          <div class="item-title">公共客户</div>
		          <div class="item-after">2人</div>
		        </div>
		       
		      </router-link>
		      <router-link to='/customer/assign/invalid' tag="li" class="item-content item-link">
		     
		        <div class="item-media"><i class="icon icon-f7"></i></div>
		        <div class="item-inner">
		          <div class="item-title">无效客户</div>
		          <div class="item-after">4人</div>
		        </router-link>
		       
		      </li>
		      <router-link to='' tag="li" class="item-content item-link">		      
			        <div class="item-media"><i class="icon icon-f7"></i></div>
			        <div class="item-inner">
			          <div class="item-title">跟进中客户</div>
			          <div class="item-after">0人</div>
			        </div>
		        
		      </router-link>
		    </ul>
		  </div>
		  
		  <router-view></router-view>	
    </div>
</template>
<script type='text/ecmascript-6'>
import cusSearch from './search.vue';
export default{
	data(){
		return {

		}
	},
	components:{
		cusSearch,
	}
}

</script>

<style lang='less' scoped>
.list-block {
	margin-top: 90px;
	.item-content{
		padding-left: 0;
	}
}
.slide-right-enter-active {
  animation: fadeInRight 0.5s ease;
}
</style>