<template>
<div>
<div class="row goods-list">	
	<div class="col-50" v-for='goods in goodses'>
	<!-- :to="{  name: 'article', params: { id: value.articleId,uid:{a:123,b:123}}}" -->
	<router-link :to="{ name: 'info',params:{id:goods.goodsId}}">
	<div class="card demo-card-header-pic">
	    <div valign="bottom" class="card-header color-white no-border no-padding">
	      <img class='card-cover' :src='goods.img' alt="">
	    </div>
	    <div class="card-content">
	      <div class="card-content-inner">
	       <span class="tag">商品</span>
	        {{goods.name}}
	      </div>
	    </div>
	    <div class="card-footer">
	     <div class="score">{{goods.score}}<span>积分</span></div>
	     <div class="tag">兑换</div>
	    </div>
	  </div>
	  </router-link>
	</div>

</div>	
</div>
</template>
<script type='text/ecmascript-6'>

export default{
		data(){
			return{
				goodses:[
					{img:'//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg'
					 ,name:'此处是内容是内容是内容是内容是内容'
					 ,score:366,
					 goodsId:23
					},
					 {img:'//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg'
					 ,name:'此处是内容是内容是内容是内容是内容'
					 ,score:366,
					 goodsId:15
					},
					 {img:'//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg'
					 ,name:'此处是内容是内容是内容是内容是内容'
					 ,score:366,
					 goodsId:25
					},
					 {img:'//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg'
					 ,name:'此处是内容是内容是内容是内容是内容'
					 ,score:366
					 ,goodsId:7},
					 {img:'//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg'
					 ,name:'此处是内容是内容是内容是内容是内容'
					 ,score:366,
					 goodsId:1
					},
					 {img:'//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg'
					 ,name:'此处是内容是内容是内容是内容是内容'
					 ,score:366,
					 goodsId:14
					}

				]
			}
		},
		created(){

		},
		methods:{
			getData(){

			}
		}
}

</script>

<style lang='less' scoped>
@import '../style/less/variables.less';

.goods-list{
	margin-left: 0;
	padding: 0 2% 0 0;
	a{
		color: @color-text;
	}
}
.goods-list .col-50{
    width: 48%;
    margin-left: 2%
}
.card{
	box-shadow: none;
	padding: 0 ;
	margin: 0 0 10px;
	.card-content-inner{
		padding: 0.3rem;
	}
	.card-footer{
		padding:0 0.4rem;
		min-height:2rem;
	}
}
.tag{
	display: inline-block;
	border-radius: 4px;
	background: @color-primary;
	padding: 0 2px;
	color: #fff;
	font-size: @button-font-size;
}
.score{
	color: orange;
	font-size: 20px;
	span{
		color: @color-text-gray;
		font-size: 0.5rem;
		margin-left: 4px;
	}
}
</style>