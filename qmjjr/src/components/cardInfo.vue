<template>
<div class="content">
  <div class="list-block">
 	 <form action="" id="card-form">
    <ul>
      <!-- Text inputs -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-name"></i></div>
          <div class="item-inner">
            <div class="item-title label">姓名</div>
            <div class="item-input">
              <input type="text" placeholder="请输入" v-model='name'>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-email"></i></div>
          <div class="item-inner">
            <div class="item-title label">银行卡号</div>
            <div class="item-input">
              <input type="text" placeholder="请输入" v-model='num'>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-password"></i></div>
          <div class="item-inner">
            <div class="item-title label">开户银行</div>
            <div class="item-input">
              <input type="text" placeholder="请选择"  :value='brankName' class="" @click='show=!show' >
            </div>
          </div>
        </div>
      </li>
       <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-password"></i></div>
          <div class="item-inner">
            <div class="item-title label">支行名称</div>
            <div class="item-input">
              <input type="text" placeholder="请输入" class="" v-model='zhihang' >
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-password"></i></div>
          <div class="item-inner">
            <div class="item-title label">城市</div>
            <div class="item-input">
              <input type="text" placeholder="请输入" class="" v-model='city'>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-toggle"></i></div>
          <div class="item-inner">
            <div class="item-title label">设为默认卡</div>
            <div class="item-input">
              <label class="label-switch">
                <input type="checkbox" v-model='isdef'>
                <div class="checkbox"></div>
              </label>
            </div>
          </div>
        </div>
      </li>
       <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-password"></i></div>
          <div class="item-inner">
            <div class="item-title label">银行卡正面照片</div>
            <div class="item-input">

              <input type="file" placeholder="请输入" class="" id="imgurl" >

            </div>
          </div>
        </div>
      </li>
	</ul>
</form>
</div>
	<popup v-model='show' height='200px'>
	  <header class="bar bar-nav" >
	  	  <h1 class="title"> 开户银行</h1>
         <div @click='show=!show' class="close" >
          <button class="button pull-right">确定</button>
         </div>
        </header>
		<picker :data='brank' v-model='brankName'></picker>
	</popup>
	<div>{{cardInfo}}</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Popup from './popup/index.vue'
import Picker from './picker/index.vue'
import zepto from 'n-zepto'

export default{
	components:{
		Popup,
		Picker
	},
	props:['card'],
	computed:{
		cardInfo(){
			this.card.bankname=this.brankName[0]
			this.card.num=this.num.trim()
			this.card.name=this.name
            this.card.zhihang=this.zhihang
            this.card.city=this.city
            this.card.isdef=this.isdef?1:0
            this.card.imgurl=this.imgurl
			switch(this.card.name){
				case "农业银行" :
				this.card.color="linear-gradient(to right, #e65a65, #e74f7c)"
				break;
				case "建设银行" :
				this.card.color='linear-gradient(to right, #e65a65, #e74f7c)'
				break;
			}

		}
	},
	mounted(){
        var url=urlstr + 'qmjjr.uploadimg';
        var that=this
        zepto("#imgurl").change(function () {
            var formData = new FormData();
            formData.append("fileimg",document.getElementById("imgurl").files[0]);
            zepto.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    that.imgurl=data;
                },
                error: function () {
                    alert("上传失败！");
                }
            });
        });

	},
	destroyed(){

	},
	data(){
		return{
			name:'',
			num:'',
            brankName:[],
            zhihang:'',
            city:'',
			isdef:false,
            imgurl:'',
			color:'',
			brank:[[
				'农业银行',
				'建设银行',
				'工商银行'
			]],
			show:false
		}
	}
}

</script>

<style lang='less' scoped>
.list-block{
	margin-top: 18px;
}
.item-content{
	font-size: 14px;
	input{
		font-size: 14px;
	}
}

</style>