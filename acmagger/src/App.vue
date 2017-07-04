<template>
  <div id="app" >
    
    <div class="contont">
      <transition :name='transitionName'>
        <router-view  ></router-view>
      </transition>
    </div>
    
    <manFooter ></manFooter>


    
</template>
<script>
import Statistics from './page/Statistics.vue'
import manFooter from './components/footer.vue'



export default{
  components:{
    Statistics,
    manFooter
  },
  data(){
    return{
      show:false,
      value:'全选',
      transitionName: 'slide-left'
    }
  },
   watch: {
      '$route' (to, from) {
        const toDepth = to.path.split('/').length;
        const fromDepth = from.path.split('/').length;
        this.transitionName = toDepth < fromDepth ? 'slide-left' : 'slide-right' 
      }
   },
  methods:{

  }
     
}
 
</script>

<style  lang='less' scoped>
@import './common/style/mixin.less';


.box{
 .size(100%,20px);
 background: red;
  
}
/* .slide-right-enter-active {
  animation: fadeInRight 0.3s ease;
}


.slide-left-enter-active{
  animation: fadeInLeft 0.3s ease;
} */
.slide-right-enter, .slide-right-leave-active {
  transform: translateX(10px);
  opacity: 0;
}
.slide-right-enter-active {
  transition: all .3s ease;
}

@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}


</style>
