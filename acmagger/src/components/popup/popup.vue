<template>
    <div class="content popup-wapper" v-show='show'>
		sdfsdf
	
    </div>
</template>
<script type='text/ecmascript-6'>
export default{
	data(){
		return{
			show:true
		}
	}
}

</script>

<style lang='less' scoped>
	.popup-wapper{
		width: 80%;
		position: absolute;
		left:40%;
		background: rgba(0,0,0,.8);
		z-index:10000
	}

</style>