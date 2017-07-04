<template>
 <div>
	<lines>
		<div slot='crumb'>日志</div> 
		<line-list slot='content'></line-list>
	</lines>


</div>
</template>
<script type='text/ecmascript-6'>
import Lines from './line.vue'
import lineList from './line-list.vue'

export default{
	components:{
		Lines,
		lineList
	}
	
}

</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';



</style>