<template>
 <div class="content">
	<top></top>	
	<div class="bar bar-header-secondary">
  <div class="searchbar">
    <span class="searchbar-cancel" :class="{'searchbarOn':focus}"
     > 搜索</span>
     <span class="si" @click='getData(param)'>搜索</span>
    <div class="search-input">

      <label class="icon icon-search" for="search" @click='getData'></label>
      <input type="search" id='search' placeholder='输入关键字...' 
      :class="{'searchOn':focus}" @focus='focus=true' v-model='param'/>
    </div>
  </div>
</div>
	 <div class="content customer-list">
		<cus-list></cus-list>
		<div>{{param}}</div>
	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
import cusList from '../components/cus-list.vue';
export default{	
	components:{
		Top,
		cusList,	
	},
	data(){
		return{
			focus:false,
			customerData:[],
			param:''

		}
	},
	created(){
		 this.$store.commit('footer',false);
		
	},
	methods:{
		getData(param){
			if(param!==''){
				alert(param)
			}
			
		}
	}
}

</script>

<style lang='less' scoped>
.customer-list{
	top:4.4rem;
}
.searchOn{
	width: 88%;
}
.searchbarOn{
	opacity: 1;
	transform: translate3d(-54px, 0, 0);
}
.searchbar-cancel{
	
}
.si{
	position: absolute;
	right:0;
	z-index: 88;
	opacity: 0; 
	color: red;
}

</style>