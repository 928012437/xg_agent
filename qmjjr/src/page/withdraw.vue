<template>
    <div class="content">
        <top></top>
        <div class="card withdraw-header margin-top">
            <div class="card-header">
                <div>
                    累计佣金(元)
                    <span class="money">{{data.num1}}</span>
                </div>
                <div>
                    <button class="button" @click="$router.push('/addons/xg_agent/qmjjr/home/withdraw/detailed')
	   			">提现明细
                    </button>
                </div>
            </div>
        </div>
        <div class="card withdraw-main">
            <div class="card-header">
                <div>可提现金额</div>
                <div class='cash'>{{data.num2}}</div>
            </div>
            <div class="card-content">
                <div class="list-block">
                    <ul>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title">已申请佣金</div>
                                <div class="item-after">{{data.num3}}元</div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title">待打款佣金</div>
                                <div class="item-after">{{data.num4}}元</div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title">无效佣金</div>
                                <div class="item-after">{{data.num5}}元</div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title">成功提现佣金</div>
                                <div class="item-after">{{data.num6}}元</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card withdraw-footer">
            <div class="card-header">用户须知</div>
            <div class="card-content">
                <div class="card-content-inner">可提现金额需大于一元，提现需审核，请耐心等待。</div>
            </div>
            <div class="card-footer">
                <button class="button withdraw-btn button-big" @click="$router.push('/addons/xg_agent/qmjjr/home/withdraw/apply')">我要提现
                </button>
            </div>
        </div>


    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    import axios from 'axios';

    export default{
        data(){
            return {
                data: []
            }
        },
        components: {
            Top
        },
        created(){
            this.$store.commit('footer', false);
            this.gettixianinfo();
        },
        methods: {
            gettixianinfo(){
                var that = this;
                axios.post(urlstr + 'qmjjr.gettixianinfo').then(function (res) {
                    if (res.data.code == 3) {
                        that.data = res.data.data;
                    }
                });
            }
        }
    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .card {
        box-shadow: none;
    }

    .withdraw-header {
        background: @color-primary;
        margin: 0;
        color: #fff;
        padding: 15px;
        .card-header {
            font-size: 16px;
        }
        @media only screen and (-webkit-min-device-pixel-ratio: 2) {
            .card-header:after {
                transform: scaleY(0);
            }
        }
        .money {
            display: block;
            font-size: 14px;
        }
        .button {
            color: #fff;
            border-color: #fff;
        }
    }

    .withdraw-main {
        margin: 0;
        font-size: 14px;
        .item-content {
            font-size: 14px;
        }
    }

    .withdraw-footer {
        margin: 10px 0 0;
        .withdraw-btn {
            padding: 0 1.8rem;
            margin: 0 auto;
        }
    }
</style>