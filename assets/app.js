import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'

import './styles/app.scss';

import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

import moment from "moment";

import App from './components/App'
import Home from './components/Home'
import Profile from './components/Profile';
import Account from './components/Account'
import AccountGeneral from './components/Account/AccountGeneral';
import AccountAvatar from './components/Account/AccountAvatar';
import AccountPassword from './components/Account/AccountPassword';

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
        unreadMessagesCount: 0,
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
        incrementUnreadMessagesCount(state) {
            state.unreadMessagesCount++
        },
        setUnreadMessagesCount(state, value) {
            state.unreadMessagesCount = value
        },
        resetUnreadMessagesCount(state) {
            state.unreadMessagesCount = 0
        },
        incrementUnreadNotificationsCount(state) {
            state.unreadNotificationsCount++
        },
        setUnreadNotificationsCount(state, value) {
            state.unreadNotificationsCount = value
        },
        resetUnreadNotificationsCount(state) {
            state.unreadNotificationsCount = 0
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
