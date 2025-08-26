<script setup>
import useDropdown from '../../composables/useDropdown.js';
import InputText from '../Form/InputText.vue';
import {computed, inject} from 'vue';
import FilterDropdown from './FilterDropdown.vue';
import Checkbox from '../Form/Checkbox.vue';
import Datepicker from '../Form/Datepicker.vue';

const props = defineProps({
    column: {type: Object, required: true},
    sortObj: {type: Object, default: null},
    filtered: {type: Array, required: true},
});

const filters = defineModel();

const emit = defineEmits(['filter', 'apply']);

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('right');

defineExpose({inputRef});

const apply = () => {
    showDropdown.value = false;
    emit('filter');
};

const settings = inject('dateSettings');

const {getModifiedFields} = inject('tableInstance');

const filtered = computed(() => props.filtered.includes(props.column.field));
const modified = computed(() => getModifiedFields(filters.value, props.filtered)?.includes(props.column.field));
</script>

<template>
    <button v-if="column.type"
            ref="inputRef"
            type="button"
            class="inline-flex items-center p-2 rounded-full ml-2"
            :class="{
                    'hover:bg-gray-200': !sortObj,
                    'hover:bg-slate-600': sortObj,
                    'text-yellow-500': filtered || modified,
                    'text-white': !filtered && !modified && sortObj,
                    'text-gray-500': !filtered && !modified && !sortObj,
                }"
            @click="toggleDropdown"
    >
        <i v-if="!filtered" class="pi pi-filter"></i>
        <i v-if="filtered" class="pi pi-filter-fill"></i>
    </button>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-250 w-64 max-h-80"
             :style="dropdownStyle"
        >
            <FilterDropdown v-if="column.type === 'string'"
                            :column="column"
                            v-model="filters[column.field]"
                            @apply="apply"
                            @filter="$emit('filter')"
            >
                <template #default="{ column, constraint }">
                    <InputText v-model="constraint.value"
                               class="text-sm mt-1 rounded-md"
                               :placeholder="'Search by ' + column.label"
                    />
                </template>
            </FilterDropdown>

            <FilterDropdown v-if="column.type === 'date'"
                            :column="column"
                            v-model="filters[column.field]"
                            @apply="apply"
                            @filter="$emit('filter')"
            >
                <template #default="{ constraint }">
                    <div class="mt-1">
                        <Datepicker v-model="constraint.value"
                                    class="text-sm w-full"
                                    :placeholder="settings.display"
                                    :display-format="settings.display"
                                    :format="settings.format"
                                    :regex="settings.regex"
                                    :separator="settings.separator"
                                    dropdown
                                    container-class="flex"
                                    position-y="bottom"
                        />
                    </div>
                </template>
            </FilterDropdown>

            <FilterDropdown v-else-if="column.type === 'boolean' || column.type === 'active'"
                            :column="column"
                            v-model="filters[column.field]"
                            @apply="apply"
                            @filter="$emit('filter')"
            >
                <template #default="{ column, constraint }">
                    <Checkbox v-model="constraint.value"
                              :indeterminate="!!constraint.value"
                              class="text-sm px-3 py-1.5 rounded-md"
                              :label="column.label"
                    />
                </template>
            </FilterDropdown>
        </div>
    </Teleport>
</template>
