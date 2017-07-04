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
	              <img src="../img/pic.jpg" alt="">
	              <input type="file" >
	            </div>
	          </div>
	          <div class="card-footer">
	            提交时间：<time></time>
	            <span class="pull-right">
	              <img src="" alt="">
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
	              <img src="../img/pic.jpg" alt="">
	               <input type="file" >
	            </div>
	          </div>
	          <div class="card-footer">
	            提交时间：<time></time>
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
export default{
	components:{
		Top
	},
	created(){
		 this.$store.commit('footer',false);
	},
	
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