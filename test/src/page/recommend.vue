<template>
 <div class="content">
	<top></top>
	<div class="content margin-top">
		<form action="">
			<edit></edit>
		</form>
	</div>
	<button type="button" class="bar bar-tab bottom-btn">
		保存
	</button>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
import Edit from '../components/edit.vue';
export default{
	components:{
		Top,
		Edit
		
	},
}

</script>

<style lang='less' scoped>


</style>