<template>
    <div>
    <header class="bar bar-nav">
      <nav class="sta-nav">
			<ul class="sta-nav-list">
				<li><router-link to='/customer/assign' active-class='sta-nav-on' class='sta-link'>客户分配</router-link></li>
				<li><router-link to='/customer/analysis' active-class='sta-nav-on' class='sta-link'>客户分析</router-link></li>	
			</ul>
		</nav>
    </header>
    </div>
</template>

<script type='text/ecmascript-6'>
	export default{
		data(){
			return{
				
			}
		}
	}

</script>

<style lang='less' scoped>
@import '../../common/style/mixin.less';
@import '../../common/style/mixins.less';


.sta-nav{
	padding: 5px 0px;
	.sta-nav-list{
		margin: 0;
		.flexbox();
		text-align: center;
		border:1px solid @color-primary;
		border-radius: 8px;
		overflow: hidden;
		li{
			flex: 1;
			&:nth-child(2){
				border-left:1px solid @color-primary;
				
			}
		}
		.sta-nav-on{
			background: @color-primary;
			color: #fff;
		}
		.sta-link{
			display: block;
			.size(100%,100%);
			padding: 2px 0;
		}
	}
}


</style>