<template>
 <div>
  <div class="list-block">
    <ul v-if='showValue==show'>
      <li class="item-content" v-for='data in message'>
        <div class="item-inner">
          <div class="item-title">{{data.key}}</div>
          <div class="item-after">{{data.value}}</div>
        </div>
      </li>     
    </ul>
    <ul v-else>
      <li class="item-content" v-for='data in message'>
        <div class="item-inner">
          <div class="item-title">{{data}}</div>
        
        </div>
      </li>     
    </ul>
  </div>
  

</div>
</template>
<script type='text/ecmascript-6'>

export default{
  props:['message','showValue'],
  data(){
    return{
      show:'message'
    }
  }
}

</script>

<style lang='less' scoped>
.item-content{
  font-size: 14px;
}

.list-block {
  .item-content{
      padding-left: 0
  }
  .item-inner{
   min-height:1.8rem;
   padding-left: 0.8rem
}
  }
.item-after{

}

</style>