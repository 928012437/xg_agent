<template>
	<div>
		<nav class="bar bar-tab sale-nav" v-show='footer' style="zIndex:999">
		  <router-link class="tab-item external" to="/addons/xg_agent/app/handle">
		    <span class=" iconfont">&#xe615;</span>
		    <span class="tab-label">待办</span>
		  </router-link>
		  <router-link class="tab-item external"  to="/addons/xg_agent/app/customer">
		    <span class="iconfont">&#xe64e;</span>
		    <span class="tab-label">客户</span>
		   
		  </router-link>
		  <router-link class="tab-item external" to="/addons/xg_agent/app/house/0">
		    <span class="iconfont">&#xe63d;</span>
		    <span class="tab-label">房源</span>
		  </router-link>
		  <router-link class="tab-item external" to="/addons/xg_agent/app/home">
		    <span class="iconfont">&#xe610;</span>
		    <span class="tab-label">我</span>
		  </router-link>		 
		</nav>
	</div>
</template>
<script>
import {mapGetters} from 'vuex';

export default{
	computed:{
		...mapGetters([
		      'footer',
		      
		      // ...
   			 ]),

		
	}
}
</script>
<style  lang='less' scoped>
	.tab-label{
		display: block;
		font-size: 14px;
	}
	.bar{
		z-index: 999
	}

</style>