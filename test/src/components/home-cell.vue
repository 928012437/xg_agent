<template>
	<div class="home-cell">
		<slot></slot>	
	</div>
</template>
<script type='text/ecmascript-6'>

export default{
	
}

</script>

<style lang='less' scoped>
@import  '../../style/less/mixins.less';
@import  '../../style/less/variables.less';
.home-cell{
	.flexbox;
	background: #fff;
	border: 1px solid red;
	div{
		.flex(1)
	}
}

</style>