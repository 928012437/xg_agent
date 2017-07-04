<template>
  <div>
      <div class="list-block media-list content" >
         <ul v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <ul>
            <li v-for='(item,index) in selectItems'>
            <label  :for="'dd'+index" class="label-checkbox item-content"  >
              <input type="checkbox" name="my-radio" :value='item.value' v-model='item.checked' :id="'dd'+index" />
              <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
              <div class="item-inner">
                <div class="item-title-row">
                  <div class="item-title">{{item.value}}</div>
                  <div class="item-after">{{index}}</div>
                </div>
                <div class="item-subtitle">New messages from John Doe</div>
              </div>
            </label>
          </li>
      </ul>
    </div>
    <nav class="bar bar-tab">
 <div class="row all-check">
 <div class="col-40">
        <label class="label-checkbox item-content" >
           <input type="checkbox" v-model='selectAll'/>
         <div class="item-media">
         <i class="icon icon-form-checkbox"></i>
         </div>
    </label>      
      </div>
      <div class="col-60">已选{{selectCount.isFooter}}</div>
    </div>
</nav>  
  </div>
</template>

<script>
import {mapGetters,mapActions} from 'vuex';

  export default{
      data(){
        return{
            selectItems:[
            {   value:'九堡',
                checked:false
              },
              { 
                value:'杭盖',
                checked:false
              },
              {
                value:'颠覆m',
                checked:false
              }
              ]
        }
      },
      created(){
          this.$store.commit('COMM_CONF',{
                isFooter:false,
                /*isSearch:true,
                isBack:false,
                isShare:false,
                title:''*/
            });
      },
      computed: {
          // 全选checkbox绑定的model
          selectAll: {
              //判断是否全选 当选中的长度跟 数组的长度相等时 
              get: function() {
                  return this.selectCount == this.selectItems.length;
              },
              //设置全选和反选
              set: function(value) {
                  this.selectItems.forEach(function(item) {
                      item.checked = value;
                  });
                  return value;
              }
          },
          //选中的数量
          selectCount: {
              get: function() {
                  var i = 0;
                  this.selectItems.forEach(function(item) {
                      if (item.checked) {
                          i++;
                      }
                  });
                  return i;
              }
          },
          //选中的数组
          checkedGroups: {
              get: function() {
                  var checkedGroups = [];
                  this.selectItems.forEach(function(item) {
                      if (item.checked) {
                          checkedGroups.push(item.value);
                      }
                  });
                  return checkedGroups;
              }
          }
      }

  }
</script>