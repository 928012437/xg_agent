<template>
 <div>
	<lines>
		<div slot='crumb'>未成交意向</div> 
		<cell slot='content' showValue='intention' :message='loupan'></cell>
		
	</lines>


</div>
</template>
<script type='text/ecmascript-6'>
import Lines from './line.vue'

import Cell from './cell.vue'

export default{
	props:['loupan'],
	components:{
		Lines,
		Cell
	},
}

</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';



</style>