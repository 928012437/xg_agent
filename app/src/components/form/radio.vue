<template>
 <div>
	<li>
	  <div class="item-content">
	    <div class="item-media"><i class="icon icon-form-email hide">*</i></div>
	    <div class="item-inner">
	      <div class="item-title label">{{radioData.title}}</div>
	      <div class="item-input">
	     
	      	<label :for="option.value" class="form-sex" v-for='option in options'>
	      		<input type="radio"   :name="radioData.name" :value='option.value'  :id='option.value'  v-model='value'>
	      		<i class="icon icon-form-checkbox" ></i>
	      		&nbsp;{{option.name}}}
	      	</label>
	        	&nbsp;&nbsp;&nbsp;
	        	<!-- <label for="woman" class="form-sex">
	        		      		<input type="radio" name="sex"  value='woman' id='woman' v-model='value'>
	        		      		<i class="icon icon-form-checkbox"></i>
	        		      		&nbsp;女
	      	</label> -->
	      </div>
	    </div>
	  </div>
	</li>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:[],
	data(){
		return{
			radioData:{
				title:'选项名',
				name:'n',
				options:[
					{
						name:'男性',
						value:'man'
					},
					{
						name:'女性',
						value:'woman'
					}
				]
			}
		}
	}
}

</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';

 input[type="radio"]{
	display: none;
}
 .icon{
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
 i.icon-form-checkbox{
	width: 1.1rem;
    height: 1.1rem;
    position: relative;
    border-radius: 1.1rem;
    border: 1px solid #c7c7cc;
    box-sizing: border-box
}
 i.icon-default{
	width: 0.8rem;
    height: 0.8rem;
}
 i.icon-form-checkbox:after{ content: ' ';
    position: absolute;
    left: 50%;
    margin-left: -0.3rem;
    top: 50%;
    margin-top: -0.2rem;
    width: 0.6rem;
    height: 0.45rem;}
 input[type="radio"]:checked + i.icon-form-checkbox{
	background: @color-primary;
	    border: none;
}

 input[type="radio"]:checked + i.icon-form-checkbox:after{
	    background: no-repeat center;

    background-size: 0.6rem 0.45rem;
}

</style>