<template>
    <div>
        <div class="list-block">
            <ul>
                <!-- Text inputs -->
                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                        <div class="item-inner">
                            <div class="item-title label">姓名</div>
                            <div class="item-input">
                                <input type="text" placeholder="请输入" id="name" name="name" >
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-email required">*</i></div>
                        <div class="item-inner">
                            <div class="item-title label">性别</div>
                            <div class="item-input">


                                <label for="man" class="form-sex">
                                    <input type="radio" name="sex" value='1' id='man' checked>
                                    <i class="icon icon-form-checkbox"></i>
                                    &nbsp;男
                                </label>
                                &nbsp;&nbsp;&nbsp;
                                <label for="woman" class="form-sex">
                                    <input type="radio" name="sex" value='0' id='woman'>
                                    <i class="icon icon-form-checkbox"></i>
                                    &nbsp;女
                                </label>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon required">*</i></div>
                        <div class="item-inner">
                            <div class="item-title label">手机</div>
                            <div class="item-input">
                                <input type="text" placeholder="请输入" id='tel' name="tel">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon required">*</i></div>
                        <div class="item-inner">
                            <div class="item-title label">意向项目</div>
                            <div class="item-input">
                                <input type="text" placeholder="请选择" @click="check('意向项目')" :value='type' id="lid" name="lid" readonly='true'>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="align-top">
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-comment"></i></div>
                        <div class="item-inner">
                            <div class="item-title label">备注</div>
                            <div class="item-input">
                                <textarea id="mark" name="mark" ></textarea>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <popup v-model='show' >
                <header class="bar bar-nav">
                    <div @click='show=!show' class="close">
                        <div class="button pull-right">确定</div>
                    </div>
                    <h1 class="title">{{popupName}}</h1>

                </header>
                <picker :data='datahouse' v-model="type" v-if="popupName==='意向项目'" ></picker>
                <picker  v-for='item in otherData' :data='item.values' v-model='item.value' v-if='popupName===item.name'></picker>
            </popup>

        </div>

        <div class="list-block">
            <ul>
                <li v-for='(option,index) in otherData'>
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-name hide">*</i></div>
                        <div class="item-inner">
                            <div class="item-title label">{{option.name}}</div>
                            <div class="item-input">
                                <input type="text" placeholder="请选择" @click='check(option.name)' :value='option.value' :name='option.namef'>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <check-card :checkData='checkData'></check-card>

    </div>
</template>
<script type='text/ecmascript-6'>
    import Popup from './popup/index.vue'
    import Picker from './picker/index.vue'
    import axios from 'axios'
    import checkCard from '../components/check-card.vue'

    export default{
        data(){
            return {
                type: [],
                show: false,
                popupName:'',
                datahouse: [[{name: '0', value: '1:1'}, {name: '1', value: '1'}]],
                otherData:[
                    {
                        name:'',
                        namef:'',
                        value:[],
                        values:[[
                            {
                                name:'',
                                value:''
                            },
                        ]]
                    },
                ],
                checkData:[
                    {
                        title:'',
                        name:'',
                        checked:false,
                        value:[]
                    }
                ],
            }
        },
        components: {
            Popup,
            Picker,
            checkCard
        },
        created(){
            this.$store.commit('footer', false);
        },
        mounted(){
            this.getloupanlist();
            this.getorderoption()
        },
        methods: {
            check(param){
                this.show=!this.show
                this.popupName=param
            },
            getloupanlist(){
                var that = this;
                axios.post(urlstr + 'qmjjr.getloupanoption').then(function (res) {
                    if (res.data.code == 3) {
                        var temp = [];
                        res.data.data.forEach(function (v) {
                            temp.push({name: v.title, value: v.id + ':' + v.title})
                        });
                        that.datahouse = [temp];
                        that.type = [temp[0].value]
                    } else {
                        alert(res.data.message);
                        that.$router.push('/addons/xg_agent/qmjjr/index');
                    }
                });
            },
            getorderoption(){
                var that = this;
                axios.post(urlstr + 'qmjjr.getoption').then(function (res) {
                    if (res.data.code == 3) {
                        that.otherData=[];
                        res.data.data.radios.forEach(function(v){
                            var tempvalues=[[]];
                            v.opt.forEach(function(v2){
                                var temp2={name: v2.name,value:v2.id+':'+v2.name};
                                tempvalues[0].push(temp2);
                            });

                            var temp={name: v.name,namef:'rad_'+v.id,value:[],values:tempvalues};
                            that.otherData.push(temp);
                        });
                        res.data.data.selects.forEach(function(v){
                            var tempvalues=[[]];
                            v.opt.forEach(function(v2){
                                var temp2={name: v2.name,value:v2.id+':'+v2.name};
                                tempvalues[0].push(temp2);
                            });

                            var temp={name: v.name,namef:'sel_'+v.id,value:[],values:tempvalues};
                            that.otherData.push(temp);
                        });
                        that.checkData=[];
                        res.data.data.checks.forEach(function(v){
                            var tempvalues=[];
                            v.opt.forEach(function(v2){
                                var temp2={name: 'che_'+v.id+'[]',value:v2.name,id:v2.id};
                                tempvalues.push(temp2);
                            });
                            var temp={title: v.name,name:'che_'+v.id,checked:false,value:tempvalues};
                            that.checkData.push(temp);
                        });
                    }
                });
            },
        }
    }

</script>

<style lang='less' scoped>
    @import '../style/less/mixins.less';
    @import '../style/less/variables.less';

    .list-block {
        margin: 0px auto 8px;
        .item-content {
            font-size: 14px;
            input {
                font-size: 14px;
            }
        }
    }

    .form-wrap {
        min-height: 600px;
        margin-top: 8px
    }

    .item-after {
        text-align: center;
        font-size: 12px
    }

    .hide {
        opacity: 0
    }

    .form-sex input[type="radio"] {
        display: none;
    }

    .form-sex .icon {
        font-family: "iconfont-sm" !important;
        font-style: normal;
        display: inline-block;
        vertical-align: middle;
        background-size: 100% auto;
        background-position: center;
        -webkit-font-smoothing: antialiased;
        -webkit-text-stroke-width: 0.2px;
        -moz-osx-font-smoothing: grayscale;
    }

    .form-sex i.icon-form-checkbox {
        width: 1.1rem;
        height: 1.1rem;
        position: relative;
        border-radius: 1.1rem;
        border: 1px solid #c7c7cc;
        box-sizing: border-box
    }

    .form-sex i.icon-default {
        width: 0.8rem;
        height: 0.8rem;
    }

    .form-sex i.icon-form-checkbox:after {
        content: ' ';
        position: absolute;
        left: 50%;
        margin-left: -0.3rem;
        top: 50%;
        margin-top: -0.2rem;
        width: 0.6rem;
        height: 0.45rem;
    }

    .form-sex input[type="radio"]:checked + i.icon-form-checkbox {
        background: @color-primary;
        border: none;
    }

    .form-sex input[type="radio"]:checked + i.icon-form-checkbox:after {
        background: no-repeat center;

        background-size: 0.6rem 0.45rem;
    }


</style>