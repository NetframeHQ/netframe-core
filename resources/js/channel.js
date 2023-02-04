import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './channel/app.vue'
import VueEcho from 'vue-echo-laravel'
import moment from 'moment'
window.io = require('socket.io-client')
import PerfectScrollbar from 'vue2-perfect-scrollbar'
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'
import VueSanitize from "vue-sanitize"

Vue.use(PerfectScrollbar)
Vue.use(VueSanitize)

Vue.use(VueRouter)
Vue.use(VueEcho, {
    broadcaster: 'socket.io',
    host: document.head.querySelector("[name~=broadcast-domain][content]").content,
    authEndpoint: "/broadcasting/auth",
  	headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})

let defaultOptions = {
    allowedTags: ['b', 'br', 'a', 'i', 'p'],
    allowedAttributes: {
        'a': [ 'href' ]
    },
    allowedSchemes: ['http', 'https'],
    allowedSchemesByTag: {},
    allowedSchemesAppliedToAttributes: ['href', 'src'],
    allowedProtocolRelative: true
};
Vue.use(VueSanitize, defaultOptions);

Vue.filter('formatDate', function(value) {
  if (value) {
    return moment(String(value)).format('DD/MM/YYYY');
  }
})

Vue.filter('formatTime', function(value) {
  if (value) {
    return moment(String(value)).format('HH:mm');
  }
})

const routes = [
  {
    path: '/channels/:id',
    name: 'app',
    component: App,
    props: true
  },
]

const router = new VueRouter({
  routes,
  mode: 'history',
  linkActiveClass: 'is-active',
  linkExactActiveClass: 'is-exact-active',
  duplicateNavigationPolicy: 'reload',
  inPost: false
})

new Vue({
  router,
  template: '<router-view></router-view>',
  firstElement: 1,
  currentOffset: 1,
}).$mount('#app')
