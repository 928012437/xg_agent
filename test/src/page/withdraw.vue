<template>
 <div class="content">
 <top></top>
	<div class="card withdraw-header margin-top">
	   <div class="card-header">
	   		<div>
	   			累计佣金(元)
	   			<span class="money">352.00</span>
	   		</div>
	   		<div>
	   			<button class="button" @click="$router.push('/home/withdraw/detailed')
	   			">提现明细</button>
	   		</div>
	   </div>
	 </div>
	 <div class="card withdraw-main">
	    <div class="card-header">
	    	<div>可提现金额</div>
	    	<div class='cash'>1000.00</div>
	    </div>
	    <div class="card-content">
	      <div class="list-block">
	      		<ul>
	      		      <li class="item-content">
	      		        <div class="item-inner">
	      		          <div class="item-title">已申请佣金</div>
	      		          <div class="item-after">元</div>
	      		        </div>
	      		      </li>
	      		      <li class="item-content">
	      		        <div class="item-inner">
	      		          <div class="item-title">待打款佣金</div>
	      		          <div class="item-after">元</div>
	      		        </div>
	      		      </li>
	      		      <li class="item-content">
	      		        <div class="item-inner">
	      		          <div class="item-title">无效佣金</div>
	      		          <div class="item-after">元</div>
	      		        </div>
	      		      </li>
	      		      <li class="item-content">
	      		        <div class="item-inner">
	      		          <div class="item-title">成功提现佣金</div>
	      		          <div class="item-after">元</div>
	      		        </div>
	      		      </li>
	      		       <li class="item-content">
	      		        <div class="item-inner">
	      		          <div class="item-title">未结算佣金</div>
	      		          <div class="item-after">元</div>
	      		        </div>
	      		      </li>
	      		    </ul>
	      </div>
	    </div>
	  </div>
	  <div class="card withdraw-footer">
	      <div class="card-header">用户须知</div>
	      <div class="card-content">
	        <div class="card-content-inner">头和尾的卡片。卡头是用来显示一些额外的信息，或自定义操作卡标题和页脚。</div>
	      </div>
	      <div class="card-footer">
	      	<button class="button withdraw-btn button-big" @click="$router.push('/home/withdraw/apply')">我要提现</button>
	      </div>
	    </div>


</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
export default{
	components:{
		Top
	},
	created(){
		 this.$store.commit('footer',false);
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

.card{
	box-shadow: none;
}
.withdraw-header{
	background: @color-primary;
	margin:0;
	color: #fff;
	padding: 15px;
	.card-header{
		font-size: 16px;
	}
	@media only screen and (-webkit-min-device-pixel-ratio: 2){
		.card-header:after {
   	 		transform: scaleY(0);
		}
	}
	.money{
	display: block;
	font-size: 14px;
	}
	.button{
		color: #fff;
		border-color: #fff;
	}
}

.withdraw-main{
	margin: 0;
	font-size: 14px;
	.item-content{
		font-size: 14px;
	}
}
.withdraw-footer{
	margin: 10px 0 0;
	.withdraw-btn{
		padding: 0 1.8rem;
		margin:0 auto;
	}
}
</style>