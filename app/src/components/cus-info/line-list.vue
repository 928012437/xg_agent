<template>
 <div>
	<ul class="time-line">
      	 	<li class="time-line-list " :class="{'newly':index==0}" v-for='(item,index) in followData'>
      	 		<div class="line-header">
      	 			{{item.time}}
      	 		</div>
      	 		<div class="line-content">
      	 			<div>
      	 				{{item.active}}
      	 			</div>
      	 			<div class="line-cotent-i clearfix">
      	 				<div class="line-content-before pull-left">
      	 				{{item.content}}
	      	 			</div>
	      	 			<div class="line-content-after pull-right">
	      	 				{{item.xiaci}}
	      	 			</div>
      	 			</div>
      	 			
      	 		</div>
      	 		
      	 	</li>
      	 </ul>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:['followData'],
}

</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';
.time-line{
	padding: 10px 15px 15px;
	.time-line-list{
		border-left:1px solid @color-split;
		padding: 18px 0 10px 15px;
		position: relative;
		overflow: hidden;


		.line-content{
			margin-top: 5px;
			background:lighten(@color-text-gray-light,10%) ;
			padding: 4px 8px;
			font-size: 13px;
			color: @color-text-secondary
		}
		&:after{
			content: '';
			position: absolute;
			width: 18px;
			height: 18px;
			
			background-color: lighten(@color-text-gray-light,10%);
			border-radius: 50%;
			left:-9px;
			top:0;
		};
	}
	.newly:after{
		background-color: @color-primary!important;
	}
}

</style>