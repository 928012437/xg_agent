<template>
 <div>
 <mt-loadmore :bottom-method="loadBottom" :bottom-all-loaded="allLoaded" ref="loadmore" bottomPullText='上拉刷新' :autoFill=false >
		<ul class="cus-list">
  		<li v-for='cus in cusData' class="customer">
  			<div class="card">
  			    <div class="card-header">
  			    	<div class="cus-router">
  			    		<router-link  class='customer-link' :to="'/addons/xg_agent/app/customer/cusinfo/'+cus.id" >
  			    		{{cus.name}} <span class="tag pull-right" >{{cus.gj_jb}}</span>
  			    		<span>{{cus.tel}}</span>
  			    		</router-link>
  			    	</div>
  			    	<div class="cus-handle">
						<div class="pull-left follow">
	  			    	  <label :for="cus.id" >
	  			    	  		<input type="checkbox" :id='cus.id' v-model='cus.is_gz' >
	  			    	  		<i class="iconfont " @click="change_gz(cus.id,cus.is_gz)">&#xe61e;</i>
	  			    	  </label>
						</div>
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
  	</mt-loadmore>
  
</div>
</template>
<script type='text/ecmascript-6'>
import Vue from 'vue';
import { Loadmore } from 'mint-ui';
Vue.component(Loadmore.name, Loadmore);
import axios from 'axios';

export default{
	props:['cusData','status','ordertype'],
	data(){
		return{
			allLoaded:false,
			page:1,

		}
	},
	methods:{
		loadBottom(){

				this.page++;
				this.getcustomerlist();


		},
		change_gz(id,gz){
			axios.post(urlstr + 'user.counselors.changegz&id='+id+'&is_gz='+gz).then(function (res) {
			});
		},
		getcustomerlist(){
			var that = this;
			axios.post(urlstr + 'user.counselors&page=' + this.page + '&status=' + this.status + '&ordertype=' + this.ordertype).then(function (res) {
				if (res.data.code == 3) {
					res.data.data.forEach(function(v){
						that.cusData.push(v);
					})
					if(res.data.data.length<8){
						that.allLoaded = true;// if all data are loaded
						that.$refs.loadmore.onBottomLoaded();
					}
				}
			});
		}
	}
}


</script>

<style lang='less' scoped>
@import '../style/less/variables.less';
.cus-list{
	margin: 0
}
		.customer{
			/*margin-bottom: 10px;*/
			.card{
				margin: 0;
				box-shadow: none;
				color: @color-text;
			}
			.card-header{
				font-size: 18px;
				 .tag{
				    font-size: 10px;
				    background: @color-primary;
				    color: #fff;
				    padding:0 1px;
				    border-radius: 2px;
				    margin-left: 4px;
				    margin-top: 3px;
				  }
				  .cus-router{
				  	/*width: 40%;*/
				  }
				  a.customer-link{
				  	color: @color-text;
				  }
				  .follow,.call{
				  		padding: 0 10px;
				  }
				  .follow{
				  		
				  		margin-right: 5px;
				  		border-right: 1px solid @color-split;
				  		input[type="checkbox"]{
				  			display: none;
				  		}
				  		input[type="checkbox"] + i.iconfont{
								color:@color-text-gray;
				  		}
				  		input[type="checkbox"]:checked + i.iconfont{
				  				color: orange;
				  		} 
				  }
				 

			}
			.card-content-inner{
				 font-size:14px;
				padding: 8px;
			}
			.card-header{
				span{
					display: block;
				}
			}
			.card-footer{
				background:Lighten(@color-text-gray-light,18%)
			}
		}

</style>