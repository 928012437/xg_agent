<template>
 <div>
  <header class="bar bar-nav">
  	<span class="icon icon-left pull-left" @click='goBack' style=""></span>
    <h1 class="title">{{$route.name}}</h1>
  </header>
</div>
</template>
<script type='text/ecmascript-6'>

export default{
	methods:{
		goBack(){
			history.go(-1)
		}
	}
}

</script>

<style lang='less' scoped>


</style>