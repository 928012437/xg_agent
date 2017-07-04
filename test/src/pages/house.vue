<template>
   <div>
   		<div id="wrapper">
		<table class="house">
			<tr v-for='index in floors'><td v-for='house in index' class="house-item" :class="{sale:house.sale}" @tap="show(house.id)">{{house.num}}</td></tr>
		</table>	
		</div>	
   </div>
</template>
<script type='text/ecmascript-6'>
import $ from 'n-zepto'
var IScroll = require('../assets/iscroll-zoom.js');

export default{
    props:[],
    data(){
        return{
        	floors:[
        		[
        			{
        				id:1,
        				num:101,
        				sale:true	
        			},
        			{
        				id:2,
        				num:102,
        				sale:false	
        			},
        			{
        				id:3,
        				num:103,
        				sale:true	
        			},
        			{
        				id:4,
        				num:104,
        				sale:false	
        			},
        		],
        		[
        			{
        				id:6,
        				num:201,
        				sale:true	
        			},
        			{
        				id:7,
        				num:202,
        				sale:true	
        			},
        			{
        				id:8,
        				num:203,
        				sale:true	
        			},
        			{
        				id:9,
        				num:204,
        				sale:true	
        			},
        		],
        		[
        			{
        				id:6,
        				num:201,
        				sale:true	
        			},
        			{
        				id:7,
        				num:202,
        				sale:true	
        			},
        			{
        				id:8,
        				num:203,
        				sale:true	
        			},
        			{
        				id:9,
        				num:204,
        				sale:true	
        			},
        		],
        		[
        			{
        				id:6,
        				num:201,
        				sale:true	
        			},
        			{
        				id:7,
        				num:202,
        				sale:true	
        			},
        			{
        				id:8,
        				num:203,
        				sale:true	
        			},
        			{
        				id:9,
        				num:204,
        				sale:true	
        			},
        		],
        		[
        			{
        				id:6,
        				num:201,
        				sale:true	
        			},
        			{
        				id:7,
        				num:202,
        				sale:true	
        			},
        			{
        				id:8,
        				num:203,
        				sale:true	
        			},
        			{
        				id:9,
        				num:204,
        				sale:true	
        			},
        		],
        	],
        	myScroll:null
        }
    },
	created(){
		
	},
	mounted(){
			this.loaded()
			// $('#wrapper').click(function(){
			// 	alert('sfdsfsd')
			// })
			// $(document).on('tap','td',function(){
						
			// 				alert('sdfsdfs')
			// })
	},
	methods:{


		loaded(){
			var myScroll = new IScroll('#wrapper', {
				zoom: true,
				scrollX: true,
				scrollY: true,
				mouseWheel: true,
				wheelAction: 'zoom',
				tap:true
			});
			
			// $('table').on('tap',function(){
			// 			alert('啦啦啦啦啦啦啦啦')
							
			// })

			// alert(Object.keys(myScroll))
			// $(document).on('click','table',function(){
					
			// 			alert('sdfsdf')
					
							
			// })
			
		},
		show(id){
			// this.$router.push({ path: '/yesterdaty'})
			// this.$router.push({ path: '/yesterdaty',query:{id:id}})
			this.$router.push({name:'yesterdaty',params:{id:id}})
		}
	
	},
	computeds:{
	}

}


</script>
<style lang='less' scoped>
.house{
	border:1px solid red;
	width: 100%;
	height: auto;
	text-align: center;
	background: #eee;
	.house-item{
		display:inline-block;
		width: 30px;
		height: 30px;
		font-size: 10px;
		background: #fff;
		cellspacing:5px;
	}
	.sale{
		background: red;
	}
}

</style>