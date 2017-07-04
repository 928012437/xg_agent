<template>
  <div id="app">
    <router-view ></router-view>
    <shop-footer></shop-footer>
    <loading></loading>
  </div>
</template>

<script>
import shopFooter from './components/footer.vue'
import Loading from './components/loading.vue'
import Axios from 'axios'

require('babel-polyfill')

export default {
  name: 'app',
  components:{
    shopFooter,
    Loading
  },
  created(){
        if(this.$route.name==undefined){
         this.$router.replace("index");
      }
  },

  data () {
    return {
        param:''
    }
  }
}
</script>

<style lang='less'>

</style>
