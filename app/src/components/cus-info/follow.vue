<template>
 <div>
	<lines>
		<div slot='crumb'>跟进</div>
		<div slot='right'>{{num}}</div>
		<line-list slot='content' :followData="followData" ></line-list>
	</lines>


</div>
</template>
<script type='text/ecmascript-6'>
import Lines from './line.vue'
import lineList from './line-list.vue'

export default{
    props:['followData','num'],
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