<template>
 <div class="content">
	
	<div class="img-wrapper">
		<!-- <img src="../img/logo.png" alt=""> -->
		<img src="../img/header.jpg" alt="">
	</div>
	
	<div class="button-wrapper">
		<button class="button button-big">
			<i class="iconfont">&#xe652;</i>微信登录
		</button>
		<button class="button button-big" @click="$router.push('/checksign/sign')">
			<i class="iconfont">&#xe627;</i>账号登录
		</button>
	</div>
<div class="bar bar-tab">
  还没有账号，<router-link to=''>点击注册</router-link>
</div>
</div>
</template>
<script type='text/ecmascript-6'>

export default{
	created(){
		 this.$store.commit('footer',false);
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.img-wrapper{
	width: 100%;
	margin: 6% auto 0;
	img{
		width: 100%;
		display: block;
		height: auto;
	}
}
.content{
	background: @color-primary;
}
.button-wrapper{
	/*background: #fff;*/
	text-align: center;
	margin: 10% 0
}
.button{
	margin: 34px auto ;
	padding: 0 4rem;
	border: 2px solid #fff;
	color: #fff;
	font-size: 18px;
	&:last-child{
		background:#fff;
		color: @color-primary
	}
	.iconfont{
		margin-right: 3%
	}
}
.bar-tab{
	background: transparent;
	border: none;
	font-size: 14px;
	color: #fff;
	text-align: center;
	a{
		color: Darken(@color-primary,30%);
		text-decoration: underline;
		line-height: 40px;
	}
	&:before{
		background:none;
	}
}

</style>