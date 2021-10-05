import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'
import getters from './getters'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        userInfos: {},
        alert: null,
        friendships: {},
        friends: {},
        messages: {},
        conversations: [],
        unreadConversations: {},
        unreadNotificationsCount: 0,
        lastSeenMessage: {}
    },
    mutations,
    getters,
})
