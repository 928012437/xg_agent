<template>
 <div class="content">
 <Top></Top>
 <div class="content margin-top margin-bottom">	
 <ul>
 	<li v-for='(card,index) in cards'>
 		<div class="card">
		    <div class="card-content">
		      <div class="card-content-inner" :style="'background:'+card.color">
		      	<div>{{card.name}}</div>
		      	<div>{{card.num}}</div>
		      </div>
	    </div>
	    <div class="card-footer">
	    	<label :for='card.id' class="card-btn">
	    		<input type="radio" :value='card.id' name='card' :id='card.id' :checked='card.checked' />
	    		<p class="button" >
	    			默认银行卡
	    		</p>
	    	</label>
	    	<div >
	    		<span @click='resetForm(card.id)'>修改</span>
	    		<span @click='delCard(index)'>删除</span>
	    		
	    	</div>
    	</div>
  		</div>
 	</li>
 </ul>
 <button class="button add-btn  button-big" @click='resetForm'>添加银行卡</button>
 <popup v-model='show' height='400px' @on-show='resetForm'>
 	<card-info :card='card' ></card-info>
 	<button class="bar bar-tab bottom-btn" @click='addCard'> 
 		保存
 	</button>
 </popup>
</div>


</div>
</template>
<script type='text/ecmascript-6'>
import Top from '../components/header.vue'
import CardInfo from '../components/cardInfo.vue'
import Popup from '../components/popup/index.vue'
export default{
	components:{
		Popup,
		Top,
		CardInfo
	},
	created(){
		 this.$store.commit('footer',false);
	},
	data(){
		return{
			show:false,
			card:{
				name:"",
				num:'',
				checked:false,
				color:'',
				id:Math.ceil(Math.random()*50)
			},
			cards:[
				{
					name:'农业银行',
					num:258464548457,
					checked:true,
					color:'linear-gradient(to right, #e65a65, #e74f7c)',
					id:2
				},
				{
					name:'建设银行',
					num:258464548457,
					checked:false,
					color:'linear-gradient(to right, rgb(78, 129, 189), rgb(58, 92, 167))',
					id:4
				},
				{
					
				}

			],
			cardId:[]
		}
	},
	methods:{
		addCard(){
			this.cards.push(this.card);
			alert(this.card.name+" | "+this.card.num+" | "+this.card.checked+" | "+this.card.color)
			this.card={
				name:"",
				num:'',
				checked:false,
				color:'',
				id:Math.ceil(Math.random()*50)
			},			
			this.show=!this.show
		},
		delCard(index){
			this.cards.splice(index, 1)
		},
		resetForm(id){
			this.show=!this.show
			// alert(typeof id)
			if(typeof id!=='number'){
				
					alert('sdf')
			
				
				document.getElementById('card-form').reset();
			}else{

			}

			// alert(id)
			/*if(arguments.length<=1){
				
			}*/
			
		}
	}
}

</script>

<style lang='less' scoped>
@import '../style/less/mixins.less';
@import '../style/less/variables.less';

.card-content-inner{
	color: #fff;
}
.card-btn{
	display: block;
	input[type="radio"]{
		display: none;
		position: absolute;
		z-index: 99
	}
	input[type="radio"]:checked + p.button{
		background:@color-primary;
		color:#fff;
	}
}
.add-btn{
	background: @color-primary;
	color: #fff;
	width: 70%;
	margin: 0 auto;
}


</style>
