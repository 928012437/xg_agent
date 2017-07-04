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
			          		<span class="tag pull-right">
			          		{{customer.status}}</span>
							<p class="tel">{{customer.tel}}</p>
			          </div>
			        </div>
			        <div class="item-subtitle">
			        	<div>{{customer.mark}}</div>
			        	<div>{{customer.createtime}}</div>
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
	     	
	     	<div class="col-33" @click='assign'>
	     		分配
	     	</div>
	     	
	</nav>
	<div>{{checkedGroups}}</div>
  </div>
	
   
</template>
<script type='text/ecmascript-6'>
import {mapGetters,mapActions,mapMutations} from 'vuex';
import Vue from 'vue';
import { Loadmore } from 'mint-ui'; 
import { Toast } from 'mint-ui';
Vue.component('mtLoadmore', Loadmore);
 
export default{
	data(){
		return {
			allLoaded:false,
			allCheck:false,
			checkCustomer:{
				//是否全选了
				isCheck:0,
				//选中的客户数组
				checkedGroup:[],
				//加载的属于数组
				loadedGroup:[]
			},
			datas:'kyb'
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
		    	 this.checkCustomer.isCheck=this.selectCount == this.selectItems.list.length?1:0;
		    	
		        return this.selectCount == this.selectItems.list.length;
		    },
		    //设置全选和反选
		    set: function(value) {
		        this.selectItems.list.forEach(function(item) {
		            item.checked = value;
		        });

		        this.checkCustomer.isCheck=value?1:0;
		        this.allCheck=value;
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
                  this.selectItems.list.forEach(function(item) {
                     if (item.checked) {
                          checkedGroups.push(item.id);
                         
                      }
                  });
                  checkedCustomer.checkedGroup=checkedGroups;
                  console.log(checkedGroups);
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
	   			Toast({
	   			  message: '请选择客户',
	   			  position: 'middle',
	   			  duration: 2000
	   			});
	   		}else{
	   			//判断是否加载完成
	   			let dataLength=this.selectItems.list.length;
               		 if(dataLength<this.selectItems.length&&!!this.allCheck&&!this.checkCustomer.isCheck){
               		 	for(let i=0;i<dataLength;i++){
	                  	    this.checkCustomer.loadedGroup.push(
	                  	    	this.selectItems.list[i].id
	                  	    	);
                  	    }
                 }    
	   			this.$router.push({name: '拷贝客户',params:{ data:
	   				this.checkCustomer }})
	   			/*, params: { data:
	   				this.checkCustomer }*/
	   		}	
	   	},
	   // 下拉刷新
	   loadBottom(){
	   	// 下拉的加载api 参数：([公共的/无效的],是否全选)
	   	if(this.selectItems.list.length<8){return false};
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
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

.list-block{
	margin-top:0px;
}
.item-title{
	.tag{
		font-size: 10px;
		background:  @color-primary;
		color: #fff;
		padding:0 2px;
		border-radius: 2px;
		margin-left: 4px;
		margin-top: 2px;
	}
	p.tel{
		margin: 0
	}
}
.item-subtitle{
	.flexbox;
	.justify-content(space-between);
	background-color: Lighten(@color-text-gray-light,10%);
	padding: 0 8px;
	margin-top: 4px;
	color:@color-text-gray;
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
	
	}
}

</style>