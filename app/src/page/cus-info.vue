<template>
 <div class="content">
 	<shop-header></shop-header>
 <div class="content">

	<div class="card cus-info-header">
	    <div class="card-header">
	    	<div class="photo">
	    		<div class="photo-wraper">
	    			<img :src="detail.headurl">
	    		</div>
	    	</div>
	    	<div class="info">
	    	<div class="clearfix">
	    		<span class="pull-left">{{detail.name}} </span>
	    		<span class="tag pull-left">{{detail.statusname}}</span>
	    		<span class="integrity pull-left">完整度{{detail.baifenbi}}%</span>
	    	</div>	
	    		<p class="tel">{{detail.tel}}</p>
	    	</div>
	    	<div class="call">
	    		<router-link to=''></router-link>
	    		<router-link to=''>
					<span class="icon icon-phone"></span>
	    		</router-link>
	    	</div>
	    </div>
	 </div>
	 <tab activeColor='#33CC66' >
	 	<tab-item @click.native="tab='follow'">
	 	跟进记录</tab-item>
	 	<tab-item @click.native="tab='intention'">
	 	购买意向</tab-item>
	 	<tab-item @click.native="tab='message'">
	 	资料信息</tab-item>
	 	<tab-item @click.native="tab='path'">
	 	转介路径
	 	</tab-item>
	 </tab>
	<div class="content cus-content">
		<div class="follow" v-show="tab=='follow'">
			<follow :followData="followData" :num="num" ></follow>
		</div>
		<div class="intention" v-show="tab=='intention'">
			<intention :loupan="loupan" ></intention>
		</div>
		<div class="message" v-show="tab=='message'">
			<message :message="message"></message>
		</div>
		<div class="path" v-show="tab=='path'">
			<paths :pathData="pathData" ></paths>
		</div>
	</div>
</div>
	<nav class="bar bar-tab cus-info-nav sale-nav" >
		  <a class="tab-item " href="#">
    <span class="iconfont">&#xe622;</span>
    <span class="tab-label">置于无效</span>
  </a>

  <router-link :to="'/addons/xg_agent/app/customer/cusinfo/'+$route.params.id+'/edit'" class="tab-item " href="#">
    <span class="iconfont ">&#xe62a;</span>
    <span class="tab-label">编辑资料</span>
  </router-link>

 <router-link  class="tab-item external" :to="'/addons/xg_agent/app/customer/cusinfo/'+$route.params.id+'/addfolow'" >
    <span class="iconfont" style="display:inline-block">&#xe602;</span>
    新增跟进
  </router-link>
		  
	</nav>
</div>
</template>
<script type='text/ecmascript-6'>
import shopHeader from '../components/header.vue'

import tab from '../components/tab/tab.vue'
import tabItem from '../components/tab/tab-item.vue'
import Follow from '../components/cus-info/follow.vue'
import Intention from '../components/cus-info/intention.vue'
import Message from '../components/cus-info/message.vue'
import Paths from '../components/cus-info/path.vue'
import axios from 'axios';

export default{
	components:{
		shopHeader,
		tabItem,
		tab,
		Follow,
		Paths,
		Message,
		Intention
	},
	created(){
		 this.$store.commit('footer',false);
		 this.getdetail()
		 
	},
	data(){
		 return {
  			today:'',
  			tab:'follow',
			 detail:'',
			 followData:[],
			 pathData:[],
			 num:0,
			 loupan:[],
			 message:[]
    	}
	},
	computed:{	
	},
	methods:{
		getdetail(){
			var id=this.$route.params.id;
			var that = this;
			axios.post(urlstr + 'user.counselors.getdetail&id='+id).then(function (res) {
				if (res.data.code == 3) {
					that.detail=res.data.data;
					that.loupan.push(res.data.data.loupan);
					that.followData=res.data.genjinlog;
					that.pathData=res.data.fplog;
					that.message=res.data.message;
					that.num=res.data.num;
				}
			});
		}
	}
	
    
  }


</script>

<style lang='less' scoped>
@import  '../style/less/mixins.less';
@import  '../style/less/variables.less';

.external{
	background: @color-primary;
	color: #fff;
}

.cus-info-header{
	margin-top: 50px;
	box-shadow: none;
	background: @color-primary;
	color:#fff;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	padding: 10px 0 0;
	margin-bottom: 0;
	a{
		color: #fff;
	}
	.card-header{
		.info{
			width: 50%
		}
		.photo{
			width: 20%;
		}
	}

	.tag{
		font-size: 12px;
		background: orange;
		color: #fff;
		padding:0 1px;
		border-radius: 2px;
		margin-left: 4px;
		margin-top:3px;
		 }
	.integrity{
		font-size: 12px;
		margin-left: 4px;
		margin-top:4px;
	}
	.tel{
		margin-top: 0px;
	}
	.photo-wraper{
		width: 65px;
		height: 65px;
		border-radius: 50%;
		overflow: hidden;
		margin-top: -10px;
		img{
			display: block;
			width: 100%;
			height: 100%;
		}
	}
}
.cus-content{
	margin-top: 194px;
	height: 400px;
	margin-bottom: 30px;
}

.sign-top{
	margin-top: 44px;
	background: #4DA2FD;
	.flexbox;
	.align-items(center);
	color: #fff;
	height: 200px;	
	.button{
		margin: 0 auto;
	}
	div{
		.flex(1);

		text-align: center;
	}
}
.cus-info-nav{
	}

</style>