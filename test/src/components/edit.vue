<template>
<div >
 <div class="list-block">
    <ul>
      <!-- Text inputs -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-name required">*</i></div>
          <div class="item-inner">
            <div class="item-title label" >姓名</div>
            <div class="item-input">
              <input type="text" placeholder="请输入" name='name'>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-email required">*</i></div>
          <div class="item-inner">
            <div class="item-title label">性别</div>
            <div class="item-input">
           
      	
            	<label for="man" class="form-sex" >
            		<input type="radio" name="sex" value='woman'  id='man' v-model='value'>
            		<i class="icon icon-form-checkbox" ></i>
            		&nbsp;男
            	</label>
              	&nbsp;&nbsp;&nbsp;
              	<label for="woman" class="form-sex">
            		<input type="radio" name="sex"  value='man' id='woman' v-model='value'>
            		<i class="icon icon-form-checkbox"></i>
            		&nbsp;女
            	</label>
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon required">*</i></div>
          <div class="item-inner">
            <div class="item-title label">手机</div>
            <div class="item-input">
              <input type="text" placeholder="请输入" name='tel'>
            </div>
            <div class="item-after">
            	<label for="phone" class="form-sex">
            		<input type="radio" name="tel"  value='phone' id='phone' checked>
            		<i class="icon icon-form-checkbox icon-default"></i>
            		<br>
            		默认
            	</label>         	
            </div>
          </div>
        </div>
      </li>
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon required">*</i></div>
          <div class="item-inner">
            <div class="item-title label">意向户型</div>
            <div class="item-input">
              <input type="text" placeholder="请选择" @click='show=!show' :value='type'>
            </div>           
          </div>
        </div>
      </li>
      <li class="align-top">
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-comment"></i></div>
          <div class="item-inner">
            <div class="item-title label">备注</div>
            <div class="item-input">
              <textarea></textarea>
            </div>
          </div>
        </div>
      </li>
    </ul>
	 <popup  v-model='show' height='200px'>
      <header class="bar bar-nav" >
         <div @click='show=!show' class="close" >
          <div class="button pull-right" >确定</div>
         </div>
         <h1 class="title">意向户型</h1>
         
      </header>
       <picker  :data='houseType' v-model='type' ></picker>
 </popup>
  
</div>
</template>
<script type='text/ecmascript-6'>
import Popup from './popup/index.vue'
import Picker from './picker/index.vue'
export default{
	data(){
		return{
			type:[],
      show:false,
      houseType:[['dd','ds']]

			
		}
	},
	components:{
		 Popup,
     Picker
	},
	created(){
		 this.$store.commit('footer',false);
	},
	methods:{
	
		}
	}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.list-block {
	margin: 0px auto 8px;
	.item-content{
		font-size: 14px;
		input{
			font-size: 14px;
		}
	}
}
.form-wrap{
	min-height: 600px;
	margin-top: 8px
}
.item-after{
	text-align: center;
	font-size: 12px
}
.hide{
	opacity: 0
}

.form-sex input[type="radio"]{
	display: none;
}
.form-sex .icon{
    font-family: "iconfont-sm" !important;
    font-style: normal;
    display: inline-block;
    vertical-align: middle;
    background-size: 100% auto;
    background-position: center;
    -webkit-font-smoothing: antialiased;
    -webkit-text-stroke-width: 0.2px;
    -moz-osx-font-smoothing: grayscale;
}
.form-sex i.icon-form-checkbox{
	width: 1.1rem;
    height: 1.1rem;
    position: relative;
    border-radius: 1.1rem;
    border: 1px solid #c7c7cc;
    box-sizing: border-box
}
.form-sex i.icon-default{
	width: 0.8rem;
    height: 0.8rem;
}
.form-sex i.icon-form-checkbox:after{ 
  content: ' ';
    position: absolute;
    left: 50%;
    margin-left: -0.3rem;
    top: 50%;
    margin-top: -0.2rem;
    width: 0.6rem;
    height: 0.45rem;
  }
.form-sex input[type="radio"]:checked + i.icon-form-checkbox{
	background: @color-primary;
	    border: none;
}

.form-sex input[type="radio"]:checked + i.icon-form-checkbox:after{
	    background: no-repeat center;
  

    background-size: 0.6rem 0.45rem;
}


</style>