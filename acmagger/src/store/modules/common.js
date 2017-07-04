/**
 * Created by linhaifeng on 2016/11/18.
 */
import * as types from '../mutation-types.js';
/*
* 通用的配置，例如顶部的配置跟地址菜单
*
*
* */

    //状态对象
const state = {
      /*  isSearch:true,  //是否显示搜索
        isBack:false,   //是否显示返回
        isShare:false,  //是否显示分享
        title:'',  //显示标题内容*/
       
        isFooter:true,  //是否显示底部
       /* loading:false,  //是否显示loading,
        mark:false  //是否显示遮罩,*/
};

const actions  = {
    commConf({commit},settings){
        commit(types.COMM_CONF,settings);
    },
    /*markStatus({commit},status){
        commit(types.COMM_MARK_STATUS,status);
    }*/
};


const getters = {
    commConf : state => state, //返回state状态对象
   /* loading : state => state.loading,//loading
    markStatus : state => state.mark,//遮罩*/
};

const mutations = {
    [types.COMM_CONF](state,settings){
        state = Object.assign(state,settings);

    },
  /*  [types.COMM_LOADING_STATUS](state,status){

        state.loading = status
    },
   
    [types.COMM_MARK_STATUS](state,status){
        // "use strict";
        state.mark = status
    }*/
};


export default {
    state,
    actions,
    getters,
    mutations
}