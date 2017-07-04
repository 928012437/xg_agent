<template>
 <div>
	<ul class="time-line">
      	 	<li class="time-line-list " :class="{'newly':index==0}" v-for='(item,index) in logData'>
      	 		<div class="line-header">
      	 			{{item.createtime}}
      	 		</div>
      	 		<div class="line-content">
      	 			<div>
      	 				{{item.text}}
      	 			</div>
      	 			
      	 		</div>
      	 	</li>
      	 </ul>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:['logData'],
	data(){
		return{

		}
	}
	,
	created(){

		console.log(this.logData);
	}
}

</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';
.time-line{
	padding: 10px 15px 15px;
	.time-line-list{
		border-left:1px solid @color-split;
		padding: 0px 0 20px 15px;
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