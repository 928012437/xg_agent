<template>
 <div class="content">
	<sale-nav></sale-nav>
	<router-view></router-view>
 
</div>
</template>
<script type='text/ecmascript-6'>
import saleNav from '../components/nav.vue'
/*import goodsList from '../components/goodsList.vue'
import swiper from '../components/swiper/index.vue'*/




export default{
	components:{
		saleNav,
		
	},
	created(){
		 this.$store.commit('footer',true);
		 if(this.$route.name=='handle'){
		 	this.$router.push({ path: 'handle/remind' })
		 }
	},
	data(){
		return{
			
		}
	},
	methods:{
	   
	}
}

</script>

<style lang='less' scoped>
.goodsTip,.strip{
	background: #fff;
	padding: 8px 0;
	height: 2rem;
}
.strip{
	text-align: center;
	div:first-child{
		border-right:1px solid #e7e7e7;
	}
}
.goodsTip{
	margin-top: 20px;
	border-bottom: 1px solid #e7e7e7;
	padding-left: 15px;
	/*margin-bottom: 10px;*/
}
.goods-con{

	height: auto
}
</style>