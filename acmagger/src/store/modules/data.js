/**
 * Created by linhaifeng on 2016/11/15.
 */
import api from '../../api/api.js';
import * as types from '../mutation-types.js';

//状态对象
const state = {
    data:{
        day:{
        	data:[],
        	date:[]
        },
	    mounth:{
	        	data:[],
	        	date:[]
	    }
        
    }


};


const getters = {
	//日数据
    dataDayData: state => state.data.day,
    //月数据
    dataMounthData:state => state.data.mounth,
    
};

const actions = {
     // 获取日列表 
    dataGetDay ({ commit }) {
    	// loading
        // commit(types.COMM_LOADING_STATUS,true);
        api.dataGetDay(function (res) {
            commit(types.DATA_GET_DAY_SUCCESS, { res })
        })
    },
    // 获取月列表 
     dataGetMounth({ commit }) {
    	// loading
        // commit(types.COMM_LOADING_STATUS,true);
        api.dataGetMounth(function (res) {
            commit(types.DATA_GET_MOUNTH_SUCCESS, { res })
        })
    },
}

const mutations = {
    [types.DATA_GET_DAY_SUCCESS] (state, { res }) {
        state.data.day = res;
    },
    [types.DATA_GET_MOUNTH_SUCCESS] (state, { res }) {
        state.data.mounth = res;
    }

};


export default{
    state,
    getters,
    actions,
    mutations
}