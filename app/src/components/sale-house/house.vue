<template>
    <div class="content">

        <top></top>
        <div class="content house-list-content margin-top">

            <div class="house-list" v-for='estate in estates'>
                <h4 class="house-name"><span>{{estate.estateName}}</span></h4>
                <div v-for='project in estate.projects' class="pro-wrapper">
                    <span class="pro-name">{{project.title}}</span>
                    <ul class="pro-list">
                        <li  class="estate"  v-for='area in project.estateList' >
                            <router-link :to="'/house/'+area.id">
                                {{area.name}}
                                <span class="text">剩余{{area.num}}套</span>
                            </router-link>
                        </li>

                    </ul>
                </div>
            </div>

        </div>



    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../header.vue'
    import axios from 'axios';

    export default{
        data(){
            return{
                check:true,
                estates:[
                    {
                        estateName:'',
                        projects:[
                            {
                                title:'',
                                estateList:[
                                    {
                                        name:'',
                                        num:0,
                                        id:0
                                    }
                                ]
                            }
                        ]

                    },
                ]
            }
        },
        components:{
            Top
        },
        created(){
            this.getcholou()
        },
        methods:{
            getcholou(){
                var that = this;
                axios.post(urlstr + 'user.fangyuan.getcholou').then(function (res) {
                    if (res.data.code == 3) {
                        that.estates=[];
                        res.data.data.forEach(function(v){
                            var tempprojects=[];
                            v.qu.forEach(function(v2){
                                var tempestateList=[];
                                v2.hao.forEach(function(v3){
                                    var temp3={name:v3.name,num:v3.surpl,id:v3.id}
                                    tempestateList.push(temp3);
                                });
                                var temp2={title:v2.name,estateList:tempestateList};
                                tempprojects.push(temp2);
                            });
                            var temp={estateName: v.louname+'-'+ v.name,projects:tempprojects};
                            that.estates.push(temp);
                        });
                    }
                });
            }
        }

    }

</script>

<style lang='less' scoped>
    @import '../../style/less/mixins.less';
    @import '../../style/less/variables.less';

    .content{
        /*margin-top:44px;*/
        .row{
            margin-top: 10px;
            text-align: center;
            background: #fff;
            padding: 10px;
            margin-top: 0;
        }
        .house-tag-icon{
            display: inline-block;
            width: 15px;
            height: 15px;
            background: @color-primary;
            margin-right: 5px;
        }
        .unsale{
            background: @color-text-gray-light
        }

        .house-list{
            .house-name{
                font-weight: normal;
                border-bottom: 1px solid @color-split;
                padding:0px 10px 8px;
                margin-bottom: 5px;
                span{
                    border-bottom: 2px solid @color-primary;
                    padding-bottom: 8px;
                }
            }

            .estate{
                width: 40%;
                height: 100px;
                background: @color-text-gray-light;
                margin: 10px;
                text-align: center;
                line-height: 80px;
                color: #fff;
                font-size: 20px;
                position: relative;
                display: inline-block;
                background:@color-primary;
                .text{
                    position: absolute;
                    display: block;
                    width: 100%;
                    height: 30px;
                    line-height: 30px;
                    font-size: 14px;
                    bottom: 0;
                    left: 0;
                    background: darken(@color-primary,15%);
                }
                a{
                    color: #fff;
                }
            }

            .estateOnHandle{
                background: #fff;
                color: @color-text
            }
        }
    }
    .pro-wrapper{
        /*padding: 0 15px;*/
        .pro-list{
            margin: 0
        }
        .pro-name{
            font-size: 14px;
            padding-left: 15px;
            color: @color-text-gray
        }
    }
</style>