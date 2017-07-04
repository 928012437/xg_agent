<template>
<div class="content">
	<div class="content ">
		<top></top>
		<div class="content add-follow-content">
				<check-card :checkData='checkData'></check-card>
		</div>
	</div>
	<button type="button" class="bar bar-tab bottom-btn">
		保存
	</button>
</div>
</template>
<script type='text/ecmascript-6'>
import checkCard from '../components/form/check-card.vue'
import Top from '../components/header.vue'

export default{
	components:{
		checkCard,
		Top
	},
	data(){
		return{
			checkData:[
				{
					title:'跟进状态',
					name:'follow',
					type:'checkbox',
					checked:false,
					value:['问询','看房','认购','签约'],
					arr:[]
				},
				{
					title:'跟进方式',
					name:'way',
					type:'radio',
					checked:false,
					value:['来电','来访','上门','其他'],
					arr:[]
				},
				{
					title:'跟进方式',
					name:'way',
					type:'radio',
					checked:false,
					value:['来电','来访','上门','其他'],
					arr:[]
				},
				{
					title:'跟进方式',
					name:'way',
					type:'radio',
					checked:false,
					value:['来电','来访','上门','其他'],
					arr:[]
				}
			]
		}
	}
}

</script>

<style lang='less' scoped>
.add-follow-content{
	margin-top: 44px;
}

</style>