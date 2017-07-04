<template>
 <div class="content">
	<top></top>
	
<div class="list-block content margin-top">
    <ul>
      <!-- Text inputs -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-name"></i></div>
          <div class="item-inner">
            <div class="item-title label">姓　　名：</div>
            <div class="item-input">
              <input type="text" placeholder="请输入">
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-email"></i></div>
          <div class="item-inner">
            <div class="item-title label">手 &nbsp;机 &nbsp;号：</div>
            <div class="item-input">
              <input type="text" placeholder="请输入">
            </div>
          </div>
        </div>
        </li>
         <li class="align-top">
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-comment"></i></div>
          <div class="item-inner">
            <div class="item-title label">服务宣言：</div>
            <div class="item-input">
              <textarea></textarea>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-email"></i></div>
          <div class="item-inner">
            <div class="item-title label">微信二维码名片</div>
            <div class="item-input">
               <div class="pull-right img-wrap">
		        <input type="file" id="upload">
		        <img src="../img/ewm.jpg" alt="" id="img">
		     </div>
            </div>
             <div> <span class="icon icon-right"></span></div>
          </div>
        </div>
        </li>
      </ul>
     
 		<a href="" class="ewm-tips">如何获得微信二维码?</a>
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
	mounted(){
		document.getElementById('upload').addEventListener('change',function(){
			 var fileReader=new FileReader();//新建FileReader对象实例
		     fileReader.onload=function(e){
		       document.getElementById('img').setAttribute('src',e.target.result)
		     }
		     fileReader.readAsDataURL(this.files[0]);
		   })

	}

}

</script>

<style lang='less' scoped>
.item-content{
	font-size: 14px;
	input{
		font-size: 14px;
	}
}
.img-wrap{
	width: 140px;
	height: 140px;
	position: relative;	
	input[type='file'],img{
		display: block;
		width: 100%;
		height: 100%;
	}
	input[type='file']{
		opacity: 0;
		z-index: 999;
		top:0;
		position: absolute;
	}
}
#img{
	position: absolute;
	top:0;
	width: 100%;
	height: 100%;
}
.ewm-tips{
	margin-top: 10px;
	font-size: 12px;
	padding: 15px;
}
</style>