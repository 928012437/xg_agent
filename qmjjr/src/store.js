import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const state={
	isFooter:true,
	isLoading:true

}
const getters={
			footer:state => state.isFooter,
	loading:state => state.isLoading,
}
const mutations = {
	footer(state,res){
		state.isFooter=res
	},
	loading(state,res){
		state.isLoading=res
	},
}

const actions = {
	footer({commit}){
		commit(footer,res)
	},
	loading({commit}){
		commit(loading,res)
	}
}

export const store = new Vuex.Store({
	state,
	getters,
	mutations,
	actions
})

