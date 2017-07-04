<template>
 <div class="content">
	<top></top>
	<div class="content margin-top notice-content">
		
	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
export default{
	components:{
		Top
	},
	created(){
		 this.$store.commit('footer',false);
	},
	
}
</script>

<style lang='less' scoped>
.notice-content{
	padding: 15px;
}

</style>