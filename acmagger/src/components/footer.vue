<template>
	<div>
		 
		<nav class="bar bar-tab man-nav" v-show='commConf.isFooter'>
	
		  <router-link class="tab-item external" to="/static/data">
		    <span class=" iconfont">&#xe615;</span>
		    <span class="tab-label">案场统计</span>
		  </router-link>
		  <router-link class="tab-item external"  to="/customer">
		    <span class="iconfont">&#xe615;</span>
		    <span class="tab-label">客户</span>
		    <span class="badge">2</span>
		  </router-link>
		  <router-link class="tab-item external" to="/house">
		    <span class="iconfont">&#xe615;</span>
		    <span class="tab-label">房源</span>
		  </router-link>
		  <router-link class="tab-item external" to="/check">
		    <span class="icon icon-cart"></span>
		    <span class="tab-label">我</span>
		  </router-link>		 
		</nav>
	</div>
</template>
<script>
import {mapGetters,mapActions} from 'vuex';

export default{
	computed:{
	...mapGetters([
      'commConf',
     
      // ...
    ])
   }
}
</script>
<style>


	.tab-label{
		display: block;
		font-size: 14px;
	}
	.man-nav{
		z-index: 999
	}

</style>