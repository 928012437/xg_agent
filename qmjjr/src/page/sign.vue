<template>
    <div class="content">
        <top></top>
        <div class="content margin-top">
            <div class="sign-header">

            </div>
            <div class="button-wrapper">

                <form action="">
                    <div class="list-block content-block">

                        <ul>
                            <!-- Text inputs -->
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="iconfont">&#xe627;</i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">用户名</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入用户名" v-model='names'>
                                        </div>
                                        <div class="error" v-text='formErrors.names'></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="iconfont">&#xe687;</i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">密码</div>
                                        <div class="item-input">
                                            <input type="password" placeholder="请输入密码" v-model='pwd'>
                                        </div>
                                        <div class="error" v-text='formErrors.pwd'></div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button class="button button-big" type="button" @click='formSubmit'>
                            登　录
                        </button>
                    </div>
                </form>

            </div>

        </div>
        <div style="display:none">{{errors}}</div>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    import axios from 'axios';

    export default{
        components: {
            Top,
        },
        created(){
            this.$store.commit('footer', false);
        },
        vuerify: {
            names: {
                test: 'required',
                message: '用户名不得为空'
            },
            pwd: {
                test: 'required',
                message: '密码不得为空'
            },
        },
        data(){
            return {
                formErrors: '',
                names: '',
                pwd: ''
            }

        },
        computed: {
            errors(){
                this.formErrors = this.$vuerify.$errors
                return this.$vuerify.$errors
            }
        },
        methods: {
            formSubmit(){
                if (this.$vuerify.check()) {
                    var that = this;
                    axios.post(urlstr + 'qmjjr.login.loginweb&accounts=' + this.names + '&password=' + this.pwd).then(function (res) {
                        if (res.data.code == 2) {
                            that.setCookie('token', res.data.message, 1);
                            that.$router.push('/addons/xg_agent/qmjjr/index');
                        } else if (res.data.code == 0) {
                            alert(res.data.message);
                        }
                    });
                }

            },

            setCookie(c_name, value, expiredays) {

                var exdate = new Date();

                exdate.setDate(exdate.getDate() + expiredays);

                document.cookie = c_name + "=" + escape(value) + ((expiredays == null) ? "" : "; expires=" + exdate.toGMTString());

            }

        }

    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .content-block {
        margin-top: 40%;
    }

    /* .sign-header{
        width: 100%;
        height: 160px;
        background: @color-primary
    } */

    .button {
        margin: 25px auto;
        padding: 0 2.4rem;
        color: #fff;
        font-size: 18px;
        width: 100%;
        background: @color-primary
    }

    .item-content {
        font-size: 14px;
        input {
            font-size: 14px;
        }
    }

    .iconfont {
        color: @color-primary;
        font-size: 18px;
    }

</style>