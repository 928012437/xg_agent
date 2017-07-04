<template>
    <div>
	
	<div class="list-block media-list content">
	<mt-loadmore  :bottom-method="loadBottom" :bottom-all-loaded="allLoaded" ref="loadmore">
			<ul>
			  <li v-for='(customer,index) in selectItems.list'>
			    <label class="label-checkbox item-content">
			      <input type="checkbox" name="cus-share" v-model='customer.checked' :value='index'>
			      <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
			      <div class="item-inner">
			        <div class="item-title-row">
			          <div class="item-title">
			          		{{customer.name}} 
			          		<span class="tag pull-right" v-for='(tag,index) in customer.tags'>
			          		{{tag}}</span> 	 		
							<p class="tel">{{customer.tel}}</p>
			          </div>
			          <div class="item-after">
			         		 时间：{{customer.date}} <br>	
			         		 原置业顾问：{{customer.sales}}
			          </div>
			        </div>
	       
			      </div>
			    </label>
	 		  </li>	  
			</ul>
			
	</mt-loadmore>
	<div style="display:none">sfsdf{{checkedGroups}}</div>
	<div style="height:20px"></div>
	
	</div>
	
	<nav class="bar bar-tab check-all-bar ">
			<div class="col-33">
				<label class="label-checkbox item-content" >
	          	<input type="checkbox" v-model='selectAll'/>
						
	        	<div class="item-media">
		       	 	<i class="icon icon-form-checkbox"></i>
		       	 	&nbsp;
		       	 	全选
	       		</div>
	  			 </label> 
			</div>
	     	<div class="col-33" @click="delCustomer(checkedGroups)">
	     		回收
	     	</div>
	     	<div class="col-33" @click='assign'>
	     		分配
	     	</div>
	     	
	</nav>
  </div>
	
   
</template>
<script type='text/ecmascript-6'>
import {mapGetters,mapActions,mapMutations} from 'vuex';
import Vue from 'vue';
import { Loadmore,Toast } from 'mint-ui'; 
Vue.component('mtLoadmore', Loadmore);
 
export default{
	data(){
		return {
			checkCustomer:{
				//是否全选了
				isCheck:0,
				//选中的客户数组
				checkedGroup:[],
				//加载的属于数组
				loadedGroup:[]
			}
		}
	},
	components:{
		mtLoadmore:Loadmore
	},
	props:['selectItems'],
	created(){
		// 注入数据
		
	},
	computed:{
		selectAll: {
		    //判断是否全选 当选中的长度跟 数组的长度相等时 
		    get: function() {
		        return this.selectCount == this.selectItems.list.length;
		    },
		    //设置全选和反选
		    set: function(value) {
		        this.selectItems.list.forEach(function(item) {
		            item.checked = value;
		        });

		        this.checkCustomer.isCheck=value?1:0;
		        return value;
		    }
		},
		//选中的数量
          selectCount: {
              get: function() {
                  var i = 0;
                  this.selectItems.list.forEach(function(item) {
                      if (item.checked) {
                          i++;
                      }
                  });
                  return i;
              }
          },
          //选中的数组
          checkedGroups: {
              get: function() {
                  var checkedGroups = [],checkedCustomer=this.checkCustomer,
                  vm=this,dataLength=this.selectItems.list.length;
                  for(let i=0;i<dataLength;i++){
                  	//所有加载出来的客户id
                  	checkedCustomer.loadedGroup.push(this.selectItems.list.id);
                  }

                  this.selectItems.list.forEach(function(item) {
                  
                  	//未加载完 并且 全选
                  	if(!(dataLength<vm.selectItems.length&&!!checkedCustomer.isCheck)){
                  		 checkedCustomer.loadedGroup=[]
                  	}                     
                     if (item.checked) {
                          checkedGroups.push(item.id);
                          checkedCustomer.checkedGroup;
                      }
                  });
                  checkedCustomer.checkedGroup=checkedGroups;
                  return checkedGroups;
              }
          },
          ...mapGetters([
          	'shareCustomer'
          ])
	},
	methods:{
		//删除客户
		delCustomer(arr){
			/*var customers =this.selectItems;
			if(this.selectCount<=0){
				alert('请选择至少1个')
			}
			for(let i=0;i<arr.length;i++){
				this.selectItems.splice(arr[i],1)
				// alert(arr[i])
				
			}			*/
		},
		// 分配客户
	    assign(){
	   		// 判断是否有选择客户
	   		
	   		if(this.checkCustomer.checkedGroup.length<=0){
	   			Toast('请选择客户');
	   		}else{
	   			 	// 获取选中数据	   		
	   			this.$store.commit('CUS_CHECK_SHARE',{
	   				isCheck:  this.checkCustomer.isCheck,
	   				checkedGroup:this.checkCustomer.checkedGroup,
	   				loadedGroup:this.checkCustomer.loadedGroup
	   			});

	   			this.$router.push({ path: '/customer/assign/overdue' })
	   			/*alert(this.shareCustomer.checkedGroup)
	   			*/
	   		}
			

	   		
	   		
	   	},
	   // 下拉刷新
	   loadBottom(){
	   	// 下拉的加载api 参数：([公共的/无效的],是否全选)
	   	alert(this.$route.name)
	   	this.getData(!!this.checkCustomer.isCheck)
	   	this.allLoaded = true;// 若数据已全部获取完毕
  		this.$refs.loadmore.onBottomLoaded();
	   }, 
		   getData(isCheck){

		   }

	 
	},

	
}

</script>

<style lang='less' scoped>
@import '../../../common/style/mixins.less';

.list-block{
	margin-top: 88px;
}
.item-title{
	.tag{
		font-size: 10px;
		background:  #0894ec;
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
	font-size: 12px;
	margin-top: 6px;
	color:#999;
}
.check-all-bar{
	.flexbox;
	z-index: 999;
	.icon-form-checkbox{
		margin-top: -6px;
	}
	div.col-33{
		text-align: center;
		line-height: 50px;
		.flex(1);
		&:last-child{
			background:@color-primary;
			color:#fff;
		}
		&:nth-child(2){
			background:Lighten(@color-primary,25%);
			color:#fff;
		};
	}
}

</style>