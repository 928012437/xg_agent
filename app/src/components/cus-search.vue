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
		 <ul class="cus-list">
			 <li v-for='cus in customerData' class="customer">
				 <div class="card">
					 <div class="card-header">
						 <div class="cus-router">
							 <router-link  class='customer-link' :to="'/addons/xg_agent/app/customer/cusinfo/'+cus.id" >
								 {{cus.name}} <span class="tag pull-right" >{{cus.gj_jb}}</span>
								 <span>{{cus.tel}}</span>
							 </router-link>
						 </div>
						 <div class="cus-handle">
							 <div class="pull-left call">
								 <router-link to=''>
									 <span class="icon icon-phone"></span>
								 </router-link>
							 </div>
						 </div>
					 </div>
					 <div class="card-content">
						 <div class="card-content-inner">{{cus.mark}}</div>
					 </div>
					 <div class="card-footer">
						 <div>{{cus.gj_nr}}</div>
						 <div>{{cus.gj_fs}}:{{cus.gj_xc}}</div>
					 </div>
				 </div>

			 </li>
		 </ul>

	</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue';
import axios from 'axios';

// import cusList from './cus-list.vue';
export default{	
	components:{
		Top,
		// cusList,	
	},
	data(){
		return{
			page:1,
			focus:false,
			customerData:[],
			param:''

		}
	},
	created(){
		 this.$store.commit('footer',false);
		this.getData('');
	},
	methods:{
		getData(sort){
			var that = this;
			axios.post(urlstr + 'user.counselors&page=' + this.page + '&sort='+sort).then(function (res) {
				if (res.data.code == 3) {
					that.customerData=[];
					res.data.data.forEach(function(v){
						that.customerData.push(v);
					})
				}
			});
		}
	}
}

</script>

<style lang='less' scoped>
.customer-list{
	top:3.6rem;
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