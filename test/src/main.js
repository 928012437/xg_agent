import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'

//调用数据
require('./server/data')//node的require命令，用于加载文件，后缀名默认为.js



//============================================= 路由 ===================================
Vue.use(VueRouter)
import router from './router/router.js'

var vm = new Vue({
  el: '#app',
  router,
  data:{
  	url:'skjflskjflskf'
  },
  render: h => h(App)
})

