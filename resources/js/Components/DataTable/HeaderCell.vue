<script setup>
import {computed, inject, ref, useTemplateRef} from 'vue';
import Filter from './Filter.vue';
import ColumnSelect from './ColumnSelect.vue';

const props = defineProps({
    column: {type: Object, required: true},
    isLast: {type: Boolean, default: false},
    table: {type: Object, default: null},
});

const emit = defineEmits(['sort', 'filter']);
const activeData = defineModel();
const resizeLeftStyle = defineModel('resizeLeftStyle');
const defaultWidth = 20;

const sortObj = computed(() => {
    const index = activeData.value.sort.findIndex(obj => obj.field === props.column.field);

    if (index !== -1) {
        const {field, order} = activeData.value.sort[index];
        return {field, order, position: index + 1};
    }

    return null;
});

const filterEl = useTemplateRef('filterRef');
const thRef = useTemplateRef('thRef');

const filterClicked = event => {
    return filterEl.value?.inputRef.childNodes && Array.from(filterEl.value?.inputRef.childNodes).includes(event.target) || event.target === filterEl.value?.inputRef;
};

const applySort = (column, removeOtherSorts = false) => {
    const sortField = activeData.value.sort.find(col => col.field === column.field);

    if (sortField) {
        if (sortField.order === 'desc') {
            activeData.value.sort = activeData.value.sort.filter(col => col.field !== column.field);
        } else {
            sortField.order = 'desc';
        }
    } else {
        activeData.value.sort.push({field: column.field, order: 'asc'});
    }

    if (removeOtherSorts) {
        activeData.value.sort = activeData.value.sort.filter(col => col.field === column.field);
    }
};

const singleClick = (event, column) => {
    if (!filterClicked(event) && props.column.sortable) {
        applySort(column, true);
        emit('sort');
    }
};

const ctrlClick = (event, column) => {
    if (!filterClicked(event) && props.column.sortable) {
        applySort(column);
        emit('sort');
    }
};

const mouseX = ref();
const {getColumn} = inject('tableInstance');
const activeDataCol = getColumn(props.column.field);
const width = computed(() => activeDataCol && activeDataCol.width !== null && activeDataCol.width !== undefined ?
    activeDataCol.width :
    defaultWidth);

const resizeMouseup = event => {
    resizeLeftStyle.value = null;
    window.removeEventListener('mousemove', resizeMousemove);

    const movement = event.pageX - mouseX.value;
    const currentWidth = thRef.value.getBoundingClientRect().width;

    if (movement !== 0) {
        activeDataCol.width = Math.max(defaultWidth, currentWidth + movement);
    }

    window.removeEventListener('mouseup', resizeMouseup);
};

const resizeMousemove = event => {
    const x = event.pageX + 2;
    resizeLeftStyle.value = {'left': x + 'px'};
};

const resizeMousedown = event => {
    const x = event.pageX + 2;
    resizeLeftStyle.value = {'left': x + 'px'};
    mouseX.value = event.pageX;
    window.addEventListener('mouseup', resizeMouseup);
    window.addEventListener('mousemove', resizeMousemove);
};

const resizeDblclick = () => {
    activeDataCol.width = defaultWidth;
};
</script>

<template>
    <th class="border-y p-0 select-none border-gray"
        :class="{
            'sticky left-0 z-25': column.sticky,
            'relative z-24': !column.sticky,
            'bg-white text-gray-800': !sortObj,
            'hover:bg-gray-100': !sortObj && column.sortable,
            'bg-slate-800 text-white hover:bg-slate-700': sortObj,
            'cursor-pointer': column.sortable,
        }"
        :style="{
            width: width + 'px',
            minWidth: width + 'px',
        }"
        ref="thRef"
    >
        <div v-if="column.options" class="px-4 py-3.5 flex items-center">
            <ColumnSelect v-model="activeData.columns"/>
        </div>

        <template v-else-if="column.label">
            <div class="px-4 py-2 flex justify-between items-center"
                 @click.exact="singleClick($event, column)"
                 @click.ctrl="ctrlClick($event, column)"
            >
                <div class="inline-flex items-center">
                    <span class="py-2 whitespace-nowrap">{{ column.label }}</span>
                    <span v-if="sortObj" class="text-white inline-flex ml-2">
                        <i v-if="sortObj.order === 'asc'" class="pi pi-sort-amount-up-alt"></i>
                        <i v-if="sortObj.order === 'desc'" class="pi pi-sort-amount-down-alt"></i>
                        <span
                            class="ml-2 flex items-center justify-center h-5 p-1.5 min-w-5 bg-slate-500 text-white text-xs rounded-full"
                        >
                            {{ sortObj.position }}
                        </span>
                    </span>
                    <span v-else-if="column.sortable" class="text-gray-500 ml-2"><i class="pi pi-sort-alt"></i></span>
                </div>
                <Filter v-if="column.type"
                        v-model="activeData.filters"
                        :filtered="activeData.filtered"
                        :column="column"
                        :sort-obj="sortObj"
                        ref="filterRef"
                        @filter="$emit('filter')"
                />
            </div>
            <span class="absolute top-0 right-0 h-full w-2 cursor-col-resize"
                  @mousedown="resizeMousedown"
                  @dblclick="resizeDblclick"
            ></span>
        </template>
        <div v-else class="px-4 py-3 flex justify-between items-center">
            &nbsp;
        </div>
    </th>
</template>
