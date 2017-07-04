<template>
 <div>
	<div class="card" v-for='input in checkData'>
    <div class="card-header">{{input.title}}</div>
    <div class="card-content">
      <div class="card-content-inner">
      	<label :for="input.name+'-'+index" class="checkbox" v-for='(item,index) in input.value'>
      		<input type="checkbox" :id="input.name+'-'+index" :value='item.id' :name='item.name' :checked="item.checked" >
      		<div class="checkbtn">{{item.value}}</div>
      	</label>
      </div>
    </div>

  </div>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	props:['checkData'],
	data(){
		return{

		}
	},
	created(){

	},
	computed:{

	}

}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

.card{
	margin:0 0 8px;
	box-shadow: none;
	.card-header{
		font-size: 12px;
		padding: 0.2rem 0.5rem;
		min-height:1.6rem;
	}
	.card-content-inner{
		padding: 0.6rem 0 0
	}
}
.checkbox{
	display: inline-block;
	border: 1px solid @color-split;
	border-radius: 5px;
	width:30%;
	height: 1.8rem;
	margin: 0 0 0.4rem 2%;
	position: relative;
	overflow: hidden;
	input[type='radio'],input[type='checkbox']{
		display: none;
	}
	input[type='radio'] + div.checkbtn,input[type='checkbox'] + div.checkbtn{
			width: 100%;
			height:100%;
			background-color: transparent;
			position: absolute;
			top: 0;
			text-align: center;
			color:@color-text-secondary;
			line-height:1.8rem 
	}
	input[type='radio']:checked + div.checkbtn,input[type='checkbox']:checked + div.checkbtn{
			background-color: @color-primary;
			color: #fff;
			
	}
}
@media only screen and (-webkit-min-device-pixel-ratio: 2){
	.card-header:after{
	    transform: scaleY(0.58);
	}
}

</style>