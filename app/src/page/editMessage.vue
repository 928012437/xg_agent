<template>
<div class="content">
	<div class="content">
		<top></top>
		<div class="content add-follow-content">
			<form action="" id='editForm'>
				<Common :info="detail" ></Common>

				<div class="list-block">
					<ul>
						<li v-for='(option,index) in otherData'>
							<div class="item-content">
								<div class="item-media"><i class="icon icon-form-name hide">*</i></div>
								<div class="item-inner">
									<div class="item-title label">{{option.name}}</div>
									<div class="item-input">
										<input type="text" placeholder="请选择" @click='check(option.name)' :value='option.value' :name='option.namef'>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>

				<check-card :checkData='checkData'></check-card>
				<!--<cell></cell>-->
				
			</form>
		</div>

		<popup  v-model='show' height='200px'>
			<header class="bar bar-nav" >
				<div @click='show=!show' class="close" >
					<button class="button">确定</button>
				</div>
				<h1 class="title">{{popupName}}</h1>

			</header>
			<picker  v-for='item in otherData' :data='item.values' v-model='item.value' v-if='popupName===item.name'></picker>
		</popup>

	</div>
	<button type="button" class="bar bar-tab bottom-btn" @click="updatecustomer">
		保存
	</button>

</div>
</template>
<script type='text/ecmascript-6'>
import checkCard from '../components/form/check-card.vue'
import Top from '../components/header.vue'
import Common from '../components/form/common.vue'
import Cell from '../components/form/cell.vue'
import {serialize} from '../assets/serialize.js'
import axios from 'axios';
import Popup from '../components/popup/index.vue'
import Picker from '../components/picker/index.vue'

export default{
	components:{
		checkCard,
		Popup,
		Picker,
		Top,
		Common,
		Cell
	},
	data(){
		return{
			show:false,
			popupName:'',
			otherData:[
				{
					name:'',
					namef:'',
					value:[],
					values:[[
						{
							name:'',
							value:''
						},
					]]
				},
			],
			checkData:[
				{
					title:'',
					name:'',
					value:[]
				}
			],
			detail:[]
		}

	},
	created(){
		// alert(this.$route.params.id)
		this.getdetail()
		this.getorderoption()
	},
	mounted(){
		$('editForm').serializeArray()
	},
	methods:{
		check(param,option){
			this.show=!this.show
			this.popupName=param
		},
		getdetail(){
			var id=this.$route.params.id;
			var that = this;
			axios.post(urlstr + 'user.counselors.getdetail&id='+id).then(function (res) {
				if (res.data.code == 3) {
					that.detail=res.data.data;
				}
			});
		},
		getorderoption(){
			var id=this.$route.params.id;
			var that = this;
			axios.post(urlstr + 'user.counselors.getoption&id='+id).then(function (res) {
				if (res.data.code == 3) {
					that.otherData=[];
					res.data.data.radios.forEach(function(v){
						var tempvalues=[[]];
						v.opt.forEach(function(v2){
							var temp2={name: v2.name,value:v2.id+':'+v2.name};
							tempvalues[0].push(temp2);
						});

						var temp={name: v.name,namef:'rad_'+v.id,value:[v.value],values:tempvalues};
						that.otherData.push(temp);
					});
					res.data.data.selects.forEach(function(v){
						var tempvalues=[[]];
						v.opt.forEach(function(v2){
							var temp2={name: v2.name,value:v2.id+':'+v2.name};
							tempvalues[0].push(temp2);
						});

						var temp={name: v.name,namef:'sel_'+v.id,value:[v.value],values:tempvalues};
						that.otherData.push(temp);
					});
					that.checkData=[];
					res.data.data.checks.forEach(function(v){
						var tempvalues=[];
						v.opt.forEach(function(v2){
							var temp2={name: 'che_'+v.id+'[]',value:v2.name,id:v2.id,checked:v2.checked};
							tempvalues.push(temp2);
						});
						var temp={title: v.name,name:'che_'+v.id,value:tempvalues};
						that.checkData.push(temp);
					});
				}
			});
		},
		updatecustomer(){
			var id=this.$route.params.id;
			var fromdata=serialize(document.forms[0]);
			var that=this;
			axios.post(urlstr + 'user.counselors.updatecustomer&id='+id+'&'+fromdata).then(function (res) {
				if (res.data.code == 4) {
					if (res.data.status == 1) {
						that.$router.push('/addons/xg_agent/app/customer/cusinfo/'+id);
					}
				}
			});
		}
	}

}

</script>

<style lang='less' scoped>
@import '../style/less/variables.less';

.add-follow-content{
	margin-top: 44px;
	margin-bottom: 54px;
}
.close{
	position: absolute;
	top:2px;
	right: 15px;
	z-index: 888
}

</style>