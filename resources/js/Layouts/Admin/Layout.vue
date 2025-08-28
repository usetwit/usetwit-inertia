<script setup>
import Navbar from '@/Components/Admin/Navbar/Navbar.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import Storage from '@/Components/Storage.vue';
import Sidebar from '@/Components/Admin/Sidebar/Sidebar.vue';
import {computed, watch} from 'vue';

const page = usePage();
const user = computed(() => page.props.user);
const profileImage = page.props.profileImage;
const version = computed(() => page.props.version);
const sidebarItems = computed(() => page.props.sidebar_items);
const breadcrumbs = computed(() => page.props.breadcrumbs);
const title = computed(() => [...page.props.breadcrumbs].reverse().map(c => c.title).join(' / ') + ' - useTwit');
const heading = computed(() => page.props.heading);
const logo = page.props.logo;
const errors = computed(() => page.props.errors);

const flash = computed(() => page.props.flash ?? {});

watch(
    flash,
    (f) => {
        if (f?.success) {
            toast.success(f.success);
        }

        if (f?.error) {
            toast.error(f.error);
        }
    },
    {immediate: true},
);

watch(errors, (val) => {
    const messages = Object.values(val).flat().join('\n');
    if (messages) {
        toast.error(messages);
    }
});
</script>

<template>
    <Head :title="title"/>
    <div id="storage">
        <Storage :version="version"/>
    </div>

    <div id="navbar" class="sticky top-0 z-200">
        <Navbar :user="user" :profile-image="profileImage" :logo="logo"/>
    </div>

    <div class="bg-slate-100 dark:bg-slate-700 flex">

        <aside id="sidebar" class="flex">
            <Sidebar :items="sidebarItems"/>
        </aside>

        <div class="flex-1 pb-4 overflow-x-auto">

            <header
                class="mx-2 mt-4 lg:mx-4 lg:mt-6 font-bold text-3xl text-slate-700 dark:text-white flex align-middle">
                {{ heading }}
            </header>

            <nav v-if="breadcrumbs?.length">
                <ol class="flex flex-wrap items-center mb-4 lg:mb-6 mt-1 mx-2 lg:mt-2 lg:mx-4 text-slate-500 dark:text-slate-200 breadcrumbs text-xs">
                    <li v-for="(crumb, i) in breadcrumbs" :key="i">
                        <Link v-if="i < breadcrumbs.length - 1"
                              :href="crumb.url"
                              class="text-slate-500 hover:text-orange-500 dark:text-teal-500"
                        >
                            <i v-if="i === 0" class="pi pi-home mr-1"></i>{{ crumb.title }}
                        </Link>
                        <span v-else>{{ crumb.title }}</span>
                    </li>
                </ol>
            </nav>

            <main class="overflow-x-auto">
                <slot/>
            </main>

        </div>
    </div>

    <footer class="bg-slate-800 py-8 border-t border-slate-700">
        footer
    </footer>
</template>
