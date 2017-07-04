<template>
   <div class="content">
        这里是昨天
    </div>
</template>
<script type='text/ecmascript-6'>
export default{
    props:[],
    data(){
        return{}
    },
    created(){
       // alert('sfsf');
       // alert(this.$route.query.id)
       alert(this.$route.params.id)
    },
    mounted(){

      // alert(this.$route.query.id)
    },
    methods:{
    },
    computeds:{
    },

    }

</script>
<style lang='less' scoped>

</style>
