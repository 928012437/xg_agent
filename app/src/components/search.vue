<template>
<div class="bar bar-header-secondary" :style="{top:height}">
	<div class="searchbar">
		 <router-link to="/addons/xg_agent/app/addons/xg_agent/app/customer/search" >
		 <div class="search-input">
		      <label class="icon icon-search" for="search"></label>
		      <input type="search" id='search' placeholder='输入关键字...'
		      :disabled='dd' />
		 </router-link>
 	</div>
</div>

 
</template>
<script type='text/ecmascript-6'>

export default{
	 props:{
	 	type:String,
	 	dd:{
	 		type:Boolean,
	 		default:true
	 	},
	 	height:{
	 		type:String,
	 		default:"1px"
	 	}
	 }
	 
	

}

</script>

<style lang='less' scoped>
.bar-header-secondary{
	top:1px;
}

</style>