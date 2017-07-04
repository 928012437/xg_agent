<template>
 <div>
	<top><span class="pull-right more " @click='show=!show'>
	<i class="iconfont">&#xe67e;</i><!-- 简介 --></span></top>
	<div class="img-wrap">
		<img src="" alt="">
	</div>
	<popup v-model="show" height='340px'>
		<div class="list-block">
			<span class="brief-title">简介</span>
		    <ul>
		      <li class="item-content" v-for="">
		        <div class="item-inner">
		          <div class="item-title">商品名称</div>
		          <div class="item-after"></div>
		        </div>
		      </li>
		      <li class="item-content">
		        <div class="item-inner">
		          <div class="item-title">
		          	<span class="type-tag">nib</span>
		          </div>
		          
		        </div>
		      </li>
		    </ul>

	</popup>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
import Popup from '../components/popup/index.vue';
export default{	
	components:{
		Top,
		Popup
	},
	data(){
		return{
			show:false
		}
	},
	created(){
	  this.$store.commit('footer',false);
	},
	methods:{
		
	}
}
</script>

<style lang='less' scoped>
@import '../style/less/variables.less';
.more{
	position: absolute;
	top:10px;
	right: 15px;
	color:@color-primary;
	z-index: 999;
	font-size: 16px;
}
.brief-title{
	margin-left: 15px;
	color:@color-primary;
	
}
.list-block{
	margin: 15px 0;
}
.item-content{
	font-size: 14px;
}

</style>