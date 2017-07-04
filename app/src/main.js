import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'

import './style/less/sm.less'
import {routes} from './router.js'
import {store} from './store.js'
import $ from 'n-zepto'
import 'mint-ui/lib/style.css'
import Vuerify from 'vuerify'
Vue.use(Vuerify,{
	tel:{
		    test:/^1[3|5|8]\d{9}$/,
		    message:'请输入正确的手机号'
		},
		name:{
	    		test:/^[\u4e00-\u9fa5 ]{2,10}$/,
	    		message:'请输入真实姓名'
	    },
})

Vue.use(VueRouter)

const router = new VueRouter({
	routes:routes, 
    //router-link的选中状态的class，也有一个默认的值
    linkActiveClass:'active',
    history:true,
    mode: 'history'
})

var vm = new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})


