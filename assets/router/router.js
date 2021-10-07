import Vue from 'vue'
import VueRouter from 'vue-router'

import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js'
const FOSroutes = require('../../public/js/fos_js_routes.json')
Routing.setRoutingData(FOSroutes)
Vue.prototype.$Routing = Routing

import Home from '../components/Home'
import Profile from '../components/Profile'
import Account from '../components/Account'
import AccountGeneral from '../components/Account/AccountGeneral'
import AccountAvatar from '../components/Account/AccountAvatar'
import AccountPassword from '../components/Account/AccountPassword'
import Chat from '../components/Chat'
import PostEdit from '../components/Post/PostEdit'
import PostView from '../components/Post/PostView'
import FriendshipList from '../components/Friendship/FriendshipList'

Vue.use(VueRouter)

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
    },
    {
        name: 'post_view',
        path: '/post/:postId',
        component: PostView,
        props: true,
    },
    {
        name: 'post_edit',
        path: '/post/:postId/edit',
        component: PostEdit,
        props: true,
    },
    {
        name: 'friendships',
        path: '/friendships/',
        component: FriendshipList,
    }
]

export default new VueRouter({
    routes
})
