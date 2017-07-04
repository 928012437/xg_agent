<template>
 <div class="list-block media-list ">
	<ul>
	  <li class="project-list" v-for='item in projectData'>
	    <label class="label-checkbox item-content">
	      <div class="item-inner">
	        <div class="item-title-row">
	          <div class="item-title">{{item.name}}</div>
	        </div>
	      </div>
	       <input type="checkbox" name="my-radio" v-model='item.checked'>
	      <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
	    	</label>
	  </li>
	</ul>


</div>
</template>
<script type='text/ecmascript-6'>

export default{
	created(){
		 this.$store.commit('footer',false);
		 alert(this.$route.params.data.checkedGroup)
	},
	data(){
		return{
			projectData:[
				{
					name:'世纪城',
					checked:false
				},
				{
					name:'银狐蓝山',
					checked:true
				},
				{
					name:'世纪城',
					checked:true
				},
				{
					name:'银狐蓝山',
					checked:false
				}
			]
		}
	}
	
}

</script>

<style lang='less' scoped>
.list-block{
	margin-top: 0px;
}
.project-list{
	padding-right: 0.75rem;
}

</style>