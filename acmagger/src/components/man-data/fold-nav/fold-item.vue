<template>
 <div>
	<li class="list" :class="[selected ? activeClass:'']" @click='onItemClick'> 
		<slot></slot>	
	</li>


</div>
</template>
<script type='text/ecmascript-6'>
import { childMixin } from './multi-items.js'
export default{
	 mixins: [childMixin],
	 data(){
	 	return{
	 		activeClass:'activeClass'
	 	}
	 }
}

</script>

<style lang='less' scoped>
@import '../../../common/style/mixins.less';
	    .card-content{
	      
	      .list{
		        padding: 10px 20px;
		        overflow: hidden;
		        position: relative;
		        width: 100%;
		        border-bottom: 1px solid @color-split;	       
	      }
		.activeClass{
			background: Lighten(@color-text-gray-light,10%);
			}
	    }
	
</style>