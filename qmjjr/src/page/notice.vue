<template>
 <div class="content">
	<top></top>
	<div class="content margin-top notice-content" v-html="data" >

	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import axios from 'axios';

export default{
	data(){
		return {
			data: ''
		}
	},
	components:{
		Top
	},
	created(){
		 this.$store.commit('footer',false);
		this.getxuzhi()
	},
	methods:{
		getxuzhi(){
			var that = this;
			axios.post(urlstr + 'qmjjr.getxuzhi').then(function (res) {
				if (res.data.code == 3) {
					that.data=res.data.data;
				}
			});
		}
	}
	
}
</script>

<style lang='less' scoped>
.notice-content{
	padding: 15px;
}

</style>