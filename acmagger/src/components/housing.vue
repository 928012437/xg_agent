<template>
<div >
	<table class="housing">
			<tr v-for='floor in floors' class="h-floors">
				<td v-for='house in floor.houses' class="h-house" :class="{houseOn:house.checked,handle:!check}">
					{{house.num}}
					<!-- check决定能不能操作 -->
					<input type="checkbox" :disabled='check' v-model='house.checked' :value='house.num' />
					<i class="icon icon-form-checkbox" v-show='!check'></i>
				</td>
			</tr>
  </table>
		
<p>{{checkedGroups}}</p>

		
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
	props:[
		'check',
		'floors'
	],
		computed:{
			checkedGroups: {
              get: function() {
                  var checkedGroups = [];
                  this.floors.forEach(function(floor){
	                  	floor.houses.forEach(function(item) {
	                      if (item.checked) {
	                          checkedGroups.push(item.num);
	                      }
                  	});
                  })
                  
                  return checkedGroups;
              }
          }
		}
}
</script>
<style lang='less'>
.housing{
	width: 800px;
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
			position: relative;
			overflow: hidden;
			width: 65px;
			height: 45px;
			input{
				display: block;
				position: absolute;
				width: 100%;
				height: 100%;
				top:0;
				opacity: 0;
			}
			input:checked+i{
				    background-color: #0894ec;
			}

			.icon-form-checkbox{
				   height: 0.7rem;
				   position: absolute;
				   border-radius: 0.7rem;
				   border: 1px solid #c7c7cc;
				   box-sizing: border-box;
				   bottom: 4px;
				   right: 4px;
			}

			.icon{
				 font-family: "iconfont-sm" !important;
				    font-style: normal;
				    display: inline-block;
				    vertical-align: middle;
				    background-size: 100% auto;
				    background-position: center;
				    -webkit-font-smoothing: antialiased;
				    -webkit-text-stroke-width: 0.2px;
				    width: 0.7rem;
			}

			.icon:after{
				content: ' ';
				position: absolute;
				left: 50%;
				margin-left: -0.3rem;
				top: 50%;
				margin-top: -0.2rem;
				width: 0.6rem;
				height: 0.45rem	; 
			}
		}
		.houseOn{
			background: orange;
			color:#fff;
		}
		.handle{
			background: #fff;
			color:#3d4145;
		}
		.saled{
			background: red;
			color:#fff;
		}
	}
</style>