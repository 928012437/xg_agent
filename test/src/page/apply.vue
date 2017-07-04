<template>
 <div class="content">
 <top></top>
 <div class="content apply-content margin-top">
	<div class="card apply-card">
	    <div class="card-header">我的可提现佣金</div>
	    <div class="card-content">
	      <div class="card-content-inner pay-content">
	      	￥ 6000.00
	      </div>
	    </div>
	    <div class="card-footer">
	    	<button class="button apply-btn button-big" >我要提现</button>
	    </div>
	 </div>
</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
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
@import '../style/less/variables.less';
.apply-card{
	/*box-shadow: none;*/
}
.apply-btn{
	margin: 0 auto;
}
.pay-content{
	font-size: 24px;
}
.apply-btn{
	padding: 0 2rem;
	background: @color-primary;
	color:#fff;
}

</style>