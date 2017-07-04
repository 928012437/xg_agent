<template>
 <div>
	<top><span class="pull-right more " @click='show=!show'>
	<i class="iconfont">&#xe67e;</i><!-- 简介 --></span></top>
	<div class="img-wrap">
		<img :src="goods.l_url" alt="" style="width:100%">
	</div>
	<popup v-model="show" height='340px'>
		<span class="brief-title bar-nav bar ">简介</span>
		<div class="list-block content">
		    <ul>
				<li class="item-content">
					<div class="item-inner">
						<div class="item-title">名称</div>
						<div class="item-after">{{goods.l_name}}</div>
					</div>
				</li>
		      <li class="item-content">
				<div class="item-inner">
					<div class="item-title">平米数</div>
					<div class="item-after">{{goods.l_pingmi}}㎡</div>
				</div>
			</li>
				<li class="item-content">
					<div class="item-inner">
						<div class="item-title">户型</div>
						<div class="item-after">{{goods.l_shi}}室{{goods.l_ting}}厅{{goods.l_wei}}卫</div>
					</div>
				</li>
				<li class="item-content">
					<div class="item-inner">
						<div class="item-title">正常报价</div>
						<div class="item-after">{{goods.bj_zc_type}}{{goods.bj_zc}}元/m²</div>
					</div>
				</li>
				<li class="item-content">
					<div class="item-inner">
						<div class="item-title">底价</div>
						<div class="item-after">{{goods.bj_dj_type}}{{goods.bj_dj}}元/m²</div>
					</div>
				</li>
				<li class="item-content">
					<div class="item-inner">
						<div class="item-title">简介</div>
						<div class="item-after">{{goods.l_content}}</div>
					</div>
				</li>
		      <li class="item-content">
		        <div class="item-inner">
		          <div class="item-title">
		          	<span class="type-tag"  v-for="v in goods.l_biaoqian" >{{v}}</span>
		          </div>
		          
		        </div>
		      </li>
		    </ul>
		</div>
	</popup>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
import Popup from '../components/popup/index.vue';
import axios from 'axios';

export default{
	components:{
		Top,
		Popup
	},
	data(){
		return{
			show:false,
			goods:''
		}
	},
	created(){
	  this.$store.commit('footer',false);
		var id=this.$route.params.typeid;
		this.gethouselayer(id)
	},
	methods:{
		gethouselayer(id){
			var that=this;
			axios.post(urlstr + 'qmjjr.gethouselayer&id='+id).then(function (res) {
				if (res.data.code == 3) {
					that.goods = res.data.data;
				}
			});
		}
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