<template>
<div>
	<div class="card" v-for='team in cuslist'>
		头部
	    <div class="card-header" >
	    	<label class="label-checkbox item-content checkall">
		      <input type="checkbox" name="cus-share"> 
		      <div class="item-media"><i class="icon icon-form-checkbox"></i>
		      {{team.teamer}}
		      </div>
		      </label>
			{{(team.team).length}}人
	    </div>

		列表

	    <div class="content list-block media-list cus-over-item" >		   
	     		<mt-loadmore  :bottom-method="loadBottom" :bottom-all-loaded="allLoaded" ref="loadmore">
	     		<ul>
			<ul >	      	
	      	  <li v-for='teams in team.team'>
	      	    <label class="label-checkbox item-content">
	      	      <input type="checkbox" name="">
	      	      <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
	      	      <div class="item-inner">
	      	        <div class="item-title-row">
	      	          <div class="item-title">{{teams.name}}
						<span class="tag pull-right">看房</span>
						<p class="tel">{{teams.tel}}</p>
	      	          </div>
	      	          <div class="item-after">逾期天数：{{teams.days}}</div>
	      	        </div>
	      	      </div>
	      	    </label>
	      	  </li>
	      	 
	      	</ul>
	  		<mt-spinner type="triple-bounce"   color="#26a2ff"></mt-spinner>
	   
	        <div class="loading "><div style="height:122px"></div></div>
	    </div>
	
	  </div>		

</div>
</template>

<script type='text/ecmascript-6'>
import {mapGetters,mapActions} from 'vuex';
import Vue from 'vue';
/*import { Loadmore } from 'mint-ui';
Vue.component('mt-loadmore', Loadmore);*/
import { InfiniteScroll, Spinner } from 'mint-ui';
Vue.component('mtSpinner', Spinner)
Vue.use(InfiniteScroll);
export default{
		data(){
			return{
				loading:false
			}
		},
		props:['cuslist'],
		methods:{
			loadMore(){
				this.loading = true;
				var _this=this;
				  setTimeout(() => {
				   		var newlist=_this.cuslist[0].team;
				   		_this.cuslist[0].team=newlist.concat(newlist)

				    this.loading = false;
				  }, 5000);
			}
		}
}

</script>

<style lang='less' scoped>
@import '../../../common/style/mixins.less';
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
	min-height: 400px;
	background: #fff;
	margin-top: 44px;
}

</style>