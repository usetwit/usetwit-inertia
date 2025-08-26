<script setup>
import Navbar from '@/Components/Admin/Navbar/Navbar.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import {toast} from 'vue3-toastify';
import Storage from '@/Components/Storage.vue';
import Sidebar from '@/Components/Admin/Sidebar/Sidebar.vue';
import {computed} from 'vue';

const page = usePage();
const user = computed(() => page.props.user);
const defaultProfileImage = page.props.default_profile_image;
const version = computed(() => page.props.version);
const sidebarItems = computed(() => page.props.sidebar_items);
const breadcrumbs = computed(() => page.props.breadcrumbs);
const title = computed(() => [...page.props.breadcrumbs].reverse().map(c => c.title).join(' / ') + ' - useTwit');
const heading = computed(() => page.props.heading);
const logo = page.props.logo;

if (page.props.flash?.success) {
    toast.success(page.props.flash.success);
}

if (page.props.flash?.error) {
    toast.error(page.props.flash.error);
}
</script>

<template>
    <Head :title="title"/>
    <div id="storage">
        <Storage :version="version"/>
    </div>

    <div id="navbar" class="sticky top-0 z-200">
        <Navbar :user="user" :default-profile-image="defaultProfileImage" :logo="logo"/>
    </div>

    <div class="bg-slate-100 dark:bg-slate-700 flex">

        <aside id="sidebar" class="flex">
            <Sidebar :items="sidebarItems"/>
        </aside>

        <div class="flex-1 pb-4 overflow-x-auto">

            <header
                class="mx-2 mt-4 lg:mx-4 lg:mt-6 font-bold text-2xl text-slate-700 dark:text-white flex align-middle">
                {{ heading }}
            </header>

            <nav v-if="breadcrumbs?.length">
                <ol class="flex flex-wrap items-center mb-4 lg:mb-6 mt-1 mx-2 lg:mt-2 lg:mx-4 text-slate-500 dark:text-slate-200 breadcrumbs text-xs">
                    <li v-for="(crumb, i) in breadcrumbs" :key="i">
                        <Link v-if="i < breadcrumbs.length - 1"
                              :href="crumb.url"
                              class="text-slate-500 hover:text-orange-500 dark:text-teal-500"
                        >
                            {{ crumb.title }}
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
        <div class="max-w-6xl mx-auto text-white">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="space-y-2">
                    <a href="#home" class="block hover:underline">Home</a>
                    <a href="#about" class="block hover:underline">About</a>
                    <a href="#services" class="block hover:underline">Services</a>
                    <a href="#contact" class="block hover:underline">Contact</a>
                </div>
                <div class="space-y-2">
                    <a href="https://facebook.com" target="_blank" aria-label="Facebook" class="block hover:underline">Facebook</a>
                    <a href="https://twitter.com" target="_blank" aria-label="Twitter"
                       class="block hover:underline">Twitter</a>
                    <a href="https://instagram.com" target="_blank" aria-label="Instagram"
                       class="block hover:underline">Instagram</a>
                </div>
                <div>
                    <p class="text-sm">Contact us: <a href="mailto:info@example.com" class="hover:underline">info@example.com</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
