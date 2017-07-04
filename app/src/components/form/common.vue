<template>
<div >
 <div class="list-block">
    <ul>
      <!-- 姓名 -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-name required">*</i></div>
          <div class="item-inner">
            <div class="item-title label" >姓名 
             <span class="error required" v-text='fromErrors.name'></span> 
            </div>
            <div class="item-input">
              <input type="text" placeholder="请输入" name='name' :value="info.name" >
            </div>
          </div>

        </div>
      </li>
      <!-- 性别 -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon icon-form-email required">*</i></div>
          <div class="item-inner">
            <div class="item-title label">性别</div>
            <div class="item-input">
            	<label for="man" class="form-sex" >
            		<input type="radio"   name="sex" value='1'  id='man' :checked="info.sex==1" >
            		<i class="icon icon-form-checkbox" ></i>
            		&nbsp;男
            	</label>
              	&nbsp;&nbsp;&nbsp;
              	<label for="woman" class="form-sex">
            		<input type="radio" name="sex"  value='0' id='woman' :checked="info.sex==0" >
            		<i class="icon icon-form-checkbox"></i>
            		&nbsp;女
            	</label>
            </div>
          </div>
        </div>
      </li>
      <!-- 手机号 -->
      <li>
        <div class="item-content">
          <div class="item-media"><i class="icon required">*</i></div>
          <div class="item-inner">
            <div class="item-title label">手机
             <span class="error required" v-text='fromErrors.tel'></span> 
            </div>
            <div class="item-input">
              <input type="text" placeholder="请输入" name='tel' :value="info.tel" >

            </div>
          </div>
        </div>
      </li>

        <li v-for='(option,index) in data' v-if="info==''" >
            <div class="item-content">
                <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                <div class="item-inner">
                    <div class="item-title label">{{option.name}}</div>
                    <div class="item-input">
                        <input type="text" placeholder="请选择" @click='check(option.name)' :value='option.value' :name='option.namef'>
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="item-content">
                <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                <div class="item-inner">
                    <div class="item-title label" >标签
                        <span class="error required" v-text='fromErrors.name'></span>
                    </div>
                    <div class="item-input">
                        <input type="text" placeholder="请输入" name='label' :value="info.label" >
                    </div>
                </div>

            </div>
        </li>

        <li>
            <div class="item-content">
                <div class="item-media"><i class="icon icon-form-name required">*</i></div>
                <div class="item-inner">
                    <div class="item-title label" >备注
                        <span class="error required" v-text='fromErrors.name'></span>
                    </div>
                    <div class="item-input">
                        <input type="text" placeholder="请输入" name='mark' :value="info.mark" >
                    </div>
                </div>

            </div>
        </li>

    </ul>



	<popup v-model="show">
        <header class="bar bar-nav" >
            <div @click='show=!show' class="close" >
                <button type="button" class="button">确定</button>
            </div>
            <h1 class="title">{{popupName}}</h1>

        </header>
        <picker  v-for='item in data' :data='item.values' v-model='item.value' v-if='popupName===item.name'></picker>
    </popup>
	<div style="display:none">{{errors}}</div>
</div>
</div>
</template>
<script type='text/ecmascript-6'>
import Popup from '../popup/index.vue'
import Picker from '../picker/index.vue'
import axios from 'axios';

export default{
    props:['info'],
	data(){
		return{
			company:false,
			fax:false,
			other:false,
			home:false,
			count:0,
			show:false,
            popupName:'',
      fromErrors:[],
        data:[{
            name:'选择项目',
                    namef:'lid',
                value:[],
                values:[[
        ]]
        }]

		}
	},
  mounted(){
 
  },
	components:{
		Popup,
        Picker,
	},
  vuerify:{
    name:'name',
    tel:'tel'

  },
  computed:{
    errors(){
      this.fromErrors=this.$vuerify.$errors
      return this.$vuerify.$errors
    }
  },
	created(){
		 this.$store.commit('footer',false);
        this.getloupan()
	},
	methods:{
		addTel(){
				this.count++;
				switch(this.count){
					case 1:
						this.home=true;
					break;
					case 2:
						this.company=true;
					break;
					case 3:
						this.fax=true;
					break;
					case 4:
						this.other=true;
					break;
					
				}
		},
        check(param,option){
            this.show=!this.show
            this.popupName=param
        },
        getloupan(){
            var that = this;
            axios.post(urlstr + 'user.counselors.getloupan').then(function (res) {
                if (res.data.code == 3) {
                    res.data.data.forEach(function(v){
                        var temp={ name: v.title,value:v.id+':'+v.title };
                        that.data[0].values[0].push(temp);
                    });
                }
            });
        }
	}
}
</script>

<style lang='less' scoped>
@import '../../style/less/mixins.less';
@import '../../style/less/variables.less';
.error{
  display: block;
  margin:  0;
  font-size: 12px;

}
.list-block {
	margin: 0px auto 8px;
	.item-content{
		font-size: 14px;
		input{
			font-size: 14px;
		}
	}
}
.form-wrap{
	min-height: 600px;
	margin-top: 8px
}
.item-after{
	text-align: center;
	font-size: 12px
}

.form-sex input[type="radio"]{
	display: none;
}
.form-sex .icon{
    font-family: "iconfont-sm" !important;
    font-style: normal;
    display: inline-block;
    vertical-align: middle;
    background-size: 100% auto;
    background-position: center;
    -webkit-font-smoothing: antialiased;
    -webkit-text-stroke-width: 0.2px;
    -moz-osx-font-smoothing: grayscale;
}
.form-sex i.icon-form-checkbox{
	width: 1.1rem;
    height: 1.1rem;
    position: relative;
    border-radius: 1.1rem;
    border: 1px solid #c7c7cc;
    box-sizing: border-box
}
.form-sex i.icon-default{
	width: 0.8rem;
    height: 0.8rem;
}
.form-sex i.icon-form-checkbox:after{ content: ' ';
    position: absolute;
    left: 50%;
    margin-left: -0.3rem;
    top: 50%;
    margin-top: -0.2rem;
    width: 0.6rem;
    height: 0.45rem;}
.form-sex input[type="radio"]:checked + i.icon-form-checkbox{
	background: @color-primary;
	    border: none;
}

.form-sex input[type="radio"]:checked + i.icon-form-checkbox:after{
	    background: no-repeat center;

    background-size: 0.6rem 0.45rem;
}

.close{
    position: absolute;
    top:2px;
    right: 15px;
    z-index: 888
}
</style>