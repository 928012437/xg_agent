import Vue from 'vue'  //引入vue库
import Vuex from 'vuex'
import MintUI from 'mint-ui'
import VueRouter from 'vue-router'
// import 	Echarts	 from 'echarts'
import 'mint-ui/lib/style.css'
import mock from './server/mock';

import './less/sm.less';
import App from './App.vue'//跟组件，入口组件
import routes from './router.js'
import store from './store/store.js'
import  iScroll from 'iscroll'


import { Loadmore,Navbar, TabItem } from 'mint-ui';
Vue.component('mt-loadmore', Loadmore);


Vue.use(VueRouter)


 const router =new VueRouter({
	routes:routes, // short for routes: routes
       //router-link的选中状态的class，也有一个默认的值
    history:true,
    mode: 'history'
})


export default router

//实例化一个vue实例（spa单页面应用）
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})

