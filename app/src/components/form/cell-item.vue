<template>
 <div>
	<ul>
      <!-- Text inputs -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-name"></i></div>
          <div class="item-inner">
            <div class="item-title label">
            	<slot></slot>
            </div>
            <div class="item-input" >
				<slot name="input">
					<input type="text" placeholder="请输入"></slot>
				</slot>
            </div>
          </div>
        </div>
      </li> 
     </ul>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	
}

</script>

<style lang='less' scoped>


</style>