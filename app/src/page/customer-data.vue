<template>
    <div class="content">
        <div class="content">
            <top></top>
            <search height='44px'></search>
            <div class="content data-content">
                <data-lists :selectItems='cusList'></data-lists>
            </div>
        </div>
        <nav class="bar bar-tab">
            <div>
                <label for="">
                    <input type="text">
                </label>
            </div>
            <div></div>
        </nav>
    </div>
</template>
<script type='text/ecmascript-6'>
    import Search from '../components/search.vue'
    import Top from '../components/header.vue'
    import DataLists from '../components/data-list.vue'
    import axios from 'axios';

    export default{
        components: {
            Top,
            Search,
            DataLists
        },
        created(){
            this.$store.commit('footer', false);
            this.getpool();
        },
        data(){
            return {
                cusList: {
                    length: 0,
                    list: [ ]
                },
                page:1,
            }
        },
        methods: {
            getpool(){
                var that = this;
                axios.post(urlstr + 'user.counselors.getpool&page='+this.page).then(function (res) {
                    if (res.data.code == 3) {
                        that.length=res.data.count;
                        res.data.data.forEach(function(v){
                            that.cusList.list.push(v);
                        })
                    }
                });
            }
        }
    }


</script>

<style lang='less' scoped>


</style>