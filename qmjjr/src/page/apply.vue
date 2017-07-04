<template>
 <div class="content">
 <top></top>
 <div class="content apply-content margin-top">
	<div class="card apply-card">
	    <div class="card-header">我的可提现佣金</div>
	    <div class="card-content">
	      <div class="card-content-inner pay-content">
	      	￥ {{data.num2}}
	      </div>
	    </div>
	    <div class="card-footer">
	    	<button class="button apply-btn button-big" @click="tixianpost" >我要提现</button>
	    </div>
	 </div>
</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
import axios from 'axios';

export default{
	data(){
	return{
		data:[]
	}
	},
	components:{
		Top
	},
	created(){
		this.$store.commit('footer',false);
		this.gettixianinfo();
	},
	methods:{
		gettixianinfo(){
			var that = this;
			axios.post(urlstr + 'qmjjr.gettixianinfo').then(function (res) {
				if (res.data.code == 3) {
					that.data = res.data.data;
				}
			});
		},
		tixianpost(){
			var that = this;
			if(that.data.num2<1){
				alert('提现金额需大于一元。');return;
			}else {
				axios.post(urlstr + 'qmjjr.tixianpost').then(function (res) {
					if (res.data.code == 4) {
						if(res.data.status==1){
							alert('提现成功请等待审核。');
							that.$router.push('/addons/xg_agent/qmjjr/home');
						}
					}
				});
			}
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/variables.less';
.apply-card{
	/*box-shadow: none;*/
}
.apply-btn{
	margin: 0 auto;
}
.pay-content{
	font-size: 24px;
}
.apply-btn{
	padding: 0 2rem;
	background: @color-primary;
	color:#fff;
}

</style>