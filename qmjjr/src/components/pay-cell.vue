 <template>
 <div>
   <ul   v-infinite-scroll="loadMore"
  infinite-scroll-disabled="loading"
  infinite-scroll-distance="2" style="margin-bottom:0px;">
    <li class="pay-cell " v-for='pay in payData'>
      <div class=" pull-left">
        <div class="pay-text">{{pay.text}}：积分+{{pay.credit}}</div>
        <div class="pay-time">{{pay.createtime}}</div>
      </div>
      <div class=" pull-right">
        <div class="pay-money">+{{pay.commis}}</div>
        <div class="pay-state">{{pay.state}}</div>
      </div>
    </li>
</ul>
<!-- <div>{{index}}</div> -->
</div>
</template>
<script type='text/ecmascript-6'>
import Vue from 'vue'
import { InfiniteScroll } from 'mint-ui';
Vue.use(InfiniteScroll);

export default{
    props:['payData','payCount','index','deCount'],
    data(){
      return{
          // count:0
      }
    },
    created(){
     
    },
    mounted(){
      
    },
    methods:{
      //param参数，
      getData(param,count){
         alert(param+'----'+count)
        this.payData=this.payData.concat([
            {
        },
             ])
      },
      loadMore() {
        this.loading = true;
        var _this =this;
        if(this.payData.length>=10){
        setTimeout(() => {

            switch(_this.index){
              case 1:
                _this.payCount.allCount++
                 _this.getData(_this.index, _this.payCount.allCount)
              break;
              case 2:
             
                _this.payCount.pendingCount++
                _this.getData(_this.index, _this.payCount.pendingCount)
              break;
        }
          
          this.loading = false;
        }, 2500);
       }
      }

    }
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
.pay-cell{
  font-size: 14px;
  border-bottom: 1px solid @color-split;
  padding: 4px 15px;
  .clearfix;
  .pay-state,.pay-time{
    color: @color-text-gray
  }

}

</style>

