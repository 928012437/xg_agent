<template>
 <div>
	<div class="home-cell">
		<slot></slot>	
	</div>


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
	padding: 4px 0;
	background: #fff;
	border-bottom: 1px solid @color-split;
	.flexbox;
	div,a{
		display: block;
		height: auto;
		.flex(1)
	}
}

</style>