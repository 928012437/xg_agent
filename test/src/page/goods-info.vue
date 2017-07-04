<template>
 <div class="content margin-bottom">
 <top></top>
 <div class="content goods-content margin-top">
<swiper :list="banner"  :index='index'  v-show='navNum==0'
 height='240px'></swiper>

<div class="vr" v-show='navNum==1'>
<iframe src="" frameborder="0">
		
</iframe>
</div>


<!-- <swiper :list="banner"  :index='index'  style="width:100%;height:100%" height='240px'></swiper> -->


 <ul class="goods-nav">
 	<li @click='navNum=1'>
 		<i class="iconfont">&#xe682;</i>
 		虚拟样板间
 	</li>
 	<li @click='navNum=0'>
 		<i class="iconfont">&#xe67f;</i>
 		楼盘相册
 	</li>
 	<li>
 		<a href="#type">
	 		<i class="iconfont">&#xe683;</i>
	 		户型图
 		</a>
 	</li>
 </ul>
<div class="card goods">
    <div class="card-header">产品一号</div>
    <div class="card-content">
      <div class="card-content-inner">
			<div class="list-block">
			  <ul>
			    <li class="item-content">
			      <div class="item-media"><i class="iconfont main-color">&#xe63e;</i></div>
			      <div class="item-inner">
			        <div class="item-title">均价：<span class=''>dsfd</span></div>
			       
			      </div>
			    </li>
			    <li class="item-content">
			      <div class="item-media"><i class="iconfont main-color">&#xe67d;</i></div>
			      <div class="item-inner">
			        <div class="item-title">地址：<span class=''>dsfd</span></div>
			      </div>
			    </li>
			    <li class="item-content">
			      <div class="item-media"><i class="iconfont main-color">&#xe607;</i></div>
			      <div class="item-inner">
			        <div class="item-title">电话：<span class=''>dsfd</span></div>
			      </div>
			    </li>
			  </ul>
      </div>
    </div>
  </div>
  </div>

  <div class="card goods-customer">
    <div class="card-header">
    我的客户
    </div>
    <div class="card-content">
      <div class="card-content-inner">
			<div class="list-block">
			  <ul>
					   <li class="item-content">
		        <div class="item-inner">
		          <div class="item-title">已推荐客户</div>
		          <div class="item-after">1000 人</div>
		        </div>
		      </li>
		      <li class="item-content">
		        <div class="item-inner">
		          <div class="item-title">合作规则</div>
		          <div class="item-after" :class="{'openRule':ruleShow}"
		          @click='ruleShow=!ruleShow'> <span class="icon icon-down"></span></div>
       		 	</div>
     		 </li>
     		  <li class="item-content" v-show='ruleShow'>
		        <div class="item-inner rule-content">
		          合作规则sfsfsfsf 合作规则sfsfsfsf 合作规则sfsfsfsf 合作规则sfsfsfsf 合作规则sfsfsfsf 合作规则sfsfsfsf
       		 	</div>
     		 </li>
     		 <li class="item-content">
		        <div class="item-inner rule-content">
		          	<button class="button button-big recommend-btn"
		          	@click="$router.push('/customer/recommend')">推荐客户</button>
       		 	</div>
     		 </li>
			  </ul>
      </div>
    </div>
  </div>
  </div>

  <div class="card goods-customer">
    <div class="card-header">
		进一步了解
    </div>
    <div class="card-content">
	 <Cell>
		<cellItem :show='true' @click.native="$router.push('/home/pay')">
			<span class="info-title" slot='icon'>开盘时间:</span>
			<span slot='title' class="info-content">我的佣金</span>
		</cellItem>
		<cellItem @click.native="$router.push('/home/withdraw')">
			<span class="info-title" slot='icon'>建筑面积:</span>
			<span slot='title' class="info-content">提现</span>
		</cellItem>
	</Cell>
	<Cell>
		<cellItem :show='true' @click.native="$router.push('/home/pay')">
			<span class="info-title" slot='icon'>开盘时间:</span>
			<span slot='title' class="info-content">我的佣金</span>
		</cellItem>
		<cellItem @click.native="$router.push('/home/withdraw')">
			<span class="info-title" slot='icon'>建筑面积:</span>
			<span slot='title' class="info-content">提现</span>
		</cellItem>
	</Cell>
    </div>
  </div>
  <div class="more-wraper">
  	<divider><a name='type' class="text-color">更多户型</a></divider>
   </div>
 	
   <typeList :id='$route.params.id'></typeList>
 </div> 
</div>
</template>
<script type='text/ecmascript-6'>
import swiper from '../components/swiper/index.vue'
import Cell from '../components/home-cell/home-cell.vue'
import cellItem from '../components/home-cell/cell-item.vue'
import typeList from '../components/type-list.vue'
import divider from '../components/divider/index.vue'
import Top from '../components/header.vue';
const baseList =
[{
  url: 'www.baidu.com',
  img: '//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg',
  title: '如何手制一份秋意的茶？'
}, {
  url: 'javascript:',
  img: '//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg',
  title: '茶包VS原叶茶'
}, {
  url: 'javascript:',
  img: 'http://7xqzw4.com2.z0.glb.qiniucdn.com/3.jpg',
  title: '播下茶籽，明春可发芽？'
}]

export default{
	components:{
		swiper,
		cellItem,
		Cell,
		typeList,
		divider,
		Top

	},
	data(){
		return{
			index:1,
			navNum:0,
			banner: baseList,
			ruleShow:false
		}
	},
	created(){
		//参数id
		var param = this.$route.params.id
	},
	methods:{
		getData(){

		},
		 onIndexChange(val){
	    	this.index=val;
	    	alert(val)
	    }
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.icon{
		transform: rotate(90deg);
		transition: transform 0.3s;
}
.openRule{
	.icon{
		transform: rotate(0deg);
	}
}
.rule-content{
	padding: 20px 10px;
	color: @color-text-gray
}
.vr,.photos{
	width: 100%;
	height: 240px;
	overflow: hidden;
	background: #eee;
}
.vr>iframe{
	display: block;
	width: 100%;
	height: 100%;
}

.goods-nav{
	.flexbox;
	background-color: #fff;
	font-size: 14px;
	padding: 8px 0;
	.iconfont{
		display: block;
	}
	li{	
		color: @color-primary;
		padding: 2px 0;
		text-align: center;
		.flex(1);
		&+li{
			border-left: 1px solid @color-split;
		}
	}
}
.card{
	margin: 0 0 10px 0;
	box-shadow: none;
	.item-content{
		font-size: 14px;
	}
	.item-title span{
		color: @color-text-gray;
	}
}
.info-title{
	color: @color-text-gray;
}
/* .info-content{
	color:Lighten(@color-text-secondary,8%);
} */
.recommend-btn{
	padding: 0 2.2rem;
	margin: 0 auto;
	background-color: @color-primary;
	color: #fff;
	height: 2rem;
	line-height: 2rem;
}
.more-wraper{
	background-color: #fff;
}
p{
	margin: 0;
	padding: 20px 0;
	margin-bottom: 5px;
}
</style>