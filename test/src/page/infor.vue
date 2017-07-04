<template>
 <div class="content">
	<div class="card demo-card-header-pic">
	    <div valign="bottom" class="card-header color-white no-border no-padding">
	      <img class='card-cover' src='//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg' alt="">
	    </div>
	    <div class="card-content">
	      <div class="card-content-inner">
	       <span class="tag">商品</span>
	        {{name}} undf不熟水电费了水电费
	      </div>
	    </div>
	    <div class="card-footer">
	     <div class="score">{{score}} 555<span>积分</span> <span class="price">原价：1.800元</span></div>
	    
	    </div>
	  </div>

	  <div class="goods-info">
	  	<tab activeColor='#FF4F51'>
	  		<tab-item  @click.native="value='info'">商品详情</tab-item>
	  		<tab-item  @click.native="value='record'">参与记录</tab-item>
	  	</tab>
	  </div>
	  <div class="info-wrap ">
	  		<div class="info content" v-show="value=='info'">
	  		{{value}}
	  		</div>
	  		<div class="record content" v-show="value=='record'">{{value}}
	  		
	  		</div>
	  </div>
	  <div class="card recommend">
	     <div class="card-header">为您推荐</div>
	     <div class="card-content">
	       <div class="card-content-inner">
	       		<ul>
	       			<li>
	       				
	       			</li>
	       			<li>
	       				
	       			</li>
	       			<li>
	       				
	       			</li>
	       		</ul>

	       </div>
	     </div>
	    
	   </div>
	
</div>
</template>
<script type='text/ecmascript-6'>
import tab from '../components/tab/tab.vue'
import tabItem from '../components/tab/tab-item.vue'

export default{
	components:{
		tab,
		tabItem
	},
	created(){
		this.getData(this.$route.params.id)
	},
	methods:{
		getData(res){
			// alert(res)
		}
	},
	data(){
		return{
			value:'info'
		}
	}

}

</script>

<style lang='less' scoped>
@import '../style/less/variables.less';
.go-back{
	position: absolute;
	background: rgba(0,0,0,.5);
	width: 60px;
	height: 60px;
	border-radius: 50%;
	z-index: 999
}
.tag{
	background: @color-primary;
	color: #fff;
	padding: 4px;
}
.card-content-inner{
	color: @color-text-secondary
}
.score{
	font-size: 22px;
	color: @color-primary;
	span{
		font-size: 0.6em;
		margin-left: 0.3em
	}
	.price{
		color: @color-text-gray;
		 text-decoration:line-through;
	}
}
.goods-info{
	background: #fff
}
.recommend{
	margin: 10px 0 52px;
	@media only screen and (-webkit-min-device-pixel-ratio: 2){
		.card-header:after {
   		 	transform: scaleY(1);
		}
	}
}
.info-wrap{
	min-height: 480px;
	background: #fff;
	position: relative;
	.info,.record{
		padding: 5px 10px
	}
}
</style>