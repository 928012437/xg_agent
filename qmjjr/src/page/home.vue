<template>
    <div class="content">

        <div class="home-top">
            <div class="user-brank">
                <router-link to='/addons/xg_agent/qmjjr/home/bankcard'>
                    <i class="iconfont">&#xe657;</i>
                    我的银行卡
                </router-link>
            </div>
            <div class="home-center">
                <div class="user-photo">
                    <img :src="data.headurl" alt="">
                </div>
                <p class="user-name">{{data.realname}}</p>
                <div class="user-team">[{{data.sfname}}]</div>

            </div>
            <div class="user-message">
                <router-link to='/addons/xg_agent/qmjjr/home/revise'>
                    <i class="iconfont">&#xe669;</i>
                    修改资料
                </router-link>
            </div>
        </div>
        <Cell>
            <cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/pay')">
                <i class="iconfont orangeColor" slot='icon'>&#xe677;</i>
                <span slot='title'>我的佣金</span>
                <span slot='result'>{{data.commission}}</span>
            </cellItem>

            <cellItem @click.native="$router.push('/addons/xg_agent/qmjjr/home/withdraw')">
                <i class="iconfont orangeColor" slot='icon'>&#xe65d;</i>
                <span slot='title'>提现</span>
                <span slot='result'>{{data.ketixian}}</span>
            </cellItem>
        </Cell>
        <Cell>
            <cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/integral')">
                <i class="iconfont orangeColor" slot='icon'>&#xe675;</i>
                <span slot='title'>我的积分</span>
                <span slot='result'>{{data.credit1}}</span>
            </cellItem>
            <cellItem>
                <i class="iconfont orangeColor" slot='icon'>&#xe66d;</i>
                <span slot='title'>已打款佣金</span>
                <span slot='result'>{{data.credit2}}</span>
            </cellItem>
        </Cell>

        <div class="my-item">
            <Cell>
                <cellItem :show='true' @click.native="fuwutel()" >
                    <i class="iconfont greenColor" slot='icon'>&#xe665;</i>
                    <span slot='title' class="martop">服务专员</span>
                </cellItem>

                <cellItem @click.native="$router.push('/addons/xg_agent/qmjjr/home/act')">
                    <i class="iconfont greenColor" slot='icon'>&#xe66b;</i>
                    <span slot='title' class="martop">我的认证</span>
                </cellItem>
            </Cell>
        </div>
        <div class="my-item">
            <Cell>
                <cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/ask')">
                    <i class="iconfont blueColor" slot='icon'>&#xe678;</i>
                    <span slot='title' class="martop">经济人问答</span>
                </cellItem>

                <cellItem @click.native="$router.push('/addons/xg_agent/qmjjr/home/notice')">
                    <i class="iconfont blueColor" slot='icon'>&#xe66c;</i>
                    <span slot='title' class="martop">经纪人须知</span>

                </cellItem>
            </Cell>
            <Cell>
                <cellItem :show='true' @click.native="$router.push('/addons/xg_agent/qmjjr/home/suggest')">
                    <i class="iconfont blueColor" slot='icon'>&#xe67b;</i>
                    <span slot='title' class="martop">投诉和建议</span>

                </cellItem>

            </Cell>


        </div>

        <div class="my-item">
            <Cell>
                <cellItem :show='true' @click.native="goshop()">
                    <i class="iconfont greenColor" slot='icon'>&#xe643;</i>
                    <span slot='title' class="martop">积分商城</span>
                </cellItem>

                <cellItem :show='true' @click.native="loginout()">
                    <i class="iconfont greenColor" slot='icon'>&#xe619;</i>
                    <span slot='title' class="martop">退出</span>
                </cellItem>
            </Cell>
        </div>


    </div>
</template>
<script type='text/ecmascript-6'>
    import Cell from '../components/home-cell/home-cell.vue'
    import cellItem from '../components/home-cell/cell-item.vue'
    import axios from 'axios';

    export default{
        components: {
            Cell,
            cellItem
        },
        created(){
            this.$store.commit('footer', true);
            this.getinfo();
        },
        data(){
            return {
                data: []
            }
        },
        computed: {},
        methods: {
            show(){
                alert('sfd')
            },
            getinfo(){
                var that = this;
                axios.post(urlstr + 'qmjjr.getinfo').then(function (res) {
                    if (res.data.code == 3) {
                        that.data = res.data.data;
                    } else {
                        alert(res.data.message);
                        that.$router.push('/addons/xg_agent/qmjjr/index');
                    }
                });
            },
            loginout(){
                var that = this;
                axios.post(urlstr + 'qmjjr.login.logout').then(function (res) {
                    if (res.data.code == 0) {
                        that.$router.push('/addons/xg_agent/qmjjr/index');
                    }
                });
            },
            goshop(){
                window.location.href=urlstr+'creditshop'
            },
            fuwutel(){
                window.location.href='tel:'+this.data.mobile;
            }
        }


    }


</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .martop {
        margin-top: 5px;
        display: block;
    }

    .orangeColor {
        color: #FF4E00;
    }

    .greenColor {
        color: #3AB663
    }

    .blueColor {
        color: #0894EC;
    }

    .home-center {
        .user-photo {
            width: 80px;
            height: 80px;
            border: 1px solid @color-split;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto;
            img {
                display: block;
                width: 100%;
                height: 100%;
            }
        }
        .user-name {
            font-size: 16px;
            margin: 5px 0 0;
            padding: 0;
        }
        .user-team {
            font-size: 12px;
        }
    }

    .home-top {
        background: @color-primary;
        .flexbox;
        .align-items(center);
        color: #fff;
        height: 200px;
        font-size: 16px;
        div {
            .flex(1);
            text-align: center;
        }
        .user-brank a, .user-message a {
            color: #fff;
            font-size: 14px;
        }
    }

    .user-brank .iconfont, .user-message .iconfont {
        display: block;
    }

    .cell-link {
        border-right: 1px solid @color-split;
        display: block;
        width: auto;
    }

    .my-item {
        margin-top: 10px;
    }

</style>