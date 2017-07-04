<template>
 <div class="content margin-bottom">
 <top></top>
 <div class="content goods-content margin-top">
<swiper :list="banner"  :index='index'  v-show='navNum==0'
 height='240px'></swiper>

<div class="vr" v-show='navNum==1'>
<iframe :src="data.frame" frameborder="0">
		
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
    <div class="card-header">{{data.title}}</div>
    <div class="card-content">
      <div class="card-content-inner">
			<div class="list-block">
			  <ul>
			    <li class="item-content">
			      <div class="item-media"><i class="iconfont main-color">&#xe63e;</i></div>
			      <div class="item-inner">
			        <div class="item-title">均价：<span class=''>{{data.price}}</span></div>
			       
			      </div>
			    </li>
			    <li class="item-content">
			      <div class="item-media"><i class="iconfont main-color">&#xe67d;</i></div>
			      <div class="item-inner">
			        <div class="item-title">地址：<span class=''>{{data.addr}}</span></div>
			      </div>
			    </li>
			    <li class="item-content">
			      <div class="item-media"><i class="iconfont main-color">&#xe607;</i></div>
			      <div class="item-inner">
			        <div class="item-title">电话：<span class=''>{{data.tel}}</span></div>
			      </div>
			    </li>
			  </ul>
      </div>
    </div>
  </div>
  </div>

  <div class="card goods-customer">
    <div class="card-header">
    关于客户
    </div>
    <div class="card-content">
      <div class="card-content-inner">
			<div class="list-block">
			  <ul>
					   <li class="item-content">
		        <div class="item-inner">
		          <div class="item-title">已推荐客户</div>
		          <div class="item-after">{{data.recnum}} 人</div>
		        </div>
		      </li>
				  <!--<li class="item-content">-->
					  <!--<div class="item-inner">-->
						  <!--<div class="item-title">已成交客户</div>-->
						  <!--<div class="item-after">人</div>-->
					  <!--</div>-->
				  <!--</li>-->
		      <li class="item-content">
		        <div class="item-inner">
		          <div class="item-title">合作规则</div>
		          <div class="item-after" :class="{'openRule':ruleShow}"
		          @click='ruleShow=!ruleShow'> <span class="icon icon-down"></span></div>
       		 	</div>
     		 </li>
     		  <li class="item-content" v-show='ruleShow'>
		        <div class="item-inner rule-content"  v-html="data.l_guize" >
       		 	</div>
     		 </li>
     		 <li class="item-content">
		        <div class="item-inner rule-content">
		          	<button class="button button-big recommend-btn"
		          	@click="$router.push('/addons/xg_agent/qmjjr/customer/recommend')">推荐客户</button>
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
		<cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/pay')">
			<span class="info-title" slot='icon'>开盘时间:</span>
			<span slot='title' class="info-content">{{data.starttime}}</span>
		</cellItem>
		<cellItem @click.native="$router.push('/addons/xg_agent/qmjjr/home/withdraw')">
			<span class="info-title" slot='icon'>交房日期:</span>
			<span slot='title' class="info-content">{{data.endtime}}</span>
		</cellItem>
	</Cell>
	<Cell>
		<cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/pay')">
			<span class="info-title" slot='icon'>建筑面积:</span>
			<span slot='title' class="info-content">{{data.l_mianji}}</span>
		</cellItem>
		<cellItem @click.native="$router.push('/addons/xg_agent/qmjjr/home/withdraw')">
			<span class="info-title" slot='icon'>物业类型:</span>
			<span slot='title' class="info-content">{{data.l_wuye}}</span>
		</cellItem>
	</Cell>
		<Cell>
			<cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/pay')">
				<span class="info-title" slot='icon'>交房标准:</span>
				<span slot='title' class="info-content">{{data.l_biaozhun}}</span>
			</cellItem>
			<cellItem @click.native="$router.push('/addons/xg_agent/qmjjr/home/withdraw')">
				<span class="info-title" slot='icon'>规划户数:</span>
				<span slot='title' class="info-content">{{data.l_hushu}}</span>
			</cellItem>
		</Cell>
    </div>
  </div>
  <div class="more-wraper">
  	<divider><a name='type' class="text-color">更多户型</a></divider>
   </div>
 	
   <typeList :id='$route.params.id' :goodsData='houselayer' ></typeList>
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
import axios from 'axios';

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
			banner: [{}],
			ruleShow:false,
			data:[],
			houselayer:[]
		}
	},
	created(){
		//参数id
		var param = this.$route.params.id;
		this.getdetail(param);
	},
	methods:{
		getdetail(param){
			var that = this;
			axios.post(urlstr + 'qmjjr.getloupandetail&id='+param).then(function (res) {
				if (res.data.code == 3) {
					that.data = res.data.data;
					that.banner = res.data.sence;
					that.houselayer = res.data.houselayer;
				}else {
					alert(res.data.message);
					that.$router.push('/addons/xg_agent/qmjjr/index');
				}
			});
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