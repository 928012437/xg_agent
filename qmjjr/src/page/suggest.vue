<template>
    <div class="content">
        <top></top>
        <div class="content suggenst-content margin-top">
            <div class="content">
                <div class="list-block">
                    <form action="">
                        <ul>
                            <!-- Text inputs -->
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">手&nbsp;&nbsp;&nbsp;&nbsp;机&nbsp;&nbsp;&nbsp;&nbsp;号:</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入手机号码" v-model="mobile" >
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="align-top">
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-comment"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">投诉和建议:</div>
                                        <div class="item-input">
                                            <textarea v-model="content" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
            <button class="bottom-btn bar bar-tab" @click='complain'>
                提交
            </button>

        </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    import axios from 'axios';

    export default{
        data(){
            return {
                mobile: '',
                content:''
            }
        },
        components: {
            Top
        },
        created(){
            this.$store.commit('footer', false);
        },
        methods: {
            complain(){
                var that = this;
                axios.post(urlstr + 'qmjjr.complain&mobile='+this.mobile+'&complain='+this.content).then(function (res) {
                    if (res.data.code == 4) {
                        if(res.data.status == 1){
                            that.$router.push('/addons/xg_agent/qmjjr/home');
                        }
                    }
                });
            }
        }

    }

</script>

<style lang='less' scoped>
    .list-block {
        margin: 0;
        .item-content {
            font-size: 14px;
            input {
                font-size: 14px;
            }
        }
    }

</style>