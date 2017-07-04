<template>
	<div>
		<nav class="bar bar-tab shop-nav" v-show='footer' >
		  <router-link class="tab-item external" to="/index">
		    <span class=" iconfont">&#xe63d;</span>
		    <span class="tab-label">首页</span>
		  </router-link>
		  <router-link class="tab-item external" to="/customer">
		    <span class="iconfont">&#xe64e;</span>
		    <span class="tab-label">客户</span>
		  </router-link>
		  <router-link class="tab-item external" to="/home">
		    <span class="iconfont">&#xe610;</span>
		    <span class="tab-label">个人中心</span>
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
<style  lang='less'>
	
.shop-nav{
		z-index: 999
}
.shop-nav span.tab-label {
    display: block;
    font-size: 0.55rem;
    margin-top: -4px;
}
</style>