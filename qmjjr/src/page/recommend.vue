<template>
    <div class="content">
        <top></top>
        <div class="content margin-top">
            <form action="">
                <edit></edit>
            </form>
        </div>
        <button type="button" class="bar bar-tab bottom-btn" @click="recommend">
            保存
        </button>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue';
    import Edit from '../components/edit.vue';
    import {serialize} from '../assets/serialize.js'
    import axios from 'axios';

    export default{
        components: {
            Top,
            Edit

        }, methods: {
            recommend(){
                var fromdata=serialize(document.forms[0]);
                var that=this;
                axios.post(urlstr + "qmjjr.insertcustomer&"+fromdata).then(function (res) {
                    console.log(res)
                        if (res.data.status == 1) {
                            that.$router.push('/addons/xg_agent/qmjjr/customer');
                        }else if(res.data.status == 0){
                            alert(res.data.message);
                        }
                });
            }
        }
    }

</script>

<style lang='less' scoped>


</style>