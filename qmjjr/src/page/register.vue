<template>
    <div class="content">
        <header id="reg-header">
            <div class="project-img">
                <img :src="headurl" style="width:100%">
            </div>
        </header>

        <form action="" id="registe-form">
            <p class="reminded-text"><strong>提示：</strong>请输入正确的姓名以及电话，否则可能无法结佣。</p>
            <div class="card">
                <div class="card-content ">
                    <div class="list-block">
                        <ul>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">用户名：</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入用户名" name='username' v-model='username'>
                                        </div>
                                        <div class='error' v-text='formErrors.username'></div>
                                    </div>

                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">密　码：</div>
                                        <div class="item-input">
                                            <input type="password" name='pwd' placeholder="请输入密码" v-model='password'>
                                        </div>
                                        <div class='error' v-text='formErrors.password'></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">确认输入：</div>
                                        <div class="item-input">
                                            <input type="password" name='apwd' placeholder="请再次输入密码"
                                                   v-model='againword'>
                                        </div>
                                        <div class='error' v-text='formErrors.againword'></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">姓　名：</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入真实姓名" v-model='reallyName' name='rname'>
                                        </div>
                                        <div class='error' v-text='formErrors.reallyName'></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">手机号：</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入手机号码" v-model='tel' name='tel'>
                                        </div>
                                        <div class='error' v-text='formErrors.tel'></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">身份类型：</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请选择" @click="show=!show" :value='type'  name='type' readonly='true'>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">验证码：</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请选择">
                                        </div>
                                        <div>
                                            <!-- 获取验证码 -->
                                            <button type='button' class="button" @click='djs'
                                                    :disabled='dis'>{{time}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>


                        </ul>
                    </div>
                </div>

            </div>
            <div class="reg-btn">
                <label for="" @click='agreement=!agreement'> <input type="checkbox" :checked='agree'>同意<span
                        class="open-popup">服务协议及风险提示</span></label>
                <label for="">
                    <button type="button" class="button" id='regBtn' @click='formSubmit'>注册</button>
                </label>
                <p class="reg-reminded">销售员、经理请勿注册为经纪人</p>
            </div>
        </form>

        <popup v-model='agreement' >
            <section >
                <h3 class="agree-title">请阅读并接受服务协议</h3>
                <div class="content agrContent margin-top">
                    <p v-html="rule">

                    </p>
                </div>
                <nav class="bar bar-tab ">
                    <button class="button agr-btn " @click='agree=true;agreement=!agreement'>同意</button>
                </nav>
            </section>
        </popup>

        <popup v-model='show' >
            <header class="bar bar-nav" >
                <div @click='show=!show' class="close">
                    <div class="button pull-right">确定</div>
                </div>
                <h1 class="title">身份类型</h1>

            </header>
            <picker :data='houseType' v-model='type' ></picker>
        </popup>
        <div style="display:none">{{errors}}</div>

    </div>
</template>
<script type='text/ecmascript-6'>
    import Popup from '../components/popup/index.vue'
    import Picker from '../components/picker/index.vue'
    import {serialize} from '../assets/serialize.js'
    import axios from 'axios';


    export default{
        created(){
            this.$store.commit('footer', false);
            this.getheadimg()
        },
        components: {
            Popup,
            Picker
        },
        vuerify: {
            username: {
                test: 'required',
                message: '用户名不得为空'
            },
            password: {
                test: /^[\w!@#$%^&*.]{6,16}$/,
                message: '6-16位字母或数字'
            },
            againword: {
                test (val) {
                    return val === this.password
                },
                // test:val=> this.password===val,
                message: '密码不一致'
            },
            reallyName: 'name',
            tel: 'tel'


        },
        mounted(){
            this.reForm = document.forms[0]
            this.getidentity()
        },
        data(){
            return {
                formErrors: '',
                username: '',
                password: '',
                againword: '',
                reallyName: '',
                tel: '',
                show: false,
                houseType: [[{name: '12', value: '1:1'}]],
                type: [],
                time: '获取验证码 ',
                dis: false,
                reForm: null,
                checked: false,
                agreement: false,
                agree: false,
                headurl: '',
                rule: ''
            }
        },
        computed: {
            errors(){
                this.formErrors = this.$vuerify.$errors
                return this.$vuerify.$errors
            }
        },
        methods: {
            djs(){
                var vm = this
                vm.dis = true
                let times = 60;
                let timer = setInterval(function () {
                    times--
                    vm.time = '(' + times + ') 重新获取';
                    if (times <= 0) {
                        clearInterval(timer)
                        vm.time = '获取验证码'
                        vm.dis = false
                    }
                }, 1000)

            },
            formSubmit(){
                if (this.$vuerify.check()) {
                    var fromdata = serialize(this.reForm);
                    var that = this;
                    axios.post(urlstr + 'qmjjr.login.register&' + fromdata+'&tjid='+tjid).then(function (res) {
                        if (res.data.code == 2) {
                            that.$router.push('/addons/xg_agent/qmjjr/sign');
                        }else {
                            alert(res.data.message)
                        }
                    });
                }

            },
            getheadimg(){
                var that = this;
                axios.post(urlstr + 'qmjjr.opensource.getheadimg').then(function (res) {
                    if (res.data.code == 3) {
                        that.headurl = res.data.data;
                        that.rule = res.data.rule;
                    }
                });
            },
            getidentity(){
                var that = this;
                axios.post(urlstr + 'qmjjr.opensource.getidentity').then(function (res) {
                    if (res.data.code == 3) {
                        var tempht=[];
                        res.data.data.forEach(function (v) {
                            tempht.push({name: v.name, value:v.id+':'+ v.name})
                        });
                        that.houseType=[tempht];
                        that.type=[tempht[0].value]
                    }
                });
            }
        }
    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    #reg-header {
        width: 100%;
        height: 160px;
        background: @color-primary;
        position: relative;
        .project-img {
            position: absolute;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #fff;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            margin: 30px auto;
        }
    }

    .card {
        box-shadow: none;
        .item-content {
            font-size: 14px;
            input {
                font-size: 14px;
            }
        }

    }

    .reminded-text {
        font-size: 0.55rem;
        padding: 0 12px;
        margin-bottom: -5px;
        color: #999
    }

    .reg-btn label:first-child {
        font-size: 14px;
        color: #999;
        margin-bottom: 5px;
    }

    .reg-btn label {
        display: block;
        width: 100%;
        padding: 0 10px
    }

    .reg-btn button {
        display: inline-block;
        width: 100%;
        background: @color-primary;
        color: #fff;
        height: 40px
    }

    .reg-reminded {
        color: #999;
        font-size: 0.55rem;
        text-align: center;
        margin-top: 2px;
    }

    .error {
        font-size: 12px;
        color: @color-danger;
        width: 90%;
        text-align: right;
    }

    .agrContent {
        font-size: 14px;
        color: @color-text-gray;
        padding: 0px 15px;
        height: 76%;
        background: #ddd;
        margin: 0 15px;
    }

    .agree-title {
        padding-left: 15px;
        font-size: 16px;
        font-weight: normal;
        color: @color-primary
    }

    .agr-btn {
        margin: 0 auto;
        padding: 0 2.4rem;
        height: 1.6rem;
        background: @color-primary;
        color: #fff;
        width: 40%;
    }

</style>