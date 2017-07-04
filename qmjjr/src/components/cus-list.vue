<template>
    <div class="list-block media-list">
        <mt-loadmore :bottom-method="loadBottom" :bottom-all-loaded="allLoaded" ref="loadmore" :bottomPullText='tishitext' :autoFill=false >
            <ul>
                <li class="label-checkbox item-content" v-for='cus in cusData'>

                    <div class="item-inner">

                        <div class="item-title-row">
                        <router-link :to="'/addons/xg_agent/qmjjr/customer/customerinfo/'+cus.id">
                            <div class="item-title">{{cus.name}}
                                <span class="tag" :class="{'tagok':cus.is_review}" >{{cus.is_reviewtext}}</span>
                                <span class="tel">{{cus.tel}}</span>
                            </div>
                        </router-link>
                            <div class="item-after">
                                <a :href="'tel:'+cus.tel" class="iconfont ">&#xe607;</a>
                                <a :href="'sms:'+cus.tel" class="iconfont ">&#xe684;</a>
                            </div>
                        </div>

                        <div class="item-title-row">
                            <div class="item-title">{{cus.mark}}</div>
                            <div>{{cus.yuqi}}</div>
                            <div class="item-after">{{cus.status}}</div>
                        </div>

                    </div>

                </li>
                </ul>
        </mt-loadmore>
        </div>
</template>
<script type='text/ecmascript-6'>
    import Vue from 'vue';
    import { Loadmore } from 'mint-ui';
    import axios from 'axios';

    Vue.component(Loadmore.name, Loadmore);

    export default{
        data(){
            return {
                page:1,
                allLoaded: false,
                tishitext:'上拉刷新'
            }
        },
        props:['cusData','status'],
        methods: {
            loadBottom(){
                    this.page++;
                    this.getcustomerlist();
            },
            getcustomerlist(){
                var that = this;
                axios.post(urlstr + 'qmjjr&page=' + this.page + '&status=' + this.status).then(function (res) {
                    if (res.data.code == 3) {
                        res.data.data.forEach(function(v){
                            if(v.is_review==1){
                                v.is_review=true;
                                v.is_reviewtext='已审核';
                            }else {
                                v.is_reviewtext='未审核';
                                v.is_review=false;
                            }
                            that.cusData.push(v);
                        })
                        if(res.data.data.length<8){
                            that.allLoaded = true;// if all data are loaded
                            that.$refs.loadmore.onBottomLoaded();
                            that.tishitext='加载完成';
                        }
                    }
                });
            }

        }
    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .list-block {
        margin-top: 0px;
        margin-bottom: 10px;
    }

    .item-title-row {
        font-size: 14px;
        color: @color-text-gray;
        &:first-child {
            color: @color-text-secondary;
            margin-bottom: 15px;
            font-size: 16px;
        }
    ;
        .item-after {
            color: @color-text-gray;
            .iconfont {
                margin: 0 8px;
            }
        }
    }

    .item-title {

        .tel {
            display: block;
        }
    }
    .tag{
        color: #fff;
        background: @color-danger;
        padding: 0 2px;
        border-radius: 2px;
        font-size: 12px;
        margin-top: 2px;
        display: inline-block;
        margin-left: 4px;
        transform: translateY(-2px);

    }
    .tagok{
        background: @color-success;
    }
    a.cus-link{
        width: 100%;
        display: inline-block;
        color: @color-text;
    }


</style>