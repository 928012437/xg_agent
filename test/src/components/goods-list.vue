<template>
 <div>
 <ul>
 	<li v-for='goods in goodses'>
 		<div class="card">
 			<router-link :to="'/index/goodsinfo/'+goods.id">
 			<div class="note">{{goods.note}}</div>
			<div class="card-header">
				<div>{{goods.name}} <span>[忘记些什么了]</span></div>
			</div>
			<div class="card-content">
					<div class="card-content-inner">
						<img src="//gqianniu.alicdn.com/bao/uploaded/i4//tfscom/i3/TB10LfcHFXXXXXKXpXXXXXXXXXX_!!0-item_pic.jpg_250x250q60.jpg" alt="">
						<div class="tag">
							<ul>
								<li v-for='tag in goods.tags'>{{tag}}</li>
							</ul>
							<div class="pull-right">{{goods.area}}&nbsp;㎡</div>
						</div>
					</div>
			</div>	
		<div class="card-footer">
			<div>佣金: <span class="pay">{{goods.pay}}</span></div>
			<div>
				<router-link  class="button main-color" 	to='/customer/recommend'>推荐</router-link>
			</div>
		</div>
		</router-link>
		</div>
 	</li>
 </ul>

</div>
</template>
<script type='text/ecmascript-6'>

export default{
		data(){
			return{
				goodses:[
					{
						name:'某某房产',
						tags:['朝阳','厉害','牛逼'],
						pay:666,
						note:'这个也忘了sdfsdf',
						area:1000,
						id:2
					},
					{
						name:'某某房产',
						tags:['朝阳','厉害','牛逼'],
						pay:666,
						note:'这sdfsdf',
						area:500,
						id:8
					}
				]
			}
		}	
}
</script>
<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

	.card{
		a{color: @color-text}
		position: relative;
		box-shadow: none;
		.note,.tag{
			position: absolute;
		}
		.note{
				background: @color-primary;
				height: 28px;
				color: #fff;
				top: 60px;
				left:-5px;
				min-width: 60px;
				z-index: 10;
				text-align: center;
				line-height: 28px;
				padding: 0 6px;
				&:after{
					content:'';
					    position: absolute;
					    border: .18rem solid #d04e00;
					    border-left-color: transparent;
					    border-bottom-color: transparent;
					    left: 0;
					    bottom: -5px
				}
		}
		.card-header{
			font-size: 16px;
			span{
				font-size: 12px;
				color: @color-text-gray
			}
		}
		
		.card-content{
			padding: 0 5px;
		}
		.card-content-inner{
			padding: 0px;
			position: relative;
			overflow: hidden;
			img{
				display: block;
				width: 100%;
				height: 100%;
			}
			.tag{
				bottom: 0;
				background:rgba(0,0,0,.6);
				height: 40px;
				width: 100%;
				padding: 10px;
				color:rgba(255,255,255,.7);
				li{
					float: left;
					margin-left: 10px;
					line-height: 20px;
					&+li{
						border-left:1px solid rgba(255,255,255,.5);
						padding-left: 10px
					}				} } 

				}
				.card-footer{
					.pay{
						font-size: 20px
					}
				}
	}


</style>