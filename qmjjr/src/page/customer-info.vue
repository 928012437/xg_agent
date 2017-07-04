<template>
    <div class="content">
        <shop-header></shop-header>
        <div class="content cus-content">
            <div class="card cus-info-header">
                <div class="card-header">
                    <div class="photo">
                        <div class="photo-wraper">
                            <img :src="info.headurl" >
                        </div>
                    </div>
                    <div class="info">
                        <div class="clearfix">
                            <span class="pull-left">{{info.name}}</span>
                            <span class="tag pull-left">{{info.status_name}}</span>
                        </div>
                        <p class="tel">{{info.tel}}</p>
                        <p class="tel">意向产品：{{info.louname}}</p>
                    </div>

                    <div class="call">
                        <a :href="'sms:'+info.tel" class='iconfont'>
                            &#xe62e;
                        </a>
                        <a :href="'tel:'+info.tel" class='iconfont'>
                            &#xe607;
                        </a>
                    </div>
                </div>
                <div class=" sale-content">
                    <div>
                        <div class="photo-wraper pull-left">
                            <img :src="info.user_headurl"/>
                        </div>
                        <div class="info pull-left">
                            {{info.user_name}}<span>销售员</span>
                            <p class="tel">{{info.user_tel}}</p>
                        </div>
                    </div>

                    <div>

                        <a :href="'tel:'+info.user_tel" class='iconfont saleTel'>
                            &#xe607;
                        </a>
                    </div>
                </div>
            </div>
            <div class="path">
                <ul>
                    <li class="path-list" v-for='(sta,index) in status' :class="{'path-list-active':sta.id==info.status}">{{sta.name}}
                    </li>
                </ul>
            </div>
            <follow :logData='log'></follow>
        </div>
    </div>

</template>
<script type='text/ecmascript-6'>
    import shopHeader from '../components/header.vue'
    import Follow from '../components/cus-info/follow.vue'
    import axios from 'axios';

    export default{
        components: {
            shopHeader,
            Follow,

        },
        created(){
            this.$store.commit('footer', false);
            var id = this.$route.params.id;
            this.getcustomerdetail(id)
            this.getstatus()
        },
        data(){
            return {
                today: '',
                tab: 'follow',
                info: '',
                status: '',
                log:''
            }
        },
        computed: {},
        methods: {
            getcustomerdetail(id){
                var that = this;
                axios.post(urlstr + 'qmjjr.getcustomerdetail&id=' + id).then(function (res) {
                    if (res.data.code == 3) {
                        that.info = res.data.data;
                        that.log = res.data.log;
                    }
                });
            },
            getstatus(){
                var that = this;
                axios.post(urlstr + 'qmjjr.getstatus').then(function (res) {
                    if (res.data.code == 3) {
                        that.status = res.data.data;
                    }
                });
            }
        }


    }


</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .external {
        background: @color-primary;
        color: #fff;
    }

    .cus-info-header {
        margin-top: 50px;
        box-shadow: none;
        background: @color-primary;
        color: #fff;
        border-top-right-radius: 5px;
        border-top-left-radius: 5px;
        padding: 10px 0 0;
        margin-bottom: 0;
        .call {
            .iconfont {
                color: #fff;
                font-size: 22px;
            }
        }

        .card-header {
            .info {
                width: 50%
            }
            .photo {
                width: 20%;
            }
        }

        .tag {
            font-size: 12px;
            background: orange;
            color: #fff;
            padding: 0 1px;
            border-radius: 2px;
            margin-left: 4px;
            margin-top: 3px;
        }
        .integrity {
            font-size: 12px;
            margin-left: 4px;
            margin-top: 4px;
        }
        .tel {
            margin: 0px;
            font-size: 16px;
        }
        .photo-wraper {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            overflow: hidden;
            margin-top: -10px;
            img {
                display: block;
                width: 100%;
                height: 100%;
            }
        }
    }

    .sale-content {
        margin: 0;
        background: #fff;
        border-radius: 0;
        color: @color-text-secondary;
        font-size: 13px;
        .flexbox;
        padding: 10px 20px 5px;
        .justify-content(space-between);
        .photo-wraper {
            width: 40px;
            height: 40px;
            margin-top: -2px;
            margin-right: 8px;
        }
        .tel {
            margin-top: -5px;
        }
        .info > span {
            margin-left: 4px;
            color: #ccc;
            font-size: 12px;
        }
        .saleTel {
            margin-top: 4px;
            width: 26px;
            height: 26px;
            display: inline-block;
            border-radius: 50%;
            background: @color-primary;
            text-align: center;
            line-height: 25px;
            color: #fff;

        }
    }

    .sign-top {
        margin-top: 44px;
        background: #4DA2FD;
        .flexbox;
        .align-items(center);
        color: #fff;
        height: 200px;
        .button {
            margin: 0 auto;
        }
        div {
            .flex(1);

            text-align: center;
        }
    }

    .path {
        background: #fff;
        margin-top: 10px;
        padding: 15px 15px 10px;
        .clearfix;
        margin-bottom: 10px;
        ul {
            margin: 0 auto;
            .clearfix;
        }

        .path-list {
            float: left;
            font-size: 14px;
            padding: 20px 0 0;
            width: 25%;
            text-align: center;
            border-top: 1px solid #9c9a9a;
            position: relative;
            &:before {
                position: absolute;
                content: '';
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: #9c9a9a;
                top: -8px;
                left: 50%;
                margin-left: -8px;
            }
        ;
        }

        .path-list-active {
            float: left;
            font-size: 14px;
            padding: 20px 0 0;
            width: 25%;
            text-align: center;
            border-top: 1px solid @color-primary;
            position: relative;
            &:before {
                position: absolute;
                content: '';
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: @color-primary;
                top: -8px;
                left: 50%;
                margin-left: -8px;
            }
        ;
        }

    }

</style>