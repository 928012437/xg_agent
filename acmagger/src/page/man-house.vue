<template>

    <div>
   
<div class="content">
<div class="row man-house-top bar bar-nav" >
	<div class="col-50">sdf</div>
	<div class="col-20">筛选</div>
	<div class="col-20">
	<router-link to='/house/handle'>楼盘</router-link>	
	</div>
</div>
	<!-- <transition name='slide-right'>
		<router-view></router-view>
	</transition> -->
	<div class="card">
	 <tab>
      <tab-item  v-on:click.native="demo1 = 'A单元'">A单元</tab-item>
      <tab-item  v-on:click.native="demo1 = 'B单元'">B单元</tab-item>
      <tab-item  v-on:click.native="demo1 = 'C单元'">C单元</tab-item>
    </tab> 
	    <div class="card-content">
	        <div class="card-content-inner house-note">
	      		<ul class="row">
			      <li class="col-50">已售&nbsp;&nbsp;16套&nbsp;&nbsp;占50%</li>
			      <li class="col-50">已售&nbsp;&nbsp;16套&nbsp;&nbsp;占50%</li>
			      <li class="col-50">已售&nbsp;&nbsp;16套&nbsp;&nbsp;占50%</li>
			      <li class="col-50">已售&nbsp;&nbsp;16套&nbsp;&nbsp;占50%</li>
			    </ul>
	        </div>
	    </div>
   
  </div>
<ul class="floor-num">
   	<li v-for='num in getFloors' :key='index'>{{num}}</li>
 </ul>		
	
    <!-- <div>{{demo1}}</div> -->

	<scroll>
		<div class="ll">
			<house :check='check' :floors='floors'></house>
		</div>
	</scroll>
</div>
<nav class="bar bar-tab house-btn" @click='check=!check'>
  预销控
</nav>
		
</div> 
</template>

<script type='text/ecmascript-6'>
import House from '../components/housing.vue';
import Scroll from '../components/scroll/scroll.vue';
import tab from '../components/tab/tab.vue';
import tabItem from '../components/tab/tab-item.vue';
	export default{
		data(){
			return{
				check:true,
				demo1 : '已发货',
				floorNum:[],
				selected:{check:true},
				floors:[
				{
					houses:[
						{num:501,checked:true},
						{num:503,checked:true},
						{num:504,checked:false,saled:true},
						{num:503,checked:true},
						{num:504,checked:false},
						{num:503,checked:true},
						{num:504,checked:false}
					]
				},
				{
					houses:[
						{num:201,checked:false},
						{num:301,checked:false},
						{num:5401,checked:false},
						{num:201,checked:false},
						{num:301,checked:false},
						{num:201,checked:false},
						{num:301,checked:false},
						{num:201,checked:false},
						{num:301,checked:false},
					]
				}
			],
			}
		},
		components:{
			House,
			Scroll,
			tab,
			tabItem
		},
		methods:{
			show(){
				alert('sdfsfs')
			}
		},
		created(){
          this.$store.commit('COMM_CONF',{
                isFooter:false, 
            });

          // 数据初始化加载
      },
      computed:{
      		getFloors(){
      			for(let i=1;i<this.floors.length+1;i++){
      				this.floorNum.push(i)
      			}
      			return this.floorNum.reverse();
      		}
      },
      watch:{
      	demo1(value){
      		//当单元改变时，加载新的数据
      		 alert(value)
      	}
      }
	}

</script>

<style lang='less' scoped>
.floor-num{
	position: absolute;
	z-index: 100;
	li{
		width: 36px;
		height: 50px;
		line-height: 50px;
		background: #fff;
		margin-bottom: 10px;
		text-align: center;
	}
}

.man-house-top{
	/*background: #fff;*/
	text-align: center;
	line-height: 34px;
	padding: 5px ;
	padding-left: 10%;

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
	background: #fff;
	padding: 10px 0;
	ul{
		margin: 0;
		padding: 0;

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
  min-width:600px;
  height: auto;
}
.slide-right-enter-active {
   animation: fadeInRight 0.5s ease;
}
</style>
