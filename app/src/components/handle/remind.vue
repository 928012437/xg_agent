<template>
    <div>
        dsf
        <over>
            <over-items v-for='(list,index) in remindData' :dataItem='list' pageName='remind'></over-items>

        </over>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Over from '../handle-list/over.vue'
    import OverItems from '../handle-list/over-item.vue'
    import axios from 'axios';

    export default{
        components: {
            OverItems,
            Over
        },
        data(){
            return {
                remindData: [
                ]
            }
        },
        created(){
            //获取数据
            this.getgenjincategory()
        },
        methods: {
            getgenjincategory(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getgenjincategory').then(function (res) {
                    if (res.data.code == 3) {
                        that.remindData=res.data.data
                        //res.data.data.forEach(function(v){
                        //    that.remindData.push(v.title)
                        //})
                    }
                });
            }
        }

    }

</script>

<style lang='less' scoped>


</style>