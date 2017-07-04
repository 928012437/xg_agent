<template>
 <div>
	<div class="row user-info" v-if="isregister">
		<div class="col-20">
			<div class="img-wrapper">
				<img :src="data.headurl" />
			</div>
		</div>
		<div class="col-80">
			<div class="info">
				<div class="user-name">{{data.realname}}</div>
				<div>
					<router-link to="/addons/xg_agent/qmjjr/home/bankcard" class="iconfont brank-card">&#xe657;</router-link>
					<router-link to='' class="iconfont user-card">&#xe685;</router-link>
				</div>
			</div>
			<div class="result">
				<div>
					<i class="iconfont pull-left">&#xe677;</i>
					<div class="pull-left">
						总佣金
						<span class="score">
						<strong>{{data.commission}}</strong>元
						</span>
					</div>
				</div>

				<div>
				<i class="iconfont pull-left">&#xe63f;</i>
					推荐人数
					<span class="score">
					<strong>{{data.allnum}}</strong>人</span>
				</div>
			</div>
		</div>
	</div>

	 <div class="indexHeader" v-if='isregister==false'>
		 <router-link to="/addons/xg_agent/qmjjr/checksign">
		 <img :src="headerimg" alt="" >
		 </router-link>
	 </div>


</div>
</template>
<script type='text/ecmascript-6'>
	import axios from 'axios';
	import zepto from 'n-zepto'

export default{
	props:[],
	data(){
		return{
			data:'',
			rule:'',
			isregister:false,
			headerimg:"../img/header.jpg"
		}
	},
	created(){
		this.getinfo();
		this.getjssdk();
		this.getheaderimg()
	},
	methods: {
		getjssdk(){
			var that = this;
			var url=document.location.href;
			axios.post('http://w.xghd.cc/testyang/jssdk.php?url='+url).then(function (res) {

				wx.config({
					debug: false, //调式模式，设置为ture后会直接在网页上弹出调试信息，用于排查问题
					appId: res.data.appId,
					timestamp: res.data.timestamp,
					nonceStr: res.data.nonceStr,
					signature:res.data.signature,
					jsApiList: [//需要使用的网页服务接口
						'checkJsApi', //判断当前客户端版本是否支持指定JS接口
						'onMenuShareTimeline', //分享给好友
						'onMenuShareAppMessage', //分享到朋友圈
						'onMenuShareQQ', //分享到QQ
						'onMenuShareWeibo' //分享到微博
					]
				});
				wx.ready(function () {
					var sharedata = {
						title: that.rule.sha_title,
						desc: that.rule.sha_dec,
						link: urlstr+"qmjjr.opensource.cover&tjid="+that.data.id,
						imgUrl: that.rule.sha_img,
						success: function () {
						},
						cancel: function () {
						}
					};
					wx.onMenuShareAppMessage(sharedata);
					wx.onMenuShareTimeline(sharedata);
					wx.onMenuShareQQ(sharedata);
					wx.onMenuShareWeibo(sharedata);
				});
			})
		},
			getinfo(){
				var that = this;
				var token=this.getCookie('token')
				if(token==''){
					that.isregister=false;
					return;
				}
				urlstr="http://w.xghd.cc/app/index.php?i="+uniacid+"&c=entry&m=xg_agent&do=mobile&token="+token+"&r=";

				axios.post(urlstr + 'qmjjr.getinfo').then(function (res) {
					if (res.data.code == 3) {
						that.data = res.data.data;
						that.rule = res.data.rule;
						that.isregister=true
						var $body = $('body');
						document.title = that.rule.title;
						var $iframe = $('<iframe src="/favicon.ico"></iframe>');
						$iframe.on('load',function() {
							setTimeout(function() {
								$iframe.off('load').remove();
							}, 0);
						}).appendTo($body);
					}else {
						that.isregister=false
					}
				});
			},
		getCookie(c_name) {

			if (document.cookie.length > 0) {

				var c_start = document.cookie.indexOf(c_name + "=");

				if (c_start != -1) {

					c_start = c_start + c_name.length + 1;

					var c_end = document.cookie.indexOf(";", c_start);

					if (c_end == -1) {

						c_end = document.cookie.length;

					}

					return unescape(document.cookie.substring(c_start, c_end));//返回内容，解码。

				}

			}

			return "";

		},
		getheaderimg(){
			var that = this;
			axios.post(urlstr + 'qmjjr.opensource.getheaderimg').then(function (res) {
				if (res.data.code == 3) {
					if(that.headerimg!=''){
						that.headerimg = res.data.data;
					}
				}
			});
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
.indexHeader{
	width: 100%;
	height: auto;
	img{
		display: block;
		width: 100%
	}
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