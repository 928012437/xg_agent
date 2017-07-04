import VueRouter from 'vue-router'

//根目录
var rootPath="/";

// ------------- 懒加载 -----------------
const Today = function(resolve){
    require(['../pages/today.vue'],resolve)
}
//AMD语法： require([module], callback);
//写法： const Foo = resolve => require([./Foo.vue], resolve)
const Yesterday = resolve => require(['../pages/yesterdaty.vue'],resolve)
const Tomorrow = resolve => require(['../pages/tomorrow.vue'],resolve)

var routes= [
  {path:rootPath,component:Today,name:'home'},
  {path:rootPath + 'yesterdaty/:id',component:Yesterday,name:'yesterdaty'},
  {path:rootPath + 'tomorrow/:id',component:Tomorrow,name:'tomorrow'},
]


const router = new VueRouter({
  mode:"hash",
  base:__dirname,
  routes:routes,
})

export default router;
