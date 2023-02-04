import Vue from 'vue'
import VueI18n from 'vue-i18n';
import VueRouter from 'vue-router'
import App from './collab/app.vue'

import Editor from './collab/editor.vue'
import List from './collab/index.vue'

Vue.use(VueI18n);


Vue.config.productionTip = false

Vue.prototype.svg = require('./collab/svg');

Vue.use(VueRouter)

const messages = {
    'en': {
        myNotes: 'My notes',
        sharedWithMe: 'Notes shared with me',
        renameNote: 'Modify',
        deleteNote: 'Delete',
        save: 'Save',
        savePdf: 'Save as PDF',
        fullScreen: 'Full screen'
    },
    'fr': {
        myNotes: 'Mes notes',
        sharedWithMe: 'Notes partagées avec moi',
        renameNote: 'Modifier',
        deleteNote: 'Supprimer',
        save: 'Enregistrer',
        savePdf: 'Enregistrer en PDF',
        fullScreen: 'Plein écran'
    }
};

const i18n = new VueI18n({
    locale: document.head.querySelector("[name~=user-lang][content]").content, // set locale
    fallbackLocale: 'en', // set fallback locale
    messages, // set locale messages
});

const routes = [
  {
    path: '/collab/',
    name: 'list',
    component: List,
    props: true
  },
  {
    path: '/collab/:id',
    name: 'editor',
    component: Editor,
    props: true
  },
]

const router = new VueRouter({
  routes,
  mode: 'history',
  linkActiveClass: 'is-active',
  linkExactActiveClass: 'is-exact-active',
  duplicateNavigationPolicy: 'reload' 
})

new Vue({
  router,
  template: '<router-view></router-view>',
  $,
  i18n,
  render: h => h(App)
}).$mount('#collab')