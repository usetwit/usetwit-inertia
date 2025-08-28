<script setup>
import {useMenuStore} from '@/Stores/menuStore';
import {computed, watch} from 'vue';
import {Link} from '@inertiajs/vue3';
import {route} from 'ziggy-js';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const items = computed(() => props.items);

const isActive = (l) => {
    return [l.route, ...(l.matches ?? [])].some(n => route().current(n));
};

watch(() => props.items, () => {
    for (const item of items.value) {
        item.expanded = item.links?.some(l => isActive(l));
    }
}, {immediate: true});

const toggleExpanded = (item) => {
    item.expanded = !item.expanded;
};

const store = useMenuStore();

const closeMenu = () => {
    store.isMenuVisible = false;
    document.documentElement.classList.remove('overflow-hidden');
    document.body.classList.remove('overflow-hidden');
};

const checkScreenSize = () => {
    store.isLargeScreen = window.innerWidth >= 1024;

    if (store.isLargeScreen) {
        closeMenu();
    }
};

checkScreenSize();
window.addEventListener('resize', checkScreenSize);
</script>

<template>
    <div v-if="store.isMenuVisible && !store.isLargeScreen"
         class="opacity-50 bg-gray-900 z-150 w-full h-full left-0 top-0 fixed"
         @click="closeMenu"
    ></div>

    <nav v-if="(store.isMenuVisible && !store.isLargeScreen) || store.isLargeScreen"
         class="bg-white border-r w-[280px] border-gray-200 overflow-y-auto"
         :class="{
            'z-200 fixed h-full top-0 left-0':
                !store.isLargeScreen && store.isMenuVisible,
        }"
    >
        <div v-if="store.isMenuVisible && !store.isLargeScreen"
             class="flex pt-2 pr-4"
        >
            <button class="ml-auto flex items-center text-sm text-slate-700"
                    @click="closeMenu"
            >
                Close
            </button>
        </div>

        <ul class="p-4">
            <li v-for="item in items" class="mb-4" :key="item.id">
                <button @click="toggleExpanded(item)"
                        type="button"
                        class="flex items-center w-full text-slate-700"
                >
                    <span
                        class="rounded-sm h-8 w-8 mr-3 justify-center inline-flex items-center border border-gray-200">
                        <i :class="item.icon"></i>
                    </span>
                    <span class="text-sm font-bold">{{ item.label }}</span>
                    <span class="flex-1 text-right">
                        <i :class="{
                                'pi pi-angle-down': !item.expanded,
                                'pi pi-angle-up': item.expanded,
                            }"
                        ></i>
                    </span>
                </button>
                <ul v-show="item.expanded" class="ml-4 mt-2">
                    <li v-for="link in item.links" :key="link.id">
                        <div v-if="link.type === 'heading'"
                             class="text-sm font-bold text-orange-600 py-1"
                        >
                            {{ link.label }}
                        </div>
                        <Link v-else
                              :href="route(link.route)"
                              :class="[
                                'text-sm hover:text-slate-800 hover:border-l-gray-400 px-3 py-2 block border-l',
                                {
                                    'text-sky-800 border-sky-800': isActive(link),
                                    'text-gray-600 border-gray-200': !isActive(link),
                                },
                            ]"
                        >
                            {{ link.label }}
                        </Link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</template>
