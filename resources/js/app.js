import './bootstrap.js';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/Admin/Layout.vue';
import {createPinia} from 'pinia';
import Vue3Toastify, {toast} from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import {ZiggyVue} from 'ziggy-js';

createInertiaApp({
    progress: {
        delay: 250,
        color: '#00BBA7',
        includeCSS: true,
        showSpinner: false,
    },
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: true});
        const mod = pages[`./Pages/${name}.vue`];
        const comp = mod.default;
        if (name.startsWith('Admin/') && !name.startsWith('Admin/Auth/')) {
            comp.layout = AdminLayout;
        }

        return comp;
    },
    setup({el, App, props, plugin}) {
        const pinia = createPinia();

        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(Vue3Toastify, {
                autoClose: 3000,
                position: toast.POSITION.BOTTOM_RIGHT,
                style: {
                    fontSize: '0.75rem',
                    minWidth: '320px',
                    width: 'auto',
                },
            })
            .mount(el);
    },
});
