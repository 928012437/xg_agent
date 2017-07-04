<template>
   <div>
		<ul v-show='ishow'>
			<li v-for='(item,index) in items'  @click='show(index,item)'>
				{{item}}
			</li>
		</ul>

		<button>隐藏显示</button>
   </div>
</template>
<script type='text/ecmascript-6'>
export default{
    props:['ishow'],
    data(){
        return{
        	items:[
        		'努力',
        		'奋斗',
        		'活成自己想要的样子'
        	],
        	myshow:this.ishow
        }
    },
	created(){
		alert(Object.keys(this.$data))
	},

	methods:{
		show(index,ele){
			alert( index+'-----'+ ele + this.url )
		}
	},
	computeds:{
	},

}

</script>
<style lang='less' scoped>

</style>