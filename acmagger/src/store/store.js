import Vue from 'vue';
import Vuex from 'vuex';
import data from './modules/data';
import customer from './modules/customer';
import common from './modules/common.js';

Vue.use(Vuex);


const store = new Vuex.Store({
    modules: {
       data,
       common,
       customer
    },
    strict: process.env.NODE_ENV !== 'production', //是否开启严格模式
});


export default store
