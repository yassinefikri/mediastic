import Vue from 'vue'

import store from './store/store'
import router from './router/router'
import './filters/filters'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import './styles/app.scss'
import 'bootstrap';

import BootstrapVue from 'bootstrap-vue'
import App from './components/App'

Vue.use(BootstrapVue)
Vue.config.productionTip = false

new Vue({
    render: h => h(App),
    store,
    router
}).$mount('#app')
