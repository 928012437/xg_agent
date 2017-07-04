<template>
 <div>
	<lines>
		<div slot='crumb'>转介</div> 
		<line-list slot='content' :followData="pathData" ></line-list>
	</lines>


</div>
</template>
<script type='text/ecmascript-6'>
import Lines from './line.vue'
import lineList from './line-list.vue'

export default{
	props:['pathData'],
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