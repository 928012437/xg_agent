import Vue from 'vue'  //����vue��
import Vuex from 'vuex'
import MintUI from 'mint-ui'
import VueRouter from 'vue-router'
// import 	Echarts	 from 'echarts'
import 'mint-ui/lib/style.css'
import mock from './server/mock';

import './less/sm.less';
import App from './App.vue'//�������������
import routes from './router.js'
import store from './store/store.js'
import  iScroll from 'iscroll'


import { Loadmore,Navbar, TabItem } from 'mint-ui';
Vue.component('mt-loadmore', Loadmore);


Vue.use(VueRouter)


 const router =new VueRouter({
	routes:routes, // short for routes: routes
       //router-link��ѡ��״̬��class��Ҳ��һ��Ĭ�ϵ�ֵ
    history:true,
    mode: 'history'
})


export default router

//ʵ����һ��vueʵ����spa��ҳ��Ӧ�ã�
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})

