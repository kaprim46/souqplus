import './bootstrap';
import "preline/preline";
import 'sweetalert2/dist/sweetalert2.min.css';

import {createApp} from 'vue';
import {createPinia} from 'pinia';
import {i18nVue} from "laravel-vue-i18n";
import VueGoogleMaps from '@fawmi/vue-google-maps';
import VueSweetalert2 from 'vue-sweetalert2';
import Notifications from 'notiwind';
import router from '@/routes';
import VueApexCharts from "vue3-apexcharts";
import mitt from 'mitt';
import {createMetaManager} from 'vue-meta'

/**
 * App Layout
 */
import App from '@/App.vue';

/**
 * Axios
 */
import '@/axios';
import {statusEnum} from "@/types/Advertisements";


/**
 * Pinia Store and app & emitter
 */
const pinia         = createPinia()
const app   = createApp(App);
const emitter = mitt();

/**
 * Share vars
 */
app.config.globalProperties.$emitter        = emitter;
app.config.globalProperties.$assetUrl       = import.meta.env.VITE_APP_URL;
app.config.globalProperties.$defaultLang    = localStorage.getItem('lang') ?? import.meta.env.VITE_APP_LANG;
app.config.globalProperties.$statusHexColor = (status: statusEnum): string => {
    switch (status) {
        case statusEnum.PENDING:
            return 'text-yellow-500';
        case statusEnum.APPROVED:
            return 'text-green-500';
        case statusEnum.REJECTED:
            return 'text-red-500';
        default:
            return 'text-gray-500';
    }
}
app.config.globalProperties.$formatCurrency = (value: number, currency: string = 'USD') => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(value);
}

/**
 * Share this var to all components
 */
app.provide('$assetUrl', import.meta.env.VITE_APP_URL);
app.provide('$defaultLang', localStorage.getItem('lang') ?? 'en');
app.provide('$emitter', emitter);

/**
 * Register all components and add prefix 'v-' to component name and build style tailwind after register
 */
const components: Record<string, any> = import.meta.globEager('./views/components/**/*.vue');

for (const path in components) {
    const name = path.split('/').pop()?.split('.')[0];

    if (name) {
        app.component(`v-${name}`, (components[path] as any).default || components[path]);
    }
}

/**
 * Use i18n
 * */
app.use(i18nVue, {
    resolve: async (lang: string) => {
        const langs = import.meta.glob('../../lang/*.json');
        return await langs[`../../lang/${lang}.json`]();
    },
    lang: localStorage.getItem('lang') ?? import.meta.env.VITE_APP_LANG,
});



/**
 * Use Plugin
 */
app.use(createMetaManager());
app.use(VueApexCharts);
app.use(pinia);
app.use(router);
app.use(Notifications);
app.use(VueSweetalert2);
app.use(VueGoogleMaps, {
    load: {
        key: import.meta.env.VITE_APP_GOOGLE_MAPS_API_KEY,
    }
})

app.mount('#app');
