<template>
 <div class="content">
 <div class="indexHeader" v-if='false'>
 	<img src="../img/header.jpg" alt="" >
 </div>
 
<index-header v-else></index-header>
 <swiper :list="banner" @on-index-change='onIndexChange' :index='index' ></swiper>
 	<goodsList></goodsList>

	<div class="spacing"></div>
    
</div>
</template>
<script type='text/ecmascript-6'>
import shopHeader from '../components/header.vue'
import goodsList from '../components/goods-list.vue'
import swiper from '../components/swiper/index.vue'
import indexHeader from '../components/index-header.vue'

const baseList =
[{
  url: '/rigister',
  img: '//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg',
  title: '如何手制一份秋意的茶？'
}, {
  url: '/rigister',
  img: '//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg',
  title: '茶包VS原叶茶'
}, {
  url: '/rigister',
  img: 'http://7xqzw4.com2.z0.glb.qiniucdn.com/3.jpg',
  title: '播下茶籽，明春可发芽？'
}]


export default{
	components:{
		shopHeader,
		goodsList,
		swiper,
		indexHeader
	},
	created(){
		 this.$store.commit('footer',true);
	},
	data(){
		return{
			 banner: baseList,
			 index:1
		}
	},
	methods:{
	    onIndexChange(val){
	    	this.index=val;
	    	
	    }
	}
}

</script>

<style lang='less' scoped>
.indexHeader{
	width: 100%;
	height: auto;
	img{
		display: block;
		width: 100%
	}
}
.good-list{
	margin-top: 320px;
	margin-bottom: 60px;
	background: #fff;
}
.goods-con{
	height: auto
}

</style>