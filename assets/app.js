import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import './styles/app.scss'
import 'bootstrap';

import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

import moment from "moment"

import App from './components/App'
import Home from './components/Home'
import Profile from './components/Profile'
import Account from './components/Account'
import AccountGeneral from './components/Account/AccountGeneral'
import AccountAvatar from './components/Account/AccountAvatar'
import AccountPassword from './components/Account/AccountPassword'
import Chat from './components/Chat'

Vue.use(Vuex)
Vue.use(VueRouter)

import Routing from '../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

const FOSroutes = require('../public/js/fos_js_routes.json')
Routing.setRoutingData(FOSroutes)
Vue.prototype.$Routing = Routing

const store = new Vuex.Store({
    state: {
        userInfos: {},
        alert: null,
        friendships: {},
        friends: {},
        messages: {},
        conversations: [],
        unreadConversation: {},
        unreadNotificationsCount: 0
    },
    mutations: {
        setUserInfos(state, userInfos) {
            state.userInfos = userInfos
        },
        setAlert(state, alert) {
            state.alert = alert
        },
        deleteAlert(state) {
            state.alert = null
        },
        addFriendships(state, friendships) {
            this.commit('addToObject', {object: 'friendships', data: friendships, key: 'id'})
        },
        removeFriendship(state, friendship) {
            this.commit('removeFromObject', {object: 'friendships', data: friendship, key: 'id'})
        },
        addFriend(state, friends) {
            this.commit('addToObject', {object: 'friends', data: friends, key: 'username'})
        },
        removeFriend(state, friend) {
            this.commit('removeFromObject', {object: 'friends', data: friend, key: 'username'})
        },
        addMessage(state, messages) {
            let obj = {...state.messages}
            messages.forEach(function (message) {
                if (undefined === obj[message.conversation.id]) {
                    obj[message.conversation.id] = []
                }
                let arr = [...obj[message.conversation.id]]
                arr.push(message)
                obj[message.conversation.id] = arr
            })
            state.messages = obj
        },
        removeMessage(state, messages) {
            this.commit('removeFromObject', {object: 'messages', data: messages, key: 'id'})
        },
        addConversation(state, conversations) {
            let arr = [...state.conversations]
            arr = conversations.concat(arr)
            state.conversations = arr
        },
        removeConversation(state, specificConversation) {
            state.conversations = state.conversations.filter((conversation) => conversation.id !== specificConversation.id)
        },
        updateOrAddConversation(state, specificConversation) {
            let index = state.conversations.findIndex((conversation) => conversation.id === specificConversation.id)
            let arr = [...state.conversations]
            if (-1 === index) {
                arr.unshift(specificConversation)
            } else {
                arr[index] = specificConversation
            }
            state.conversations = arr
        },
        moveConversationToStart(state, otherConversation) {
            let index = state.conversations.findIndex((conversation) => conversation.id === otherConversation.id)
            let arr = [...state.conversations]
            let fullConversation = arr.splice(index, 1)[0]
            fullConversation.updatedAt = otherConversation.updatedAt
            arr.unshift(fullConversation)
            state.conversations = arr
        },
        addToObject(state, payload) {
            let obj = {...state[payload.object]}
            payload.data.forEach(function (item) {
                Object.assign(obj, {[item[payload.key]]: item})
            })
            state[payload.object] = obj
        },
        removeFromObject(state, payload) {
            let obj = {...state[payload.object]}
            delete obj[payload.data[payload.key]]
            state[payload.object] = obj
        },
        resetObject(state, payload) {
            state[payload.name] = {}
        },
        addUnreadConversation(state, conversation) {
            let obj = {...state.unreadConversation}
            if (undefined === obj[conversation.id]) {
                obj[conversation.id] = 1
            } else {
                obj[conversation.id]++
            }
            state.unreadConversation = obj
        },
        setUnreadConversation(state, value) {
            let obj = {...state.unreadConversation}
            value.forEach(function (conversation) {
                obj[conversation.id] = conversation.count
            })
            state.unreadConversation = obj
        },
        resetUnreadConversation(state, conversationId) {
            let obj = {...state.unreadConversation}
            delete obj[conversationId]
            state.unreadConversation = obj
        },
        resetUnreadConversations(state) {
            state.unreadConversation = {}
        },
        incrementUnreadNotificationsCount(state) {
            state.unreadNotificationsCount++
        },
        setUnreadNotificationsCount(state, value) {
            state.unreadNotificationsCount = value
        },
        resetUnreadNotificationsCount(state) {
            state.unreadNotificationsCount = 0
        },
        setMessageSeen(state, messages) {
            let obj = {...state.messages}
            messages.forEach(function (message) {
                obj[message.conversation.id].se
            })
            state.messages = obj
        }
    }
})

const routes = [
    {
        name: 'default',
        path: '/',
        component: Home,
        meta: {'label': 'App'}
    },
    {
        name: 'user_account',
        path: '/account',
        component: Account,
        meta: {'label': 'Account'},
        children: [
            {
                path: 'general',
                name: 'user_account_general',
                component: AccountGeneral,
                props: {
                    getUrl: Routing.generate('user_account_general_front'),
                    postUrl: Routing.generate('user_account_general'),
                },
                meta: {'label': 'General'},
            },
            {
                path: 'password',
                name: 'user_account_password',
                component: AccountPassword,
                props: {
                    getUrl: Routing.generate('user_account_password_front'),
                    postUrl: Routing.generate('user_account_password'),
                },
                meta: {'label': 'Password'},
            },
            {
                path: 'avatar',
                name: 'user_account_avatar',
                component: AccountAvatar,
                meta: {'label': 'Avatar'},
                props: {
                    getUrl: Routing.generate('user_account_avatar_front'),
                    postUrl: Routing.generate('user_account_avatar'),
                },
            }
        ]
    },
    {
        name: 'profile',
        path: '/profile',
        component: Profile,
        meta: {'label': 'Profile'}
    },
    {
        name: 'user_profile',
        path: '/profile/:username',
        component: Profile,
        props: true,
        meta: {'label': 'Profile'}
    },
    {
        name: 'chat',
        path: '/chat',
        component: Chat,
        meta: {'label': 'Chat'},
    },
    {
        name: 'chat_user',
        path: '/chat/:conversationId',
        component: Chat,
        props: true,
        meta: {'label': 'Chat'},
    }
]
const router = new VueRouter({
    routes
})

Vue.filter('momentAgo', function (date) {
    return moment(date).fromNow();
})
Vue.filter('arrayDifference', function (arr1, arr2) {
    return [
        ...arr1.filter(x => !arr2.includes(x)),
        ...arr2.filter(x => !arr1.includes(x))
    ];
})
Vue.filter('objectDifference', function (obj1, obj2) {
    return this.arrayDifference(Object.keys(obj1), Object.keys(obj2))
})

Vue.config.productionTip = false

new Vue({
    render: h => h(App),
    store,
    router
}).$mount('#app')
