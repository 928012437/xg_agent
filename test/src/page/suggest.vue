<template>
 <div class="content">
	<top></top>
	<div class="content suggenst-content margin-top">
		<div class="content">
		  <div class="list-block">
		  <form action="">
		    <ul>
		      <!-- Text inputs -->
		      <li>
		        <div class="item-content">
		          <div class="item-media"><i class="icon icon-form-name"></i></div>
		          <div class="item-inner">
		            <div class="item-title label">手&nbsp;&nbsp;&nbsp;&nbsp;机&nbsp;&nbsp;&nbsp;&nbsp;号:</div>
		            <div class="item-input">
		              <input type="text" placeholder="请输入手机号码">
		            </div>
		          </div>
		        </div>
		      </li>
		      <li class="align-top">
		             <div class="item-content">
		               <div class="item-media"><i class="icon icon-form-comment"></i></div>
		               <div class="item-inner">
		                 <div class="item-title label">投诉和建议:</div>
		                 <div class="item-input">
		                   <textarea></textarea>
		                 </div>
		               </div>
		             </div>
		           </li>
		      </ul>
		      </form>
		      </div>
	</div>
	<button class="bottom-btn bar bar-tab">
		提交
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
.list-block{
	margin: 0;
	.item-content{
		font-size: 14px;
		input{
			font-size: 14px;
		}
	}
}

</style>