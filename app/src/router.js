import App from './app.vue'
import Index from './page/index.vue'
import Remind from './components/handle/remind.vue'
import Business from './components/handle/business.vue'
import urgeInfo from './page/urge-info.vue'
import urgeResult from './page/urge-result.vue'

// 客户
import Customer from './page/customer.vue'
import Search from './components/cus-search.vue'
import AddCustomer from './page/addCustomer.vue'
import CusData from './page/customer-data.vue'
import CusCopy from './page/customer-copy.vue'

import cusInfo from './page/cus-info.vue'
import addFollow from './page/addFollow.vue'
import editMessage from './page/editMessage.vue'

// 房源
import House from './page/house.vue'
import checkHouse from './components/sale-house/house.vue'
import Home from './page/home.vue'
import myCard from './page/my-card.vue'


import HouseInfo from './page/houseInfo.vue'

import calculator from './page/calculator.vue'

export const routes=[
	{path:'/addons/xg_agent/app',component:App,
		children:[
			{path:'handle',component:Index,name:'handle',
				children:[
					{path:'remind',component:Remind,name:'remind'},
					{path:'business',component:Business,name:'business'}
				]
			},
			{path:'business/urgeinfo/:id',component:urgeInfo,name:'催办'},
			{path:'business/urgeinfo/:id/urgeresult',component:urgeResult,name:'我要催办'},
			{path:'customer',component:Customer,name:'customer'},
			{path:'customer/search',component:Search,name:'搜索'},
			{path:'customer/addCustomer',component:AddCustomer,name:'新增客户'},
			{path:'customer/cusinfo/:id',component:cusInfo,name:'客户详情'},
			{path:'customer/cusinfo/:id/addfolow',component:addFollow,name:'新增跟进'},
			{path:'customer/cusinfo/:id/edit',component:editMessage,name:'编辑资料'},
			{path:'customer/datalist',component:CusData,name:'客户库'},
			{path:'customer/datalist/cuscopy/:data',component:CusCopy,name:'拷贝客户'},

			{path:'house/:id',component:House,name:'房源'},
			{path:'house/houseinfo/:num',component:HouseInfo,name:'户型信息'},
			{path:'checkhouse',component:checkHouse,name:'选择楼栋'},

			{path:'home',component:Home,name:'个人中心'},
			{path:'home/mycard',component:myCard,name:'我的名片'},
			{path:'home/calculator',component:calculator,name:'房贷计算器'}
		]

	}
]