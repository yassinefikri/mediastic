import Vue from 'vue'
import Vuex from 'vuex'
import mutations from './mutations'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        userInfos: {},
        alert: null,
        friendships: {},
        friends: {},
        messages: {},
        conversations: [],
        unreadConversation: {},
        unreadNotificationsCount: 0,
        lastSeenMessage: {}
    },
    mutations
})
