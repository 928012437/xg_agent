<template>
 <div class="content">
 	<top></top>
	<div class="content margin-top">
		<div class="card" v-for='(ask,index) in asks'>
		   <div class="card-header" @click='count=index'>
		   		<div><span class="icon icon-caret" :class="{'icon-caret-on':count===index}"></span>{{ask.title}}</div>
		   </div>
		   <div class="card-content">
		   <transition name='fade'>
		     <div class="card-content-inner" v-show='count===index'>
		     	{{ask.text}}
		     </div>
		     </transition>
		   </div>
		   
		 </div>
	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
export default{
	components:{
		Top,
	},
	data(){
		return{
			ishow:false,
			count:0,
			asks:[
				{
					title:'我离开了南京,再没有人跟我说话',
					text:'我离开了南京,再没有人跟我说话我离开了南京,再没有人跟我说话我离开了南京,再没有人跟我说话我离开了南京,再没有人跟我说话我离开了南京,再没有人跟我说话'
				},
				{
					title:'你等着我回来',
					text:'爱人你可知道明天就要来临，码头上停着我们的船'
				}
			]
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.card{
	box-shadow: none;
	.card-header{
		padding: 0.2rem 0.5rem;
		font-size: 14px;
		min-height: 1.8rem;
		color: @color-text-secondary;
		.icon-caret{
			margin-right: 4px;
			transform: rotate(-90deg);
			margin-top: -4px;
			color: @color-primary;
			transition: transform 0.3s;
		}
		.icon-caret-on{
			transform: rotate(0deg);
		}
	}
}
.card-content-inner{
	color: @color-text-gray
}
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s
}
.fade-enter, .fade-leave-active {
  opacity: 0
}

</style>