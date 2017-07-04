<template>
 <div class="content">
 	<shop-header></shop-header>
<div class="content cus-content">
	<div class="card cus-info-header">
	    <div class="card-header">
	    	<div class="photo">
	    		<div class="photo-wraper">
	    			
	    		</div>
	    	</div>
	    	<div class="info">
	    	<div class="clearfix">
	    		<span class="pull-left">姓名 </span>
	    		<span class="tag pull-left">报备</span>
	    		<span class="integrity pull-left">完整度23%</span>
	    	</div>	
	    		<p class="tel">18453156756</p>
	    		<p class="tel">意向产品：</p>
	    	</div>

	    	<div class="call">
	    		<router-link to='' class='iconfont'>
					&#xe62e;
	    		</router-link>
	    		<router-link to=''  class='iconfont'>
					&#xe607;
	    		</router-link>
	    	</div>
	    </div>
	    <div class=" sale-content">
	    		<div >
	    			<div class="photo-wraper pull-left">
	    				
	    			</div>
					<div class="info pull-left">
						 徐绍东<span>销售员</span>
						 <p class="tel">124842315</p>
					</div>
	    		</div>
	    	
	    	<div>

	    		<router-link to=''class='iconfont saleTel'>
					&#xe607;
	    		</router-link>
	    	</div>
	    </div>
	 </div>
	 <div class="path">
	 	<ul>
	 		<li class="path-list">报备</li>
	 		<li class="path-list">到访</li>
	 		<li class="path-list">交易</li>
	 		<li class="path-list">结佣</li>
	 	</ul>
	 </div>  
			<follow></follow>
	</div>
</div>
	
</div>
</template>
<script type='text/ecmascript-6'>
import shopHeader from '../components/header.vue'


import Follow from '../components/cus-info/follow.vue'

export default{
	components:{
		shopHeader,
		Follow,
		
		
	},
	created(){
		 this.$store.commit('footer',false);
		 this.getData()
		 
	},
	data(){
		 return {
  			today:'',
  			tab:'follow',
  			info:'',
    	}
	},
	computed:{	
	},
	methods:{
		getData(){
			// alert(this.$route.params.id)
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
	.call{
		.iconfont{
			color: #fff;
			font-size: 22px;
		}
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
		margin: 0px;
		font-size: 16px;
	}
	.photo-wraper{
		width: 65px;
		height: 65px;
		border-radius: 50%;
		background-color: red;
		overflow: hidden;
		margin-top: -10px;
		img{
			display: block;
			width: 100%;
			height: 100%;
		}
	}
}

.sale-content{
	margin:0;
	background: #fff;
	border-radius: 0;
	color: @color-text-secondary;
	font-size: 13px;
	.flexbox;
	padding: 10px 20px 5px;
	.justify-content(space-between);
	.photo-wraper{
		width: 40px;
		height: 40px;
		margin-top:-2px;
		margin-right: 8px;
	}
	.tel{
		margin-top: -5px;
	}
	.info>span{
		margin-left: 4px;
		color: #ccc;
		font-size: 12px;
	}
	.saleTel{
		margin-top: 4px;
		width: 26px;
		height: 26px;
		display: inline-block;
		border-radius: 50%;
		background: @color-primary;
		text-align: center;
		line-height: 25px;
		color: #fff;
		
	
	}
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
.path{
	background:#fff;
	margin-top: 10px;
	padding:15px  15px 10px;
	.clearfix;
	margin-bottom: 10px;
	ul{
		margin: 0 auto;
		.clearfix;
	}

	.path-list{
		float: left;
		font-size: 14px;
		padding: 20px 0 0;
		width: 25%;
		text-align: center;
		border-top: 1px solid @color-primary;
		position: relative;
		&:before{
			position:absolute;
			content: '';
			width: 16px;
			height: 16px;
			border-radius: 50%;
			background: @color-primary;
			top: -8px;
			left: 50%;
			margin-left: -8px;
		};
	}

}

</style>