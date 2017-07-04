<template>
 <div class="content">
 	<top></top>
	<div class="content">
		<card-info></card-info>	
	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import CardInfo from '../components/cardInfo.vue'
export default{
	components:{
		Top,
		CardInfo
	}
}

</script>

<style lang='less' scoped>


</style>