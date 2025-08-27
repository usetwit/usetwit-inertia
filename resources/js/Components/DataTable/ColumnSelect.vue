<script setup>
import {computed, inject} from 'vue';
import useDropdown from '@/composables/useDropdown.js';
import Checkbox from '@/Components/Form/Checkbox.vue';

const columns = defineModel();

const sorted = computed(() => {
    return columns.value.toSorted((a, b) => a.label.localeCompare(b.label));
});

const {clearFilter, clearSort, filter, getSortedFields, getFilteredFields} = inject('tableInstance');

const clearFilterAndSort = field => {
    const doFetch = getSortedFields().includes(field) || getFilteredFields().includes(field);

    clearSort(field);
    clearFilter(field);
    filter(doFetch);
};

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown();
</script>

<template>
    <button type="button"
            ref="inputRef"
            class="p-1.5 rounded-sm inline-flex items-center bg-emerald-500 text-white hover:bg-emerald-400"
            @click="toggleDropdown"
            title="Select Columns"
    >
        <i class="pi pi-bars"></i>
    </button>
    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-50 min-w-64 max-h-80"
             :style="dropdownStyle"
        >
            <ul>
                <li v-for="col in sorted" :key="col.field" class="text-sm">
                    <Checkbox :label="col.label"
                              :id="col.field"
                              v-model="col.visible"
                              @update:model-value="clearFilterAndSort(col.field)"
                              class="select-none w-full px-3 py-1.5"
                    />
                </li>
            </ul>
        </div>
    </Teleport>
</template>
