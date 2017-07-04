/**
 * Created by linhaifeng on 2016/11/15.
 */
import * as types from '../mutation-types.js';

const state={
	//公共客户
	shareCustomer:{
		//是否全选了
		isCheck:0,
		//选中的客户数组
		checkedGroup:[],
		//加载的属于数组
		loadedGroup:[]
	},

	

	//逾期客户
	overdueCustomer:{
			//销售人员
			salers:[],
			//选中的客户
			checkedGroup:[]
			
	}
}

const mutations={
	[types.CUS_CHECK_SHARE](state,cus){
		// state.shareCustomer=cus;
	 	// state = Object.assign(state,cus);
	 	state.shareCustomer.isCheck=cus.isCheck;
	 	state.shareCustomer.checkedGroup=cus.checkedGroup;
	 	state.shareCustomer.loadedGroup=cus.loadedGroup

	},
	
	[types.CUS_CHECK_OVERDUE](state,arg){
		state.overdueCustomer.salers=arg
	}
	
}

const actions={
	checkShare({commit}){
		commit(types.CUS_CHECK_SHARE,cus)
	},
	test({commit}){
		commit('test',str)
	}
}

//属性的计算
const getters={
	shareCustomer: state =>state.shareCustomer,
	overdueCustomer: state =>state.overdueCustomer
}


export default{
	state,
	mutations,
	getters,
	actions
}