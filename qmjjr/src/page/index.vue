<template>
 <div class="content">
<index-header></index-header>
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
	import axios from 'axios';
	import zepto from 'n-zepto'

	export default{
		components: {
			shopHeader,
			goodsList,
			swiper,
			indexHeader
		},
		created(){
			this.$store.commit('footer', true);
			this.$store.commit('loading',true);
			this.getadv();
		},
		data(){
			return {
				banner: [{
					url: '',
					img: '',
					title: ''
				}],
				index: 1
			}
		},
		methods: {
			onIndexChange(val){
				this.index = val;
			},
			getadv(){
				var that = this;
				axios.post(urlstr + 'qmjjr.opensource.getadv').then(function (res) {
					if (res.data.code == 3) {
						that.banner = res.data.data;
						that.$store.commit('loading',false);
					}
				});
			},
		}
	}

</script>

<style lang='less' scoped>
.good-list{
	margin-top: 320px;
	margin-bottom: 60px;
	background: #fff;
}
.goods-con{

	height: auto
}
</style>