<template>
<div>

 <div class="row type-list" v-for='goods in goodsData'>
 
	<div class="col-50">
	<router-link :to="'/addons/xg_agent/qmjjr/index/goodsinfo/'+id+'/housetype/'+goods.id">
		<div class="type-img">
			<img :src="goods.l_url" alt="">
		</div>	
		</router-link>
	</div>
	<div class="col-50">
		<ul class="info-list">
			<li>{{goods.l_name}}</li>
			<li>{{goods.l_shi}}室{{goods.l_ting}}厅{{goods.l_wei}}卫</li>
			<li>{{goods.l_pingmi}}㎡</li>
			<li>
				<span class="type-tag" v-for='tag in goods.l_biaoqian'>{{tag}}</span>
				
			</li>
		</ul>
	</div>
	
</div>
</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:['id','goodsData'],
	created(){
		 this.$store.commit('footer',false);
	},
	data(){
		return{
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.type-list{
	background-color: #fff;
	margin-bottom: 5px;
}
.info-list{
	font-size: 14px;
	padding: 2px;
	color: @color-text-gray;
	li{
		padding: 4px 8px;
		&+li{
		border-top: 1px solid @color-split;
		}
	}
	.type-tag{
		float: left;
		margin-top: 2px;
		margin-right: 4px;
		border: 1px solid @color-primary;
		border-radius: 4px;
		padding: 0 2px;
		color:@color-primary 
	}
	
}
.type-img{
	height: 180px;
	width: 100%;
	overflow: hidden;
	padding: 5px;
	img{
		display: block;
		width: 100%;
		height: 100%;
	}
}

</style>