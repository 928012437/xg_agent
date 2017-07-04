<template>
 <div>
	<div class="list-block">
   <slot></slot>

  </div>
</div>
</template>
<script type='text/ecmascript-6'>

export default{
	
}

</script>

<style lang='less' scoped>
.list-block {
	margin: 0px auto 8px;
	.item-content{
		font-size: 14px;
		input{
			font-size: 14px;
		}
	}
}

</style>
