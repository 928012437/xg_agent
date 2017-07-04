<template>
 <div>
		<div class="card">
	    <div class="card-header">
		   
		    	<slot name="crumb"></slot>
		    	<slot name='right'></slot>
		    	
	    </div>

	    <div class="card-content">
	      <div class="card-content-inner">
	      	 	<slot name='content'></slot>
	      </div>
	    </div>
	   
	  </div>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	
}

</script>

<style lang='less' scoped>
.card{
	margin: 0;
	margin-bottom: 8px
}
.card-content-inner{
  padding: 0;
}
</style>