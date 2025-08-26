<script setup>
import {useMenuStore} from '@/Stores/menuStore.js';
import ProfileDropdown from '@/Components/Admin/Navbar/ProfileDropdown.vue';

const store = useMenuStore();

const props = defineProps({
    defaultProfileImage: {type: String, required: true},
    user: {type: Object, required: true},
    logo: {type: String, required: true},
});

const makeMenuVisible = () => {
    store.isMenuVisible = true;
    document.documentElement.classList.add('overflow-hidden');
    document.body.classList.add('overflow-hidden');
};
</script>

<template>
    <div class="flex justify-between items-center bg-slate-800 text-white px-2 lg:px-4 py-2">
        <div class="flex items-center align-middle">
            <button
                class="mr-4 p-2 border rounded-sm border-slate-500 hover:border-slate-600 bg-slate-700 align-middle inline-flex"
                v-if="!store.isLargeScreen"
                @click="makeMenuVisible"
                :title="store.isMenuVisible ? 'Hide Menu' : 'Show Menu'"
            >
                <i class="pi pi-bars"></i>
            </button>
            <a href="#" class="flex items-center text-lg font-semibold text-slate-100">
                <img class="w-8 h-8 mr-1" :src="logo" alt="useTwit">
                <span class="hidden sm:block">useTwit</span>
            </a>
        </div>
        <div class="flex items-center right-buttons">
            <ProfileDropdown :default-profile-image="defaultProfileImage"
                             :user="user"
            />
        </div>
    </div>
</template>
