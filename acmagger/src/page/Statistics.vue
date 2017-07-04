<template>
	<div >
		
			
		<div class="content sta-wapper">
			<sta-header></sta-header>
		 	<router-view></router-view>				
		</div>
			<div class="spacing"></div>
	
	</div>
</template>
<script>
import staHeader from '../components/header.vue'


export default{
	components:{
		staHeader,
		 
	},
	created(){
		this.$store.commit('COMM_CONF',{
                isFooter:true, 
            });
	},
	data(){
		return{
			value:{
				v:""
			}
		}
	}

}

</script>

<style lang='less'>
/* .spacing{
  height: 200px;
  width: 100%;
  background: #fff;
} */
	.sta-wapper{
		/*overflow: hidden;*/
	}

</style>