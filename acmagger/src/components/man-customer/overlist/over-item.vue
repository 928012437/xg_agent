<template>
<div>

<div class="card" :class='{index:selected}'>
		<!-- 头部 -->
	    <div class="card-header" >
	    	<label class="label-checkbox item-content checkall">
		      <input type="checkbox"   v-model='selectAll'> 
		      <div class="item-media"><i class="icon icon-form-checkbox"></i>
		      {{saleItem.teamer}}
		      </div>
		      </label>
			<div @click='onItemClick'>{{saleItem.teamCount}}人</div>
	    </div>

		<!-- 列表 -->
	    <div class="content list-block media-list cus-over-item" v-show='selected'>		  
			<ul  v-infinite-scroll="loadMore"
  infinite-scroll-disabled="loading"
  infinite-scroll-distance="25">	      	
	      	  <li v-for='teams in team'>
	      	    <label class="label-checkbox item-content">
	      	      <input type="checkbox"  v-model='teams.checked'>
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
	  		<!-- <div style="height:40px;"></div> -->
	      
	    </div>
	
	  </div>
<div style="display:none">{{checkedGroups}}</div>
	
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
				//团队的的数据 （待写）
				//用来判断是否全选，决定加载数据的选中状态和是否添加销售id
				isChecked:false,
				checkSale:[],			
				customerList:[],

				selectedIteam:{
					saleId:-1,
					customerId:[],
					loadCustomerId:[]
				}
			}
		},		
		props:['saleItem'],
		methods:{
			
		},
		computed:{
		selectAll: {
		    //判断是否全选 当选中的长度跟 数组的长度相等时 
		    get: function() {
		    	return this.saleItem.checked=this.team.length!==0&&this.selectCount ==this.team.length		        
		    },
		    //设置全选和反选
		    set: function(value) {
		    	if(this.team.length!=0){
		    		this.team.forEach(function(item) {
		            item.checked = value;
		       	 });
		    	}
		    	//判断是否全选，
		    	this.isChecked=value;
		    	//
		    	this.saleItem.checked=value
		        return value;
		    }
		},
		//选中的数量
          selectCount: {
              get: function() {
                  var i = 0;
                  if(this.length!=0){
	                 this.team.forEach(function(item) {
	                      if (item.checked) {
	                          i++;
	                      }
	                 });
	                  return i;
             	}                 
              }
          },
          //选中的数组
          checkedGroups: {//我必须调用才能够执行
              get: function() {
                  var checkedGroups = [], _this=this;

                   if(this.team.length!=0){
                   		 var dataLength=this.team.length;
                 		 for(let i=0;i<dataLength;i++){
                 		 	this.selectedIteam.loadCustomerId.push(this.team.id)
                 		 }
                   
                   this.team.forEach(function(item) {
                   		// 加载的所有客户数据
                   	 	//全选并且 客户数据还没有加载完
                   	 	if(dataLength<_this.saleItem.teamCount&&_this.isChecked){              
                   	 		 _this.selectedIteam.saleId=_this.saleItem.id;

                   	 	}else{
                   	 		//否则我只需要知道选中的客户id
                   	 		_this.selectedIteam.loadCustomerId=[]
                   	 	}
                      if (item.checked) {

                          checkedGroups.push(item.id); 
                      }
                  });   
                   _this.selectedIteam.customerId=checkedGroups;
                  return checkedGroups;
              }}
          },
         
          allSelectCount(){
          		var i = 0;
          		var _this=this;
          	 if(this.team.length!=0){
          		this.datas.forEach(function(handle){
          			_this.team.forEach(function(item){
          			 	if(item.checked){
          			 		i++;
          			 	}
          			 }
          		)})

          		this.checkData.num=i;
          		return i;
          	}
          },
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
	background: green;
}
.index{
	z-index: 98!important;
}
 label.label-checkbox .item-media input[type="checkbox"] {
	display:inline-block;
	opacity: 1;
	/* 
	width: 100%;
	height: 100%; */
	position: absolute;
	/*z-index: 222;*/



}

</style>