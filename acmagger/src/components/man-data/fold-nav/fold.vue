<template>
<div>
	
<div class="card-content" >
	 <ul style="margin:0">
	    <slot></slot>
	 </ul> 
</div>  	    

	        
	        
 </div>
</template>

<script>
import { parentMixin } from './multi-items.js'
export default{
	mixins: [parentMixin],
	props:{
		
	},
	mounted () {
	    // stop bar anmination on first loading
	    setTimeout(() => {
	      this.hasReady = true
	    }, 0)
	  }
 }
</script>
<style lang='less' scoped>
@import '../../../common/style/mixins.less';
	.card{
		margin:0;
	  	 margin-top: 48px;
	   	 position: relative;
	   	 .card-header{
	   	 	color:@color-primary;
	   	 }
	   
	    .card-content{
	      position: absolute;
	      width: 100%;
	      background: rgba(255,255,255,1);
	      z-index: 999;    
	      }
	    }
	
</style>