<template>
 <div class="content">
	<top></top>
	<div class="atc-content margin-top">
	  <form action="">
	      <div class="card">
	          <div class="card-header">
	            <div class="card-tips">
	              正面
	            </div>
	          </div>
	          <div class="card-content">
	            <div class="card-content-inner">
	              <img :src="data.idcardurl1" alt="">
	              <input type="file" id="imgurl1" >
	            </div>
	          </div>
	          <div class="card-footer">
	            提交时间：<time>{{data.idcardtime1}}</time>
	            <span class="pull-right">

	            </span>
	          </div>
	      </div>

	   <div class="card">
	          <div class="card-header">
	              <div class="card-tips man-ele-on">
	                反面
	              </div>
	          </div>
	          <div class="card-content">
	            <div class="card-content-inner">
	              <img :src="data.idcardurl2" alt="">
	               <input type="file" id="imgurl2" >
	            </div>
	          </div>
	          <div class="card-footer">
	            提交时间：<time>{{data.idcardtime2}}</time>
	            <span class="pull-right">
	             
	            </span>
	          </div>
	        </div>
	</form>
	 <div class="card">
    <div class="card-header">提示</div>
	    <div class="card-content">
	      <div class="card-content-inner">
	      	请上传清晰、完整的图片、身份证、照片请点击图片区域进行上传
	      </div>
	    </div>
  </div>
</div>

</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import axios from 'axios';
import zepto from 'n-zepto'

export default{
	data(){
	return{
		data:[]
	}
	},
	components:{
		Top
	},
	mounted(){
		var url=urlstr + 'qmjjr.uploadimg';
		var that=this
		zepto("#imgurl1").change(function () {
			var formData = new FormData();
			formData.append("fileimg",document.getElementById("imgurl1").files[0]);
			zepto.ajax({
				url: url,
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				success: function (data) {
					that.idcardpost(1,data);
				},
				error: function () {
					alert("上传失败！");
				}
			});
		});
		zepto("#imgurl2").change(function () {
			var formData = new FormData();
			formData.append("fileimg",document.getElementById("imgurl2").files[0]);
			zepto.ajax({
				url: url,
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				success: function (data) {
					that.idcardpost(2,data);
				},
				error: function () {
					alert("上传失败！");
				}
			});
		});
	},
	created(){
		 this.$store.commit('footer',false);
		this.getinfo();
	},
	methods:{
		getinfo(){
			var that = this;
			axios.post(urlstr + 'qmjjr.getinfo').then(function (res) {
				if (res.data.code == 3) {
					that.data=res.data.data;
					if(that.data.idcardurl1==''){
						that.data.idcardurl1='../src/img/pic.jpg';
					}
					if(that.data.idcardurl2==''){
						that.data.idcardurl2='../src/img/pic.jpg';
					}
				}
			});
		},
		idcardpost(type,imgurl){
			var that = this;
			axios.post(urlstr + 'qmjjr.idcardpost&type='+type+'&imgurl='+imgurl).then(function (res) {
				if (res.data.code == 4) {
					if(res.data.status == 1){
						that.getinfo();
					}
				}
			});
		}
	}
	
}

</script>

<style lang='less' scoped>
.atc-content{
	padding:10px ;
}
.card-content-inner{
	overflow: hidden;
	input{
		position: absolute;
		top: 0;
		left: 0;
		opacity: 0;
		display: inline-block;
    	height: 90%;
    	width: 90
	}
	img{
		display: block;
		width: 100%;
		height: 100%;
	}
}
.card{
	overflow: hidden;
}
.card-header{
	.card-tips {
    width: 130px;
    height: 38px;
    position: absolute;
    left: -40px;
    text-align: center;
    line-height: 38px;
    color: #fff;
    transform: rotate(-45deg);
    background: #fe8a19;
    z-index: 999;
	}
} 
.man-ele-on {
    background: #0894ec !important;
    color: #fff !important;
}
</style>