import App from './app.vue';
// 案场统计
import Static from './page/Statistics.vue'
import ManData from './components/man-data/data/data.vue'//数据统计
import Ranking from './components/man-data/ranking/ranking.vue'//业绩排名
import Rate  from './components/man-data/rate/rate.vue'



//房源
import House from './page/man-house.vue'
import handleHouse from './components/man-house/house.vue'

// 客户
import Customer from './page/man-customer.vue'
import CusAssign from './components/man-customer/cus-assign.vue'
import CusAnalysis from './components/man-customer/cus-analysis.vue'
import Search from './components/man-customer/cus-search.vue'

// 分配客户
import cusAssShare from './components/man-customer/cus-assign/cus-ass-share.vue'
import cusAssOverdue from './components/man-customer/cus-assign/cus-ass-overdue.vue'
import cusAssInvalid from './components/man-customer/cus-assign/cus-ass-invalid.vue'


const routes=[
 {path:'/',redirect: '/static/data' },
	{path:'/static',component:Static,
		children:[
			{path:'data',component:ManData},
			{path:'ranking',component:Ranking},
			{path:'rate',component:Rate}
		]
},




//房源
{path:'/house',component:House,
	children:[

	]
},
{path:'/house/handle',component:handleHouse,name:'楼盘'},
//客户
{path:'/customer',component:Customer,redirect: '/customer/assign',
	children:[
			{path:'assign',component:CusAssign},
			{path:'analysis',component:CusAnalysis}
		]
},


{path:'/customer/assign/overdue',component:cusAssOverdue,name:'逾期客户'},
{path:'/customer/assign/share',component:cusAssShare,name:'公共客户'},
{path:'/customer/assign/invalid',component:cusAssShare,name:'无效客户'},
{path:'/customer/assign/search/:id',component:Search,name:'搜索客户'}	,

	{path:'/check',component:Check,name:"选择"},
	
]


import Check from './components/check.vue'
/*import Singer from './components/home.vue'
import Content from './components/content.vue'
import User from './components/user.vue'
import Me from './components/userContent.vue'
import You from './components/you.vue'
import Chart from './components/chart.vue'*/







 

export default routes