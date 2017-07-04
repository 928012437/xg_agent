<template>
 <div class="home-cell-item clearfix" :class="{'border':show}">
	<div class="pull-left">
		<slot name='icon'></slot>
	</div>
	<div class="pull-left home-handle">
		<div>
			<slot name='title'></slot>
		</div>
		<div class="result">
			<slot name='result'></slot>
		</div>
	</div>
  

</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:{
		show:{
			type:Boolean,
			default:false
		}
	},
	data(){
		return{

		}
	}
	
}

</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';

.home-cell-item{
	background: #fff;
	padding: 5px 15px;
	color:@color-text;
	font-size: 14px;
	.home-handle{
		margin-left: 10px;
	}
	a{
		color:@color-text;
	}
}

.border{
	border-right:1px solid @color-split;
}
.result{
	font-size: 12px;
	color: @color-text-gray;
	
}
</style>