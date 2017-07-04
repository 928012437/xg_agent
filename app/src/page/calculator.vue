<template>
    <div class="content" >
        <top></top>
<iframe src="../src/page/calculator" class="iframe margin-top" frameborder="no" ></iframe>

    </div>
</template>
<script type='text/ecmascript-6'>
    import Top from '../components/header.vue'
    export default{
        components:{
            Top
        }
    }

</script>

<style lang='less' scoped>
.iframe{
    display:block;
    width: 100%;
    height: 90%;
}

</style>