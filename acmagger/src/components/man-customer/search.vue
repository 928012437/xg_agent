<template>
    <div>
		<div class="bar bar-header-secondary">
		  <div class="searchbar">
		    <router-link :to="'/customer/assign/search/'+type" >
		    <div class="search-input">
		      <label class="icon icon-search" for="search"></label>
		      <input type="search" id='search' placeholder='输入关键字...'
		      :disabled='dd' />
		    </router-link>
		    </a>
		  </div>
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
	 	}
	 }
	 
	

}

</script>

<style lang='less' scoped>


</style>