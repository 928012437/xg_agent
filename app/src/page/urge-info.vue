<template>
 <div class="content">
 <div class="content">
 	<top></top>
 	<div class="content margin-top urge-content">
		<div class="card">
		  <div class="card-header">交易信息</div>
		  <div class="card-content">
		    <div class="card-content-inner">
		    	<div class="list-block">
		    	    <ul>
		    	      <li class="item-content">
		    	        <div class="item-inner">
		    	          <div class="item-title">房间</div>
		    	          <div class="item-after">{{fname}}</div>
		    	        </div>
		    	      </li>
		    	    </ul>
		    	  </div>
		    </div>
		  </div> 
	    </div>
		
		<div class="card">
		  <div class="card-header">最近催办</div>
		  <div class="card-content">
		    <div class="card-content-inner">
		    	<div class="list-block">
		    	    <ul>
		    	      <li class="item-content">
		    	        <div class="item-inner">
		    	          <div class="item-title">催办日期</div>
		    	          <div class="item-after">{{log.createtime}}</div>
		    	        </div>
		    	      </li>
		    	      <li class="item-content">
		    	        <div class="item-inner">
		    	          <div class="item-title">催办结果</div>
		    	          <div class="item-after">{{log.result}}</div>
		    	        </div>
		    	      </li>
		    	      <li class="item-content">
		    	        <div class="item-inner">
		    	          <div class="item-title">催办内容</div>
		    	          <div class="item-after">{{log.content}}</div>
		    	        </div>
		    	      </li>
		    	      <li class="item-content">
		    	        <div class="item-inner">
		    	          <div class="item-title">下次催办</div>
		    	          <div class="item-after">{{log.xiacitime}}</div>
		    	        </div>
		    	      </li>
		    	     
		    	    </ul>
		    	  </div>	  
		    </div>
		     <div class="card-footer">
		     	<button class="button button-big urgeBtn"
		     	 @click="$router.push('/addons/xg_agent/app/business/urgeinfo/'+$route.params.id+'/urgeresult')">催办</button>
		     </div>
		  </div> 
	    </div>
    </div>
    </div>
    <router-link to='' class='bottom-btn bar bar-tab'>
     <i class="iconfont">&#xe607;</i>联系客户
    </router-link>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import axios from 'axios';

export default{
	data(){
	return{
		log:'',
		fname:''
	}
	},
	created(){
		 this.$store.commit('footer',false);
		this.getcblog();
	},
	components:{
		Top
	},
	methods:{
		getcblog(){
			var id=this.$route.params.id;
			var that = this;
			axios.post(urlstr + 'user.counselors.getcblog&id='+id).then(function (res) {
				if (res.data.code == 3) {
					that.log=res.data.data;
					that.fname=res.data.fname;
				}
			});
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

.item-content{
	font-size: 14px;
}
.item-after{
	color: @color-text-gray
}
.button.button-big{
	height: 2.2rem;
	line-height: 2.2rem
}
.urgeBtn{
	padding: 0 2.2rem;
	margin: 0 auto;
}
.urge-content{
	height:530px;
	margin-bottom: 40px;
	
}

</style>