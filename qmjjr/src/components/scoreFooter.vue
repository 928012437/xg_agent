<template>
	<div>
		<nav class="bar bar-tab shop-nav"  >
		  <router-link class="tab-item external" to="/addons/xg_agent/qmjjr/sign">
		    <span class=" iconfont">&#xe615;</span>
		    <span class="tab-label">积分签到</span>
		  </router-link>
		  <router-link class="tab-item external"  to="/addons/xg_agent/qmjjrsign/singtop">
		    <span class="iconfont">&#xe615;</span>
		    <span class="tab-label">签到排行</span>
		    <span class="badge">2</span>
		  </router-link>
		  <router-link class="tab-item external" to="/addons/xg_agent/qmjjr/index">
		    <span class="iconfont">&#xe615;</span>
		    <span class="tab-label">积分商城</span>
		  </router-link>
		  <router-link class="tab-item external" to="">
		    <span class="icon icon-cart"></span>
		    <span class="tab-label">我的</span>
		  </router-link>		 
		</nav>
	</div>
</template>
<script>
import {mapGetters} from 'vuex';

export default{
	created(){
		 this.$store.commit('footer',false);
	},
	computed:{
		...mapGetters([
		      'footer',
		      
		      // ...
   			 ]),

		
	}
}
</script>
<style  lang='less'>
	.tab-label{
		display: block;
		font-size: 14px;
	}
	.shop-nav{
		z-index: 999
	}

</style>