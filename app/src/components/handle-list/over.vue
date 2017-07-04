<template>
<div>
	<div class="content overList" :style='marginTop'>
		<slot></slot>	
	 </div>	
</div>
</template>

<script type='text/ecmascript-6'>
import { parentMixin } from './over-items'

export default{
	 mixins: [parentMixin],
	 props:['saleGroup','marginTop'],
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
  	
  }

  }


</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';
.over-wapper{
	position: relative;
}
.overList{
	margin-top: 44px;
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