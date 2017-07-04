<template>
<div >
	<table class="housing">
			<tr v-for='floor in floors' class="h-floors">
				<td v-for='house in floor.houses' class="h-house" :class="[house.checked==0?'unsaled':'saled']" :data-id="house.id" >{{house.num}} </td>
			</tr>
   </table>
	



 
		
</div>
</template>

<script>
import  IScroll from 'iscroll'

export default{
	data(){
		return{	
			checkHouse:[],
		}
	},
	mounted(){

	},
	props:[
		'check',
		'floors'
	],
	
}
</script>
<style lang='less'>
.housing{
	width: 1900px;
	margin-left: 50px;
	margin-top: 10px;
}

	.h-floors{
		.h-house{
			display: inline-block;
			background: #fff;
			margin: 10px 10px 0 0;
			text-align: center;
			line-height: 45px;
			
			width: 65px;
			height: 45px;
			a{	display: inline-block;
				background: red;
				color: #fff;
			}
		}
		
		.unsaled{
			background: #fff;
			color:#3d4145;
		}
		.saled{
			background: red;
			color:#fff;
		}
	}
</style>