<template>
<div class="content">
	<top></top>
	<div class="list-block content margin-top ">
	    <ul>
	      <li class="item-content item-link">
	    
	        <div class="item-media"><i class="iconfont deepGreen">&#xe610;</i></div>
		         
		        <div class="item-inner">
 				<router-link to='/addons/xg_agent/app/home/mycard' >
	          <div class="item-title">我的名片</div>
	          	 </router-link>
	        </div>

	        
	      </li>
	      <li class="item-content item-link">
	        <div class="item-media"><i class="iconfont man-ele-on">&#xe60a;</i></div>
	        <div class="item-inner">
	        <router-link to='/addons/xg_agent/app/home/calculator' >
	          <div class="item-title">房贷计算器</div>
	           </router-link>
	        </div>
	      </li>
	    </ul>
	 </div>
	


</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
export default{
	components:{
		Top
	},
	created(){
		 this.$store.commit('footer',false);
	},

}

</script>

<style lang='less' scoped>
.item-content{
	font-size: 14px;
}
.man-ele-on{
    background: #0894ec !important;
    color: #fff !important
}
.deepGreen {
    background: #3AB663!important;
    color: #fff!important
}

 .list-block .item-media i {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    text-align: center;
    line-height: 36px;
    color: #fff;
    margin-top: 2px;
    font-size: 24px;
}
.item-inner{
	width: 90%;
	text-align: left;
	a{
		display: inline-block;
		width: 100%;
	}
}
</style>