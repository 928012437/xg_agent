<template>
<div>
	<div class="card">
	    <div class="card-header" >
		    <div class="" @click='ishow=!ishow'><i class="iconfont" >&#xe639;</i>
		    <span>
		   {{selectedValue.value}}</span> </div> 
		    <div class="filter-icon">
		    	<!-- <solt name='filterIcon'></solt>	 -->
		    </div>
	    </div>
	    <fold v-show='ishow'>
	    	<fold-item @click.native="selectedValue.value='逾期未跟进'">逾期未跟进</fold-item>
	    	<fold-item @click.native="selectedValue.value='逾期未认购'" >逾期未认购</fold-item>
	    	<fold-item @click.native="selectedValue.value='逾期未签约'" >逾期未签约</fold-item>
	    	<fold-item @click.native="selectedValue.value='逾期未交款'" >逾期未交款</fold-item>
	    </fold>  
	</div>
	        
	        
 </div>
</template>
<script>
import Fold from './fold.vue'
import FoldItem from './fold-item.vue'

export default{
	data(){
	    return{
	      ishow:false,
	      
	    }
  },
  props:['selectedValue'],
  components:{
  	Fold,
  	FoldItem
  },
  computed:{
  		
  },
 
  watch:{
   'selectedValue.value'(val){
   		this.ishow=!this.ishow
   	}
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
	   	 .filter-icon{
	   	 	border-left: 1px solid @color-split;
	   	 	padding-left: 15px
	   	 }  
	   	 @media only screen and (-webkit-min-device-pixel-ratio: 2){
	   	 	.card-header:after{
	   	 		transform: scaleY(1);
	   	 	}
	   	 }

	   
	}
</style>