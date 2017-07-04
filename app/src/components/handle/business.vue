<template>
 <div>
 
	<over>
		<over-items  v-for='(list,index) in doData' :dataItem='list'  pageName='bussiness'></over-items>
		

	</over>
</div>
</template>
<script type='text/ecmascript-6'>
import Over from '../handle-list/over.vue'
import OverItems from '../handle-list/over-item.vue'
import axios from 'axios';

export default{
	components:{
		OverItems,
		Over
	},
	data(){
		return{
			doData:[
			]
		}
	},
	created(){
		//获取数据
		this.getcuibancategory()
	},
	methods: {
		getcuibancategory(){
			var that = this;
			axios.post(urlstr + 'user.counselors.getcuibancategory').then(function (res) {
				if (res.data.code == 3) {
					that.doData=res.data.data
					//res.data.data.forEach(function(v){
					//	that.doData.push(v.title)
					//})
				}
			});
		}
	}
	
}

</script>

<style lang='less' scoped>


</style>