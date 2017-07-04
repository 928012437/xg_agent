<template>
<div >
	<div class="content overList">
		<slot></slot>		
	 </div>
	 <nav class="bar bar-tab check-all-bar ">
			<div class="col-33">
				已选
			</div>
	     	<div class="col-33" >
	     		回收
	     	</div>
	     	<div class="col-33" @click='assign'>
	     		分配
	     	</div>
	     	
</nav>
	
</div>
</template>

<script type='text/ecmascript-6'>
import { parentMixin } from './over-items'
import { Toast } from 'mint-ui';

export default{
	 mixins: [parentMixin],
	 props:['saleGroup'],
	 mounted () {
    // stop bar anmination on first loading
    setTimeout(() => {
      this.hasReady = true
    }, 0)
  },
 
  data(){
		return{
			
		}
	},
  computed:{ },
  methods:{
  	la(){
  		 Toast('请选择客户');
  	}
  }

  }


</script>

<style lang='less' scoped>
@import '../../../common/style/mixins.less';
.over-wapper{
	position: relative;
}
.overList{
	margin-top: 142px;
}
.check-all-bar{
	.flexbox;
	z-index: 999;
	.icon-form-checkbox{
		margin-top: -6px;
	}
	div.col-33{
		text-align: center;
		line-height: 50px;
		.flex(1);
		&:last-child{
			background:@color-primary;
			color:#fff;
		}
		&:nth-child(2){
			background:Lighten(@color-primary,25%);
			color:#fff;
		};
	}
}


</style>