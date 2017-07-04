<template>
 <div class="content">
	<div class="content">
		<top></top>
		<div class="content project-content margin-top">
			<div class="note">提示：选择客户资料需要添加到的项目</div>
			<pro-list></pro-list>

		</div>
	</div>
	提示：选择客户资料需要添加到的项目
</div>
</template>
<script type='text/ecmascript-6'>
import ProList from '../components/project-list.vue'
import Top from '../components/header.vue'

export default{
components:{
	Top,
	ProList
	
}

}
</script>

<style lang='less' scoped>
.note{
	font-size: 14px;
	padding: 5px 15px;
	background: #fff;
	color: #999
}

</style>