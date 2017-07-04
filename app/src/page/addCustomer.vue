<template>
    <div class="content">
        <div class="content">
            <top></top>
            <div class="content form-wrap content-wrapper">
                <form action="">
                    <common :info="info" ></common>
                    <div class="list-block">
                        <ul>
                            <!-- Text inputs -->
                            <li v-for='(option,index) in data'>
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">{{option.name}}</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请选择" @click='check(option.name)' :value='option.value' :name='option.namef'>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li >
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">跟进内容</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入" v-model='phraseValue[0]' name='gjnr' >
                                        </div>
                                        <div  class="phraseType" @click="check('常用语')">常用语</div>
                                    </div>
                                </div>
                            </li>
                            <li >
                                <div class="item-content">
                                    <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                                    <div class="item-inner">
                                        <div class="item-title label">下次跟进</div>
                                        <div class="item-input">
                                            <datetime v-model="value2" format="YYYY-MM-DD HH:mm"  title="start time" ></datetime>

                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
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

                </form>


            </div>
            <popup  v-model='show' height='200px'>
                <header class="bar bar-nav" >
                    <div @click='show=!show' class="close" >
                        <button class="button">确定</button>
                    </div>
                    <h1 class="title">{{popupName}}</h1>

                </header>
                <picker  v-for='item in data' :data='item.values' v-model='item.value' v-if='popupName===item.name'></picker>
                <picker  v-for='item in otherData' :data='item.values' v-model='item.value' v-if='popupName===item.name'></picker>
                <picker  v-for='item in phraseData' :data='item.values' v-model='phraseValue' v-if='popupName===item.name'></picker>
            </popup>

        </div>
        <!-- @on-change="change" -->

        <button class="bar bar-tab bottom-btn" @click='insertcustomer'>
            保存
        </button>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    import Common from '../components/form/common.vue'
    import Popup from '../components/popup/index.vue'
    import Picker from '../components/picker/index.vue'
    import datetime from '../components/datetime/index.vue'
    import Radioes from '../components/form/radio.vue'
    import checkCard from '../components/form/check-card.vue'
    import Vue from 'vue'
    import {serialize} from '../assets/serialize.js'
    import axios from 'axios';



    export default{
        data(){
            return{
                show:false,   //弹出层显示
                popupName:'',//弹出层标题
                value2:'',
                data:[
                    {
                        name:'跟进方式',
                        namef:'gjfs',
                        value:[],
                        values:[[

                        ]]
                    },
                    {
                        name:'意向级别',
                        namef:'yxjb',
                        value:[],
                        values:[[
                        ]]
                    }
                ],
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
                phraseData:[
                    {
                        name:'常用语',
                        value:[],
                        values:[[
                        ]]
                    }
                ],
                phraseValue:[],

                value:'',

                info:[],
            }
        },
        mounted(){

        },
        components:{
            Top,
            Common,
            Popup,
            Picker,
            datetime,
            Radioes,
            checkCard

        },
        created(){
            this.$store.commit('footer',false);
            this.getgjfs()
            this.getyxjb()
            this.getgjnr()
            this.getorderoption()
        },
        methods:{
            check(param,option){
                this.show=!this.show
                this.popupName=param
            },
            getgjfs(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getgjfs').then(function (res) {
                    if (res.data.code == 3) {
                        res.data.data.forEach(function(v){
                            var temp={ name: v.name,value:v.name };
                            that.data[0].values[0].push(temp);
                        });
                    }
                });
            },
            getyxjb(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getyxjb').then(function (res) {
                    if (res.data.code == 3) {
                        res.data.data.forEach(function(v){
                            var temp={ name: v.name,value:v.name };
                            that.data[1].values[0].push(temp);
                        });
                    }
                });
            },
            getgjnr(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getgjnr').then(function (res) {
                    if (res.data.code == 3) {
                        res.data.data.forEach(function(v){
                            var temp={ name: v.cont,value:v.cont };
                            that.phraseData[0].values[0].push(temp);
                        });
                    }
                });
            },
            getorderoption(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getoption').then(function (res) {
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

            insertcustomer(){
                var fromdata=serialize(document.forms[0]);
                var that=this;
                axios.post(urlstr + 'user.counselors.insertcustomer&gj_xc='+this.value2+"&"+fromdata).then(function (res) {
                    if (res.data.code == 4) {
                        if (res.data.status == 1) {
                            that.$router.push('/addons/xg_agent/app/customer');
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
    .check-box{
        padding: 0px 0px 0.1rem 0rem;
        background: #ccc
    }
    .form-wrap{
        height: 530px;
        margin-top: 44px;
        .list-block {
            margin: 15px auto;
            .item-content{
                font-size: 14px;
                input{
                    font-size: 14px;
                }
            }
        }
    }

    .close{
        position: absolute;
        top:2px;
        right: 15px;
        z-index: 888
    }
    .phraseType{
        font-size: 12px;
        color: @color-primary;
        width: 25%;
    }

</style>