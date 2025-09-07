<script setup>
import useStorage from '@/composables/useStorage.js';
import useDropdown from '@/composables/useDropdown.js';
import {Form} from '@inertiajs/vue3';

const props = defineProps({
    profileImage: {type: String, required: true},
    auth: {type: Object, required: true},
});

const {activeData: darkMode, set: saveToStorage} = useStorage('dark-mode', false);

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;

    if (darkMode.value) {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }

    saveToStorage();
};

if (darkMode.value) {
    document.body.classList.add('dark');
}

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('right', 'bottom', false);

</script>

<template>
    <button ref="inputRef"
            class="flex items-center rounded-full bg-slate-700 ml-3 hover:bg-slate-600 border border-slate-500"
            @click="toggleDropdown"
    >
        <span class="pl-4 hidden sm:block mr-2 whitespace-nowrap text-sm">{{ auth.full_name }}</span>
        <span class="rounded-full bg-emerald-500">
            <img :src="profileImage"
                 :alt="auth.full_name"
                 class="object-cover w-10 h-10 rounded-full shadow-sm"
            />
        </span>
    </button>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-350 min-w-48"
             :style="dropdownStyle"
        >
            <ul>
                <li class="w-full">
                    <button @click="toggleDarkMode"
                            :title="darkMode ? 'Light Mode' : 'Dark Mode'"
                            class="p-2 flex items-center align-middle w-full rounded-md hover:bg-gray-100"
                    >
                        <i v-if="darkMode" class="pi pi-sun mr-2"></i>
                        <i v-else class="pi pi-moon mr-2"></i>
                        <span>{{ darkMode ? 'Light Mode' : 'Dark Mode' }}</span>
                    </button>
                </li>
                <li class="w-full">
                    <button title="Edit Profile"
                            class="p-2 flex items-center align-middle w-full rounded-md hover:bg-gray-100"
                    >
                        <i class="pi pi-user-edit mr-2"></i>
                        <span>Edit Profile</span>
                    </button>
                </li>
                <li class="w-full">
                    <button title="Settings"
                            class="p-2 flex items-center align-middle w-full rounded-md hover:bg-gray-100"
                    >
                        <i class="pi pi-cog mr-2"></i>
                        <span>Settings</span>
                    </button>
                </li>
                <li class="w-full mt-1 border-t border-gray-400">
                    <Form :action="route('admin.auth.logout')" method="post" class="mt-1">
                        <button type="submit"
                                title="Sign Out"
                                class="p-2 flex items-center align-middle w-full rounded-md hover:bg-gray-100"
                        >
                            <i class="pi pi-sign-out mr-2"></i>
                            <span>Sign Out</span>
                        </button>
                    </Form>
                </li>
            </ul>
        </div>
    </Teleport>
</template>
