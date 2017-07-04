import App from './app.vue'

import Index from './page/index.vue'
import Home from './page/home.vue'
import Info from './page/infor.vue'

import Pay from './page/pay.vue'
import Integral from './page/integral.vue'

import Withdraw from './page/withdraw.vue'
import Atc from './page/atc.vue'
import Notice from './page/notice.vue'
import Revise from './page/revise.vue'
import goodInfo from './page/goods-info.vue'
import Detailed from './page/detailed.vue'
import houseType from './page/house-type.vue'
import Apply from './page/apply.vue'
import Suggest from './page/suggest.vue'
import Register from './page/register.vue'
import Ask from './page/ask.vue'

import Customer from './page/customer.vue'
import CustomerInfo from './page/customer-info.vue'
import Search from './page/cus-search.vue'
import Recommend from './page/recommend.vue'
import BankCard from './page/bank-card.vue'

import Checksign from './page/checkSign.vue'
import Sign from './page/sign.vue'

export const routes=[
	{path:'/addons/xg_agent/qmjjr',component:App,
		children:[
			{path:'index',component:Index,name:'首页'},
			{path:'customer',component:Customer,name:'客户'},
			{path:'customer/customerinfo/:id',component:CustomerInfo,name:'客户详情'},
			{path:'customer/search',component:Search,name:'搜索客户'},
			{path:'customer/recommend',component:Recommend,name:'我要推荐'},
			{path:'home/bankcard',component:BankCard,name:'我的银行卡'},
			{path:'home/withdraw',component:Withdraw,name:'提现'},
			{path:'home/act',component:Atc,name:'我的认证'},
			{path:'home/notice',component:Notice,name:'经纪人须知'},
			{path:'home/revise',component:Revise,name:'修改资料'},
			{path:'home/suggest',component:Suggest,name:'投诉和建议'},
			{path:'index/goodsinfo/:id',component:goodInfo,name:'户型信息'},
			{path:'index/goodsinfo/:id/housetype/:typeid',component:houseType,name:'户型图'},
			{path:'register',component:Register,name:'rigister'},
			{path:'home/ask',component:Ask,name:'经纪人问答'},

			{path:'home/withdraw/detailed',component:Detailed,name:'提现明细'},
			{path:'home/withdraw/apply',component:Apply,name:'我要提现'},

			{path:'home',component:Home,name:'个人中心'},
			{path:'info',component:Info,name:'info'},
			
			{path:'home/pay',component:Pay,name:'我的佣金'},
			{path:'home/integral',component:Integral,name:'我的积分'},

			{path:'checksign',component:Checksign,name:'选择登录方式'},
			{path:'sign',component:Sign,name:'登陆'},
		]

	}
]