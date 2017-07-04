<template>
 <div class="content">
   <top></top>	
   <div class="row man-house-top bar bar-nav margin-top" >
	<div class="col-80">
		<router-link to='/addons/xg_agent/app/checkhouse'>
		<i class="iconfont pull-left">&#xe612;</i>{{title}}</router-link>
	</div>
	<div class="col-20" @click='show=!show'>筛选</div>
</div>
<div class="content">

	<div class="card">
	 <tab activeClass='#33CC66'>
      <tab-item  v-on:click.native="demo1 = v.id" v-for="v in danyuan" >{{v.name}}</tab-item>
    </tab> 
	    <div class="card-content">
	        <div class="card-content-inner house-note">
	      		<ul class="row">
			      <li class="col-50"><i class="house-note-icon"></i> 已售&nbsp;&nbsp;{{bili.saleo_num}}套&nbsp;&nbsp;占{{bili.saleo_prop}}%</li>
			      <li class="col-50"><i class="house-note-icon unsaled"></i>未售&nbsp;&nbsp;{{bili.salen_num}}套&nbsp;&nbsp;占{{bili.salen_prop}}%</li>
			    </ul>
	        </div>
	    </div>
   
  </div>
<ul class="floor-num">
   	<li v-for='num in floorNum' :key='index'>{{num}}</li>
 </ul>

	<div id="wrapper" >
		<div class="ll">
			<house :check='check' :floors='floors'></house>
		</div>
	</div>

</div>
<popup v-model='show' height='400px'>
<header class="bar bar-nav">
  <h1 class="title">选择条件</h1>
</header>
	<div class="content condition-content">
		<check-card :checkData='checkData'></check-card>
	</div>
</popup>

</div> 
</template>

<script type='text/ecmascript-6'>
import  IScroll from 'iscroll';	
import House from '../components/sale-house/housing.vue';
import Top from '../components/header.vue'
import tab from '../components/tab/tab.vue';
import tabItem from '../components/tab/tab-item.vue';
import Popup from '../components/popup/index.vue';
import checkCard from '../components/form/check-card.vue';
import axios from 'axios';

export default{
		data(){
			return{
				index:'',
				check:true,
				demo1 : '',
				title:'',
				danyuan:[{}],
				floorNum:[],
				floors:[
					{
						houses:[
						]
					}
				],
				bili:[],
				show:false,
				checkData:[
					{
						title:'房间结构',
						name:'jg',
						type:'checkbox',
						value:['问询','看房','认购','签约']
					},
					{
						title:'跟进方式',
						name:'way',
						type:'radio',
						value:['来电','来访','上门','其他']
					},
					{
						title:'房间结构',
						name:'jg',
						type:'checkbox',
						value:['问询','看房','认购','签约']
					},
					{
						title:'跟进方式',
						name:'way',
						type:'radio',
						value:['来电','来访','上门','其他']
					}
				]

			}
		},
		components:{
			House,
			tab,
			tabItem,
			Top,
			Popup,
			checkCard
		},
		methods:{
			load(){				
					// 初始化滑块区域
					const myScroll = new IScroll('#wrapper',{
			   		scrollX: true,
			   		scrollY:true,
			   		tap: true
				})
					var vm =this
					$(document).on('tap','.unsaled',function(){
							var t= $(this).attr('data-id');
							vm.$router.push({ name: '户型信息', params: { num: t }})
							
					})
			},
			getcenghulist(did){
				var that = this;
				axios.post(urlstr + 'user.fangyuan.getcenghulist&id=' + did).then(function (res) {
					if (res.data.code == 3) {
						that.floors=[];
						that.floorNum=[];
						res.data.data.forEach(function(value, index, array){
							var hu={houses:[]};
							value.hu.forEach(function(value2,index2,array2){
								var temp={num:value2.name,checked:value2.status,id:value2.id};
								hu.houses.push(temp);
							})
							that.floors.push(hu);
							that.floorNum.push(value.name);
						});
						that.bili=res.data.bili;
					}
				});

			},
			getdanyuanlist(){
				var id=this.$route.params.id;
				var that = this;
				axios.post(urlstr + 'user.fangyuan.getdanyuanlist&id=' + id).then(function (res) {
					if (res.data.code == 3) {
						that.title=res.data.title;
						that.danyuan=res.data.data;
						that.getcenghulist(that.danyuan[0].id)
					}
				});
			}
		},
		created(){
          	 this.$store.commit('footer',false);

          // 数据初始化加载
			this.getdanyuanlist();
      },
      mounted(){

      	// 
      	this.load();
      	
      },
      computed:{
      		//getFloors(){
      		//	for(let i=1;i<this.floors.length+1;i++){
      		//		this.floorNum.push(i)
      		//	}
      		//	return this.floorNum.reverse();
      		//}
      },
      watch:{
      	demo1(value){
      		//当单元改变时，加载新的数据
			this.getcenghulist(value)
      	}
      }
	}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';
#wrapper{
	width: 100%;
	overflow: hidden;
	position: relative;
	height: 600px;
	
}
.floor-num{
	position: absolute;
	z-index: 100;
	li{
		&:first-child{
			border-top: 2px solid @color-primary;
		};
		width: 36px;
		height: 45px;
		line-height: 45px;
		background: #fff;
		margin-bottom: 10px;
		text-align: center;
	}
}
.unsaled{
	background: #fff!important;
}
.man-house-top{
	background: #fff;
	text-align: center;
	line-height: 34px;
	padding: 5px ;
	font-size: 16px;
	padding-left: 10%;
	.col-80{
		width: 65%;
		text-align: left;
		.iconfont{
			margin-top: -2px;
			margin-right: 4px;
		}
	}
	.col-20{
		width: 26%;
	}
	div+div{
		border-left: 1px solid #e7e7e7;
	}	
}
.card{
	margin: 44px 0 4px;
	margin-bottom: 4px;
	box-shadow: none;
	text-align: center;
	.card-header{
		margin-bottom: 10px;
	}
}
.house-note{
	text-align: center;
	background: #EEEEEE;
	padding: 14px 0;
	.house-note-icon{
		display: inline-block;
		height: 10px;
		width: 10px;
		background: red;
		margin-right: 5px;
	}

	ul{
		margin: 0;
	}
}
.house-btn{
	background: #FFC926;
    color: #fff;
    text-align: center;
    line-height: 50px;
    font-size: 18px;
}
.ll{
  min-width:800px;
  height: auto;
}
.slide-right-enter-active {
   animation: fadeInRight 0.5s ease;
}
.condition-content{
	height: 380px;
	border:1px solid red;
}
</style>
