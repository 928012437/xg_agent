<template>
 <div class="content">
 	<div class="cus-header ">
 		<div class="message">
 			<div class="col-50">
 				0
 				<p class='cus-text'>累计推荐</p>
 			</div>
 			<div class="col-50">
 					0
 				<p class='cus-text'>累计推荐</p>
 			</div>
 			
 		</div>
 		<div class="message">
 			<div class="col-50">
 				0
 				<p class='cus-text'>累计推荐</p>
 			</div>
 			<div class="col-50">
 				0
 				<p class='cus-text'>累计推荐</p>
 			</div>
 		</div>
 	</div>
	<search></search>
		<div class="card customer-nav">
	    <div class="card-header" @click='ishow=!ishow' >
	    	<div>{{type}}<span class="icon icon-left pull-right" :class="{'iconOn':ishow}"></span></div>
	    	<div> 
				<button class="button pull-right" @click="$router.push('/customer/recommend')" >我要推荐</button>
	    	</div>
	    </div>
	    <div class="card-content list-block" >
	      <div class="card-content-inner " v-show='ishow' >	
	      	 <ul>
		      <li class="item-content " v-for='types in typeData' @click='type=types' :class="{'navOn':type==types}">
		        <div class="item-inner">
		          <div class="item-title">{{types}}</div>
		          <div class="item-after"></div>
		        </div>
		      </li>
	   		 </ul>
	      </div>
		</div>
		</div>
	<div class="content cus-list">
		<cus-list></cus-list>
	</div>



</div>
</template>
<script type='text/ecmascript-6'>
import Search from '../components/search.vue'
import cusList from '../components/cus-list.vue';
export default{
	components:{
		Search,
		cusList
	},
	created(){
		this.$store.commit('footer',true);
	},
	data(){
		return{
			typeData:['所有客户','无效客户','已报备客户'],
			type:'所有客户',
			ishow:false
		}
	},
	watch:{
		type(val){
			this.ishow=!this.ishow
		}
	}
	
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.cus-header{
	color: #fff;
	text-align: center;
	div.message{
		.flexbox;
		background-color: @color-primary;
		&:first-child{
				border-bottom:2px solid @color-split;
			}
		div.col-50{
			.flex(1);
			padding: 10px 0;
			&:first-child{
				border-right:2px solid @color-split;
			};
		}
	}	
}
.customer-nav{
	position: relative;
	margin: 44px 0 0;
	border-top:1px solid @color-split;
	box-shadow: none;
	z-index: 999;
	.card-header{
		font-size: 14px;
		&>div:last-child{
			width:70%;
		}
		&>div:first-child{
			width:28%;
			.icon{
				font-size: 0.8em;
				margin-right: 4%;
				transform: rotate(0);
				transition: transform 0.2s
			}
			.iconOn{
				transform: rotate(-90deg);	
			}
		}
		
	}
	.card-content{
		position: absolute;
		background: #fff;
		width: 100%;
		.navOn{
			background:Lighten(@color-text-gray-light,10%)
		}
}
	
	.card-content-inner{
		font-size: 14px; 
		padding: 0;
	}
}
.cus-list{
	margin-top: 174px;
	/*margin-bottom: 20px;*/
	
	height: 320px;
	background: #fff;
}
.col-50{
	font-size: 20px;
}
.cus-text{
	margin-top: 0;
	font-size: 14px;
}
</style>