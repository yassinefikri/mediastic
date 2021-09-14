import './styles/app.scss';

require('bootstrap');

import Vue from 'vue'
import App from './components/App'
import VueRouter from 'vue-router'
import Vuex from 'vuex'

import Home from './components/Home'
import Account from './components/Account'
import AccountGeneral from './components/Account/AccountGeneral';
import AccountAvatar from './components/Account/AccountAvatar';
import AccountPassword from './components/Account/AccountPassword';

Vue.use(Vuex)
Vue.use(VueRouter)

const store = new Vuex.Store({
    state: {
        userInfos: {},
        alerts: [],
    },
    mutations: {
        setUserInfos(state, userInfos) {
            state.userInfos = userInfos
        },
        addAlert(state, alert) {
            state.alerts.push(alert)
        },
        setAlerts(state, alert) {
            state.alerts = [alert]
        },
        deleteAlert(state, index) {
            state.alerts.splice(index, 1)
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
]
const router = new VueRouter({
    routes
})

Vue.config.productionTip = false

new Vue({
    render: h => h(App),
    store,
    router
}).$mount('#app')
