import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const state={
	isFooter:true,
	l:'啦啦啦啦'
}
const getters={
	footer:state => state.isFooter,
	test(state){
		return state.l
	}
}
const mutations = {
    footer(state,res){
        state.isFooter=res
    }
}

const actions = {
    footer({commit}){
    	commit(footer,res)
    }
}

export const store = new Vuex.Store({
	state,
	getters,
	mutations,
	actions
})

