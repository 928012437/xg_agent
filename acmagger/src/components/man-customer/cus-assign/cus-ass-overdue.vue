<template>
<div >
<div class="content ">
	 <top></top>
	<cus-search type='overdue'></cus-search>
	<div class="buttons bar">
		<tab>
		 	<tab-item v-on:click.native="param= '未跟进'">
		 		未跟进
				<span>(20)</span>
		 	</tab-item>
		 	<tab-item v-on:click.native="param= '未认购'">
		 		未认购
				<span>(20)</span>
		 	</tab-item>
		 	<tab-item v-on:click.native="param= '未签约'">
		 		未签约
				<span>(10)</span>
		 	</tab-item>
		 	<tab-item v-on:click.native="param= '未交款'">
		 			未交款
				<span>(200)</span>
		 	</tab-item>
		 </tab>
		 </div>
	<div style="display:none">{{checkedSale}}</div>
	<over :saleGroup='saleGroup'>	
	<over-item v-for='(saleItem,index) in saleDate' :saleItem='saleItem'>
	</over-item>
	</over>
	


		


</div>
</template>
<script type='text/ecmascript-6'>
import cusSearch from '../search.vue';
import overItem from '../overlist/over-item.vue';
import Over from '../overlist/over.vue';
import tab from '../../tab/tab.vue';
import tabItem from '../../tab/tab-item.vue';
import Top from '../top.vue';
// import { parentMixin } from '../overlist/over-items.js'


export default{
	 // mixins: [parentMixin],
	data(){
		return{
			param:'未跟进',
			checkData:{
				num:10
			},
			saleGroup:[],//销售人员数组
			saleDate:[
					{
						teamer:'老徐',
						teamCount:10,
						checked:false,
						id:30,
					},
					{
						teamer:'努力吧',
						teamCount:2,
						checked:false,
						id:3,
					},
					
				]
		}
	},
	created(){
		this.$store.commit('COMM_CONF',{
                isFooter:false,
            });
	},
	watch:{
		//导航变更，数据更新,调用数据接收的api
		param(val){
			alert(val)
		}
	},
	methods:{
		//数据接收 api

		//分配数据
		assignData(){

			// this.$store.commit('CUS_CHECK_OVERDUE',this.checkedSaleGrounp)

		}
	},
	computed:{
		//获得的选中的销售的ID
		checkedSale(){
			var checkedSaleGrounp=[];
			this.saleDate.forEach(function(item){
				 if(item.checked){
				 	checkedSaleGrounp.push(item.id)
				 }
			})
			this.saleGroup=checkedSaleGrounp;
			return checkedSaleGrounp;
		}
	},
	components:{
		cusSearch,
		Top,
		 Over,
		overItem,
		tab,
		tabItem
	}
}

</script>

<style lang='less' scoped>
@import '../../../common/style/mixins.less';

	.buttons{
		margin-top: 88px;
		z-index: 100;
		overflow: hidden;
		height: 56px;
		padding: 0;

		.vux-tab{
			height: 54px;
			.vux-tab-item{		
				line-height: 34px;
				span{
				display: block; 
				line-height: 8px;
			
			}
		}
		
		}
		
		
	}

.overdue-wrapper{
	margin-top: 142px;
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