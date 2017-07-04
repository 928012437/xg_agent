<template>
 <div class="content">
	<top></top>
	<div class="content margin-top revise-content">
	<div class="list-block ">
	<form action="">
    <ul>
      <!-- Text inputs -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-name"></i></div>
          <div class="item-inner">
            <div class="item-title label">姓名</div>
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
            <div class="item-title label">手机号</div>
            <div class="item-input">
              <input type="email" placeholder="请输入">
            </div>
          </div>
        </div>
      </li>
      </ul>
      </form>
      <div class="prompt">
	      提示：
	      <p>为了您能快速结佣请提供准确的信息，请勿频繁修改
		  </p>
	  </div>
    </div>

</div>
<button class="bottom-btn bar bar-tab">
	保存
</button>
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
@import '../style/less/variables.less';
.revise-content{
	.list-block{
		margin-top: 0;
	}
	.item-content{
		font-size: 14px;
		input{font-size: 14px;}
	}
}
.prompt{
	color: @color-text-gray;
	font-size: 14px;
	background-color: #fff;
	padding: 10px 15px;
	p{
		margin: 0
	}
}
</style>