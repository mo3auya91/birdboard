require('./bootstrap');

import Vue from 'vue';

import {InertiaApp} from '@inertiajs/inertia-vue';
import {InertiaForm} from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueI18n from 'vue-i18n'
import {Notyf} from 'notyf';
import 'notyf/notyf.min.css'; // for React, Vue and Svelte

// Create an instance of Notyf
const notyf = new Notyf({
    duration: 5000,//5 sec
    dismissible: true,
})

Vue.mixin({
    methods: {
        route,
        successToast: function (message) {
            notyf.success(message)
        },
        errorToast: function (title, errors) {
            let html = `<h1><b>${title}</b></h1>`
            if (errors) {
                html += `<ul>`
                Object.values(errors).forEach(error => {
                    html += `<li><sm>${error[0]}</sm></li>`
                })
                html += `</ul>`
            }
            notyf.error(html)
        },
    }
});
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueI18n);
let default_locale = document.getElementsByTagName('html')[0].getAttribute('lang')
const i18n = new VueI18n({
    locale: default_locale, // set default locale
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
