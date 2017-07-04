<template>

    <div class="content">
        <top></top>
        <div class="list-block">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">房间名称：</div>
                        <div class="item-after">{{info.title}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">户型：</div>
                        <div class="item-after">{{info.huxing}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">房间结构：</div>
                        <div class="item-after">{{info.jiegou}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">楼层：</div>
                        <div class="item-after">{{info.cengnum}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">朝向：</div>
                        <div class="item-after">{{info.chaoxiang}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">建筑面积：</div>
                        <div class="item-after">{{info.jz_mj}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">套内面积：</div>
                        <div class="item-after">{{info.tn_mj}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">建筑单价：</div>
                        <div class="item-after">{{info.jz_dj}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">套内单价：</div>
                        <div class="item-after">{{info.tn_dj}}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">标准总价：</div>
                        <div class="item-after">{{info.zongjia}}</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    import axios from 'axios';

    export default{
        data(){
            return{
                info:[]
            }
        },
        components: {
            Top
        },
        created(){
            this.$store.commit('footer', false)
            this.fangyuaninfo()
        },
        methods: {
            fangyuaninfo(){
                var id = this.$route.params.num;
                var that = this;
                axios.post(urlstr + 'user.fangyuan.fangyuaninfo&id=' + id).then(function (res) {
                    if (res.data.code == 3) {
                        that.info = res.data.data;
                    }
                });
            }
        }
    }

</script>

<style lang='less' scoped>
    .list-block {
        margin-top: 44px;
        font-size: 14px;
        .item-after {
            width: 60%;
            color: #999;
        }
    }

</style>