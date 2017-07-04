<template>
    <div class="content">
        <search></search>
        <div class="cus-nav">
            <router-link to='/addons/xg_agent/app/customer/addCustomer' class='cus-nav-link'>
                <i class="iconfont">&#xe60e;</i>
                <p class="icon-explam">新增客户</p>
            </router-link>
            <router-link to='' class='cus-nav-link'>
                <i class="iconfont">&#xe616;</i>
                <p class="icon-explam">归属查询</p>
            </router-link>
            <router-link to='/addons/xg_agent/app/customer/datalist' class='cus-nav-link'>
                <i class="iconfont">&#xe60b;</i>
                <p class="icon-explam">客户库</p>
            </router-link>
        </div>
        <div class="card customer-nav">
            <div class="card-header">
                <div @click="checkedType">{{levelType}}<span class="icon icon-left pull-right"
                                                             :class="{'iconOn':ishow}"></span></div>
                <div @click="checkedTime">{{time}} <span class="cus-count pull-right">总数：{{count}}人</span></div>
            </div>
            <div class="card-content list-block" v-show='ishow'>
                <div class="card-content-inner " v-show="check=='type'">
                    <ul>
                        <li class="item-content " v-for='(level,index) in levelDate' @click='levelType=level;changestatus(index)'
                            :class="{'navOn':levelType==level}">
                            <div class="item-inner">
                                <div class="item-title">{{level}}</div>
                                <div class="item-after"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- 时间 -->
                <div class="card-content-inner " v-show="check=='time'">
                    <ul>
                        <li class="item-content" @click="time='跟进时间↓';changeordertype(1)" :class="{'navOn':time=='跟进时间↓'}">
                            <div class="item-inner">
                                <div class="item-title">跟进时间↓</div>
                                <div class="item-after"></div>
                            </div>
                        </li>
                        <li class="item-content" @click="time='跟进时间↑';changeordertype(2)" :class="{'navOn':time=='跟进时间↑'}">
                            <div class="item-inner">
                                <div class="item-title">跟进时间↑</div>
                                <div class="item-after"></div>
                            </div>
                        </li>
                        <li class="item-content" @click="time='创建时间↓';changeordertype(3)" :class="{'navOn':time=='创建时间↓'}">
                            <div class="item-inner">
                                <div class="item-title">创建时间↓</div>
                                <div class="item-after"></div>
                            </div>
                        </li>
                        <li class="item-content" @click="time='创建时间↓';changeordertype(4)" :class="{'navOn':time=='创建时间↑'}">
                            <div class="item-inner">
                                <div class="item-title">创建时间↑</div>
                                <div class="item-after"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="content customer-list">
            <customer-list :cusData="cusData" :status="status" :ordertype="ordertype" ></customer-list>

        </div>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Search from '../components/search.vue'
    import customerList from '../components/cus-list.vue'
    import axios from 'axios';

    export default{
        components: {
            Search,
            customerList
        },

        created(){
            this.$store.commit('footer', true);
            this.getstatus();
            this.getcustomerlist();
        },
        data(){
            return {
                ishow: false,
                type: '',
                time: '跟进时间↓',
                check: '',
                levelType: "全部",
                // 数据接收 搜索类型/级别
                levelDate: ['全部', "关注"],
                levelid: ['', 0],
                cusData: [
                    {
                    }
                ],
                page: 1,
                status: '',
                is_gz: '',
                ordertype: 1,
                count:0
            }
        },
        watch: {
            levelType(val){
                this.ishow = !this.ishow
            },
            time(val){
                this.ishow = !this.ishow
            }
        },
        methods: {
            checkedType(){
                this.check = 'type'
                this.ishow = !this.ishow
            },
            checkedTime(){
                this.check = 'time'
                this.ishow = !this.ishow
            },
            // 接收数据
            getstatus(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getstatus').then(function (res) {
                    if (res.data.code == 3) {
                        res.data.data.forEach(function(v){
                            that.levelDate.push(v.name);
                            that.levelid.push(v.id);
                        });
                    }
                });
            },
            changestatus(key){
                this.status=this.levelid[key];
                this.page=1;
                this.getcustomerlist();
            },
            changeordertype(key){
                this.ordertype=key;
                this.page=1;
                this.getcustomerlist();
            },
            getcustomerlist(){
                var that = this;
                axios.post(urlstr + 'user.counselors&page=' + this.page + '&status=' + this.status + '&ordertype=' + this.ordertype).then(function (res) {
                    if (res.data.code == 3) {
                        that.count=res.data.count;
                        that.cusData = [];
                        res.data.data.forEach(function(v){
                            that.cusData.push(v);
                        })
                    }
                });
            }
        }
    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .customer-nav {
        position: relative;
        margin: 0;
        border-top: 1px solid @color-split;
        box-shadow: none;
        z-index: 999;
        .card-header {
            font-size: 14px;
            & > div:last-child {
                width: 70%;
            }
            .cus-count {
                color: @color-text-gray
            }
            & > div:first-child {
                width: 28%;
                border-right: 1px solid @color-split;
                .icon {
                    font-size: 0.8em;
                    margin-right: 4%;
                    transform: rotate(0);
                    transition: transform 0.2s
                }
                .iconOn {
                    transform: rotate(-90deg);
                }
            }

        }
        .card-content {
            position: absolute;
            background: #fff;
            width: 100%;
            .navOn {
                background: Lighten(@color-text-gray-light, 10%)
            }
        }

        .card-content-inner {
            font-size: 14px;
            padding: 0;
        }
    }

    .cus-nav {
        margin-top: 46px;
        background: #fff;
        padding: 0.5rem 0;
        .flexbox;
        .cus-nav-link {
            text-align: center;
            .flex(1);
            .iconfont {
                font-size: 1.6rem;
                display: inline-block;
            }
            & > p {
                margin: 0;
                font-size: 0.6em;
                color: @color-text-secondary
            }
        }
    }

    .cleck-list-nav {
        background: #fff;
        border-top: 1px solid @color-split;
        padding: 10px;
        margin-left: 0;
        font-size: 14px;
        position: relative;
        .list-block {
            position: absolute;
            height: 455px;
            width: 100%;
            top: 0;
            z-index: 999
        }
        .col-30 {
            width: 28%;
            border-right: 1px solid @color-split;
            margin-right: 2%;
            .icon {
                font-size: 0.8em;
                margin-right: 4%
            }
        }
        .col-80 {
            width: 66%;
        }
    }

    .customer-list {
        margin-top: 86px;
        height: 400px;
    }


</style>