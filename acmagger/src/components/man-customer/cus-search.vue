<template>
 <div>
	<top></top>	
	<div class="bar bar-header-secondary">
  <div class="searchbar">
    <a class="searchbar-cancel">取消</a>
    <div class="search-input">
      <label class="icon icon-search" for="search"></label>
      <input type="search" id='search' placeholder='输入关键字...'/>
    </div>
  </div>
</div>

</div>
</template>
<script type='text/ecmascript-6'>
import Top from './top.vue';
import cusSearch from './search.vue';
export default{	
	components:{
		Top,
		cusSearch
	},
	created(){
		this.$store.commit('COMM_CONF',{
                isFooter:false,
            });
	}
}

</script>

<style lang='less' scoped>


</style>