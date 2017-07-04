<template>
    <div>
	   	<top></top>
		<cus-search></cus-search>
		<cus-list :selectItems='cusList'></cus-list>
    </div>
</template>
<script type='text/ecmascript-6'>
import cusSearch from '../search.vue';
import Top from '../top.vue';
import cusList from '../cus-list/cus.vue';
import {mapGetters} from 'vuex'

export default{
	data(){
		return{
			cusList:[
			{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:0,
					tags:['问询'],
					checked:true
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:1,
					tags:['问询','看房'],
					checked:false
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:2,
					tags:['问询'],
					checked:false
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:3,
					tags:['问询'],
					checked:false,
					checked:true
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:4,
					tags:['问询'],
					checked:false
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:5,
					tags:['问询'],
					checked:true
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:6,
					tags:['问询'],
					checked:true
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:7,
					tags:['问询'],
					checked:true
				},
				{
					name:'拉拉',
					tel:'1575486484',
					date:'2017-12-32',
					sales:'小红',
					id:8,
					tags:['问询'],
					checked:true
				},
			]
		}
	},
	created(){
		//接受用户数据
		this.$store.commit('COMM_CONF',{
                isFooter:false,
               
            });
	},
	components:{
		cusSearch,
		Top,
		cusList
	}
}

</script>

<style lang='less' scoped>

.cus-list{
	margin-top: 0px;	
}


</style>