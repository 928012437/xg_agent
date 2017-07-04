<template>
<div class="content">

	<top></top>
	<div class="content">
		<div class="row ">
			<div class="col-50"><i class="house-tag-icon"></i>可售</div>
			<div class="col-50"><i class="house-tag-icon unsale"></i>未售</div>			
		</div>

	<div class="house-list" v-for='estate in estates'>
	   <h4 class="house-name"><span>{{estate.estateName}}</span></h4>
		<ul class="">
			<li  class="estate"  v-for='area in estate.estateList' :class='{estateOn:area.checked,estateOnHandle:!check}'>
				{{area.name}}
				<input type="checkbox" :disabled='check' v-model='area.checked'  />
				 
				<i class="icon icon-form-checkbox" v-show='!check'>SDF</i>
			</li>
			
		</ul>
	</div>
		
	</div>

<nav class="bar bar-tab house-btn" @click='check=!check'>
	 推盘设置
</nav>

</div>
</template>
<script type='text/ecmascript-6'>

import Top from '../man-customer/top.vue'

export default{
	data(){
		return{
		check:true,
		estates:[
			{
				estateName:'碧湖晓月-一期-东区',
				 estateList:[
				 	{
				 		name:'东区',
				 		checked:false
				 	},
				 	{
				 		name:'东区',
				 		checked:true
				 	}
				 ]
			},
			{
				estateName:'碧湖晓月-一期-东区',
				 estateList:[
				 	{
				 		name:'xi区',
				 		checked:false
				 	},
				 	{
				 		name:'an区',
				 		checked:true
				 	}
				 ]
			},
			{
				estateName:'碧湖晓月-一期-东区',
				 estateList:[
				 	{
				 		name:'xi区',
				 		checked:false
				 	},
				 	{
				 		name:'an区',
				 		checked:true
				 	}
				 ]
			},
			{
				estateName:'碧湖晓月-一期-东区',
				 estateList:[
				 	{
				 		name:'xi区',
				 		checked:false
				 	},
				 	{
				 		name:'an区',
				 		checked:true
				 	}
				 ]
			},
			{
				estateName:'碧湖晓月-一期-东区',
				 estateList:[
				 	{
				 		name:'xi区',
				 		checked:false
				 	},
				 	{
				 		name:'an区',
				 		checked:true
				 	}
				 ]
			}

		]
		}
	},
	components:{
		Top
	}

}

</script>

<style lang='less' scoped>
@import '../../common/style/mixins.less';

.content{
	/*margin-top:44px;*/
	.row{
		margin-top: 10px;
		text-align: center;
		background: #fff;
		padding: 10px;
		margin-top: 0;
	}
	.house-tag-icon{
		display: inline-block;
		width: 15px;
		height: 15px;
		background: @color-primary;
		margin-right: 5px;
	}
	.unsale{
		background: @color-text-gray-light
	}
	.house-list{
		.house-name{
			font-weight: normal;
			border-bottom: 1px solid @color-split;
			padding:0px 10px 8px;
			span{
				border-bottom: 2px solid @color-primary;
				padding-bottom: 8px;
			}
		}
		
		.estate{ 
				 width: 40%;
				 height: 100px;
				 background: @color-text-gray-light; 
				 margin: 10px;
				 text-align: center;
				 line-height: 100px;
				 color: #fff;
				 font-size: 22px;
				 position: relative;
				 display: inline-block;
				 i{
				 	position: absolute;
				 }
				 input{
				 	display: block;
				 	position: absolute;
				 	top: 0;
				 	width: 100%;
				 	height: 100%;
				 	/*opacity: 0;*/
				 }	 
			}
			.estateOn{
				 	background: @color-primary;

				 }
			.estateOnHandle{
				background: #fff;
				color: @color-text
			}

	}
}

</style>