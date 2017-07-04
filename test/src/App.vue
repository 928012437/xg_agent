<template>
  <div id="app">
  
      <House></House>
  


    <router-view></router-view>
    <!-- <!-- <transiton :name='transitionName'> 
        <router-view></router-view>
    </transiton>
    <child :ishow='ishow'></child>
    <button @click='ishow=!ishow'>显示隐藏</button>
    <nav>
        <ul>
          <li>
            <router-link to='/yesterdaty'>昨的天</router-link>
          </li>
          <li>
            <router-link to='/'>今天发水电费</router-link>
          </li>
          <li>
            <router-link to='/tomorrow'>明天</router-link>
          </li>

        </ul>
    </nav> -->
  </div>
</template>

<script>
import {ajax} from './api/ajax.js';
import axios from 'axios';
// import Child from './components/child.vue'
import House from './pages/house.vue'

export default {
  name: 'app',
  data () {
    return {
      transitionName:'slide-right',
      ishow:true
    }
  },
  components:{
    House
  },
  watch: {
    '$route'(to,from){
      const toDepth = to.path.split('/').length;
      const fromDepth = from.path.split('/').length;
      this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
    }
  },
  created(){
    // alert('sf地方水电费水电费斯蒂水安抚电费芬胜多负少')
    // axios.get('hello.json').then(function(d){
    //   alert(d)
    // })
        // ajax({
        //   method:"get",
        //   url:'hello.json',
        //   success:function(res){
        //     var data = JSON.parse(res)
        //   },
        //   async:true
        // })

  },
  methods:{
     
  }

}
</script>

<style lang='less' scoped>
  nav{
    ul{
      list-style: none;
      text-align: center;
      padding: 0;
      li{
        display: inline-block;
        padding: 10px 15px;
        background: rgb(154, 148, 221);
        a{
          color: #fff;
          text-decoration: none;
        }
      }
    }
  }


  .slide-left-enter{}
  .slide-left-enter-active{}
  .slide-left-leave{}
  .slide-left-active{}


</style>
