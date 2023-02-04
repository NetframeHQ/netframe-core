import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

const messages = {
    'en': {
        myNotes: 'My notes',
        sharedWithMe: 'Notes shared with me',
        renameNote: 'Modify',
        deleteNote: 'Delete'
    },
    'fr': {
        myNotes: 'Mes notes',
        sharedWithMe: 'Notes partagées avec moi',
        renameNote: 'Modifier',
        deleteNote: 'Supprimer'
    }
};

const i18n = new VueI18n({
    locale: 'en', // set locale
    fallbackLocale: 'en', // set fallback locale
    messages, // set locale messages
});