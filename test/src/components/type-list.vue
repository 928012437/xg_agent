<template>
<div>

 <div class="row type-list" v-for='goods in goodsData'>
 
	<div class="col-50">
	<router-link :to="'/index/goodsinfo/'+id+'/housetype/'+goods.id">
		<div class="type-img">
			<img src="../img/pic.jpg" alt="">
		</div>	
		</router-link>
	</div>
	<div class="col-50">
		<ul class="info-list">
			<li>{{goods.type}}</li>
			<li>{{goods.rooms}}</li>
			<li>{{goods.area}}㎡</li>
			<li>
				<span class="type-tag" v-for='tag in goods.tags'>{{tag}}</span>
				
			</li>
		</ul>
	</div>
	
</div>
</div>
</template>
<script type='text/ecmascript-6'>


export default{
	props:['id'],
	created(){
		 this.$store.commit('footer',false);
	},
	data(){
		return{
			goodsData:[
				{	
					type:'A3',
					rooms:'三室两厅',
					area:1594,
					tags:['牛逼','错','乐意'],
					id:6
				},
				{	
					type:'A1',
					rooms:'三室yi厅',
					area:100,
					tags:['牛逼','错'],
					id:8
				}

			]
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