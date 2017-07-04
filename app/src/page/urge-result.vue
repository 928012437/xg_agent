<template>
 <div class="content">
	<top></top>	
	<div class="content margin-top">
	<form action="">
		<div class="list-block">
		    <ul>
		      <!-- Text inputs -->
		      <li>
		        <div class="item-content">
		          <div class="item-media"><i class="icon icon-form-name required">*</i></div>
		          <div class="item-inner">
		            <div class="item-title label">催办结果</div>
		            <div class="item-input">
		              <input type="text" placeholder="请输入" name='' v-model="result" >
		            </div>
		          </div>
		        </div>
		        </li>
		         <li >
		          <div class="item-content">
		            <div class="item-media"><i class="icon icon-form-name hide">*</i></div>
		            <div class="item-inner">
		              <div class="item-title label">下次跟进</div>
		              <div class="item-input">
		                  <datetime v-model="xiacitime" format="YYYY-MM-DD HH:mm"  title="start time" ></datetime>
		               
		              </div>
		            </div>
		          </div>
		        </li>
		         <li>
		        <div class="item-content">
		          <div class="item-media"><i class="icon icon-form-name hide">*</i></div>
		          <div class="item-inner">
		            <div class="item-title label">预期原因</div>
		            <div class="item-input">
		              <input type="text" placeholder="请输入" name='' v-model="content" >
		            </div>
		          </div>
		        </div>
		        </li>
		     </ul>
		    
		  </div>
		  </form>
	</div>
	<button class="bottom-btn bar bar-tab" @click="setcblog" >
		保存
	</button>

</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import datetime from '../components/datetime/index.vue'
import axios from 'axios';

export default{
	data(){
		return{
			content:'',
			xiacitime:'',
			result:''
		}
	},
	components:{
		Top,
		datetime
	},
	created(){
			 this.$store.commit('footer',false);
	},
	methods:{
		setcblog(){
			var id=this.$route.params.id;
			var that=this;
			axios.post(urlstr + 'user.counselors.setcblog&id='+id+'&content='+this.content+'&xiacitime='+this.xiacitime+'&result='+this.result).then(function (res) {
				console.log(res)
				if (res.data.code == 4) {
					if (res.data.status == 1) {
						that.$router.push('/addons/xg_agent/app/business/urgeinfo/'+id);
					}
				}
			});
		}
	}

}

</script>

<style lang='less' scoped>

.list-block {
		margin: 0px auto;
		.item-content{
		font-size: 14px;
		input{
			font-size: 14px;
			}
	}
}
.button{
	margin: 30px auto;
	padding: 0 2.2rem
}
</style>