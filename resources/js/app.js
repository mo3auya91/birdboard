require('./bootstrap');

import Vue from 'vue';

import {InertiaApp} from '@inertiajs/inertia-vue';
import {InertiaForm} from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueI18n from 'vue-i18n'

Vue.mixin({methods: {route}});
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueI18n);
const i18n = new VueI18n({
    locale: document.getElementsByTagName('html')[0].getAttribute('lang'), // set default locale
    fallbackLocale: "en",
    messages: {
        'en': require('../lang/en/en.json'),
        'ar': require('../lang/ar/ar.json')
    }
})

const app = document.getElementById('app');

new Vue({
    i18n,
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
    // created() {
    //     console.log(document.getElementsByTagName('html')[0].getAttribute('lang'))
    // }
}).$mount(app);
