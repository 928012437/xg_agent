<template>
    <div>
		<div id="wrapper" >
			<slot ></slot>
		</div>

    </div>
</template>
<script type='text/ecmascript-6'  >
 	import  IScroll from 'iscroll';	
	export default{
			data(){
				return{

				}
			},
			props:{
				iscrollX:{
					type:Boolean,
					default:true
				},
				iscrollY:{
					type:Boolean,
					default:true
				},
				h:{
					type:String,
					default:'60'
				}

			},
			created(){

			},
			mounted(){
				this.load();
			},
			methods:{
				load(){				
					const myScroll = new IScroll('#wrapper',{
			   		scrollX: this.iscrollX,
			   		scrollY:this.iscrollY
				})
					
			}
	}
}

</script>

<style lang='less' scoped>
#wrapper{
	width: 100%;
	overflow: hidden;
	position: relative;
	height: 600px;
	/*background: #fff;*/
}

</style>