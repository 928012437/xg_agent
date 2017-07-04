<template>
 <div>
	<div class="row user-info">
		<div class="col-20">
			<div class="img-wrapper">
				
			</div>
		</div>
		<div class="col-80">
			<div class="info">
				<div class="user-name">姓名</div>
				<div>
					<router-link to="/home/bankcard" class="iconfont brank-card">&#xe657;</router-link>
					<router-link to='/rigister' class="iconfont user-card">&#xe685;</router-link>
				</div>
			</div>
			<div class="result">
				<div>
					<i class="iconfont pull-left">&#xe677;</i>
					<div class="pull-left">
						总佣金
						<span class="score">
						<strong>1000</strong>元
						</span>
					</div>
				</div>

				<div>
				<i class="iconfont pull-left">&#xe63f;</i>
					<router-link to='/checksign'>推荐人数</router-link>
					<span class="score">
					<strong>1000</strong>人</span>
				</div>
			</div>
		</div>
	</div>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:[],
	data(){
		return{
			
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.user-info{
	background: @color-primary;
	padding: 20px 0 20px 10px ;

}
.info,.result{
	.flexbox;
	color:#fff;
	font-size: 14px;
	padding: 10px;
	div{
		.flex(1);
	}
	.score{
		margin-top: -5px;
		display: block;
		strong{
			font-size: 18px;
		}
	}
}
.info{
	border-bottom: 1px solid @color-split;
	.iconfont{
		font-size: 20px;
		float: right;
		color: #fff;
	}
	.brank-card{
		margin-left: 10px;
		/*margin-right: 12%;*/
	}
	.user-name{
		font-size: 22px;
	}
}
.result {
	.iconfont{
		margin-right: 5px;
		margin-top: 4px;
	}
}
.row .col-80{
	width:60%;
	margin-left: 10%
}
.img-wrapper{
	width: 80px;
	height: 80px;
	border-radius: 50%;
	background:red;
	overflow: hidden;
	margin-top: 15px;
	img{
		display: block;
		width: 100%;
		height: 100%;
	}
}
</style>