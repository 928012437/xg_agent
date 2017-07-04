<template>
    <div class="content">
		<cus-header></cus-header>
		
		<div class="cus-content" >
		 <transition  :name='transitionName'>
   >
			<router-view></router-view>
		 </transition>
		</div>
    </div>
</template>
<script type='text/ecmascript-6'>
import cusHeader from '../components/man-customer/header.vue';

import cusAssign from '../components/man-customer/cus-assign.vue'
export default{
	data(){
		return{
			transitionName:'slide-right'
		}
		
	},
	created(){
		this.$store.commit('COMM_CONF',{
                isFooter:true, 
            });
	},
	components:{
		cusHeader,
		cusAssign,

	},
	watch: {
	  '$route' (to, from) {
	    const toDepth = to.path.split('/').length
	    const fromDepth = from.path.split('/').length
	     this.transitionName=to.path=='/customer/analysis'? 'slide-right':'slide-left'
	  /*  this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'*/
	  }
	}
}

</script>

<style lang='less' scoped>
.cus-content{
	margin-top: 44px;
}


.slide-right-enter-active {
  animation: fadeInRight 0.1s ease;
}


.slide-left-enter-active{
  animation: fadeInLeft 0.2s ease;
}



/* .slide-right-fade-enter {
  transform: translateX(10px);
  opacity: 0;
}
 */

</style>