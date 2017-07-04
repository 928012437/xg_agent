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
		     	{{ask.content}}
		     </div>
		     </transition>
		   </div>
		   
		 </div>
	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import axios from 'axios';

export default{
	components:{
		Top,
	},
	created(){
	this.getquestion()
	},
	methods:{
		getquestion(){
			var that = this;
			axios.post(urlstr + 'qmjjr.getquestion').then(function (res) {
				if (res.data.code == 3) {
					that.asks = res.data.data;
				}
			});
		}
	},
	data(){
		return{
			ishow:false,
			count:0,
			asks:[]
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