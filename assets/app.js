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
            let obj = {...state.friendships}
            friendships.forEach(function(friendship){
                Object.assign(obj, {[friendship.id]: friendship})
            })
            state.friendships = obj
        },
        removeFriendship(state, friendship) {
            let obj = {...state.friendships}
            delete obj[friendship.id]
            state.friendships = obj
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

Vue.config.productionTip = false

new Vue({
    render: h => h(App),
    store,
    router
}).$mount('#app')
