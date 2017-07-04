<template>
 <div>
	<lines>
		<div slot='crumb'>详细信息</div>
		<cell slot='content' :message='message' showValue='message' ></cell>
	</lines>


</div>
</template>
<script type='text/ecmascript-6'>
import Lines from './line.vue'
import Cell from './cell.vue'
export default{
	props:['message'],
	components:{
		Lines,
		Cell
	},

}

</script>

<style lang='less' scoped>


</style>