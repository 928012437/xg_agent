<template>
<div>

<div class="card" :class='{index:selected}' >
		<!-- 头部 -->
	    <div class="card-header" @click='onItemClick' >
	    	<div><span class="icon icon-caret"></span>{{dataItem.title}}</div>
	    </div>

		<!-- 列表 -->
	    <div class="content list-block media-list cus-over-item" v-show='selected'>		  
			<ul  v-infinite-scroll="loadMore"
				  infinite-scroll-disabled="loading"
				  infinite-scroll-distance="25">	      	
	      	  <li v-for='customer in cusList'>
	      	    <label class="label-checkbox item-content">
					<div class="item-inner"  v-if="pageName=='bussiness'">
					<router-link :to="'/business/urgeinfo/'+customer.id">
						   <div class="item-title-row">
						     <div class="item-title">{{customer.name}}
						      	<span class="tag pull-right">{{customer.gj_jb}}</span>
						      	<div class="explain">
									{{customer.gj_nr}}:{{customer.gj_time}}
						      	</div>
						     </div>
						     <div class="item-after">
						      	         	
						     </div>
						   </div>
					</router-link>	   
					</div>

				<div class="item-inner" v-if="pageName=='remind'">
				<router-link :to="'/customer/cusinfo/'+customer.id">
	      	       <div class="item-title-row">
	      	         <div class="item-title">{{customer.name}}
	      	     			<span class="tag pull-right" >{{customer.gj_jb}}</span>
	      	     			<div class="explain">
	      	     				{{customer.createtime}}
	      	     				<span class="way">{{customer.mark}}</span>
	      	     			</div>
	      	         </div>
	      	         <div class="item-after">
	      	         	<router-link to='/customer/cusinfo/35'>
	      	         	<span class="icon icon-phone"></span>
	      	         	</router-link>
	      	         </div>
	      	       </div>
	      	       </router-link>
	      	     </div>
	      	    </label>	      	    
	      	  </li>
	      	 
	      	</ul>
	  	
	      
	    </div>
	
	  </div>

	
</div>
</template>

<script type='text/ecmascript-6'>
import {mapGetters,mapActions} from 'vuex';
import Vue from 'vue';
import { InfiniteScroll, Spinner } from 'mint-ui';
Vue.component('mtSpinner', Spinner)
Vue.use(InfiniteScroll);

import { childMixin } from './over-items'
export default{
		mixins: [childMixin],
		data(){
			return{
				loading:false,
				show:'remind'
			}
		},		
		props:['dataItem','pageName'],
		methods:{
			
		},
		computed:{
		
         },

         


}


</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';
.card{
		margin: 0;
		margin-top: 4px;
		box-shadow: none;
		.card-header{
			border-bottom:1px solid  @color-split;
		}
		.list-block{
			padding: 0;
		}
	}
	.loading{
		width: 100%;
		text-align: center
	}
.cus-over-item{
	min-height: 486px;
	background: #fff;
	margin-top: 44px;
	
}
.icon-caret{
	color: @color-primary;
	transform: rotate(-90deg);
	transition:transform 0.3s;
}
.index{
	z-index: 98;
	.icon-caret{
		transform: rotate(0deg);
	}
}

.explain{
	font-size: 0.8em;
	color: @color-text-gray;
	.way{
		display: block;
	}
}

.item-title{
	color: @color-text;
  .tag{
    font-size: 10px;
    background: @color-primary;
    color: #fff;
    padding:0 1px;
    border-radius: 2px;
    margin-left: 4px;
    margin-top: 2px;
  }
  p.tel{
    margin: 0
  }
}
.item-after{
	margin-top: 20px;
	margin-right: 1em;
   font-size: 2em;
  color:@color-primary;
  transform: rotate(-90deg);
}

</style>