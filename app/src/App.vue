<template>
  <div id="app">
    <router-view ></router-view>
  <sale-footer></sale-footer>
  </div>
</template>

<script>
import saleFooter from './components/footer.vue'
import Axios from 'axios'
export default {
  name: 'app',
  components:{
    saleFooter
  },
  created(){
        if(this.$route.name==undefined){
         this.$router.push("customer");
      }
     this.param=this.$route.params.id
     // this.param=12;
     /* Axios.get("http://w.xghd.cc/app/index.php?i="+this.param+"&c=entry&m=xg_agent&do=mobile&r=user").then(function(res){
            console.log(res)
            console.log(res.code)
            console.log(res.message)
      })*/
     
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
