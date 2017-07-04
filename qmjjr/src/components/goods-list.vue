<template>
 <div>
 <ul>
 	<li v-for='goods in goodses'>
 		<div class="card">
 			<router-link :to="'/addons/xg_agent/qmjjr/index/goodsinfo/'+goods.id">
 			<div class="note">{{goods.l_youhui}}</div>
			<div class="card-header">
				<div>{{goods.title}} <span>[{{goods.location_a}}]</span></div>
			</div>
			<div class="card-content">
					<div class="card-content-inner">
						<img :src="goods.thumb" alt="">
						<div class="tag">
							<ul>
								<li v-for='tag in goods.l_biaoqian'>{{tag}}</li>
							</ul>
							<div class="pull-right">{{goods.price_type}}{{goods.price}}&nbsp;元/㎡</div>
						</div>
					</div>
			</div>	
		<div class="card-footer">
			<div>佣金: <span class="pay">{{goods.commis}}</span></div>
			<div>
				<router-link  class="button main-color" 	to='/addons/xg_agent/qmjjr/customer/recommend'>推荐</router-link>
			</div>
		</div>
		</router-link>
		</div>
 	</li>
 </ul>

</div>
</template>
<script type='text/ecmascript-6'>
	import axios from 'axios';

export default{
		data(){
			return{
				goodses:[],
			}
		},
	created(){
		this.getgoodlist();
	},
	methods: {
		getgoodlist(){
			var that = this;
			axios.post(urlstr + 'qmjjr.opensource.getloupanlist').then(function (res) {
				if (res.data.code == 3) {
					res.data.data.forEach(function(value){
						that.goodses.push(value);
					});
				}
			});
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