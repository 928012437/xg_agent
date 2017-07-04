
<template>
   <div class="loading" v-show='loading'>
     <div class="loding-icon">
         <mt-spinner type="snake" :size=48></mt-spinner>
     </div>
  </div> 
</template>
<script>
import Vue from 'vue'
import {mapGetters} from 'vuex';
import { Spinner } from 'mint-ui';
Vue.component(Spinner.name, Spinner);

export default{
    computed:{
        ...mapGetters([
            'loading'
             ]),
    }
}
</script>
<style  lang='less'>
.loading{
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.4);
  text-align: center;
  z-index: 999;
  .loding-icon{
     text-align: center;
      position: relative;
      top: 50%;
      -webkit-transform: translateY(-50%);
      -o-transform: translateY(-50%);
      transform: translateY(-50%);
      -webkit-transform: translateX(50%);
      -o-transform: translateX(50%);
      transform: translateX(50%);
      margin-left: -24px;
  }
}
.mint-spinner-snake {
    -webkit-animation: mint-spinner-rotate 0.8s infinite linear;
    animation: mint-spinner-rotate 0.8s infinite linear;
    border: 4px solid transparent;
    border-radius: 50%;
}
@-webkit-keyframes mint-spinner-rotate {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}
@keyframes mint-spinner-rotate {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}
</style>
