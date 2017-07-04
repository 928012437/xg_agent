import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const state={
	isFooter:true
}
const getters={
	isFooter:state => state.isFooter
}
const mutations = {
    footer(state,res){
        state.info = res
    }
}
const store = new Vuex.Store({
	state,
	getters,
	mutations
})