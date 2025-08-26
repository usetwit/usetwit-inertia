<script setup>
import {inject} from 'vue';

const props = defineProps({
    row: {type: Object, required: true},
    col: {type: Object, required: true},
});

const tableInstance = inject('tableInstance');
const {setConstraints} = tableInstance;

const setConstraintsCb = value => setConstraints(props.col, value);
</script>

<template>
    <td class="px-4 py-3 border-b border-gray-200 text-sm md:text-base">
        <component v-if="col.body" :is="col.body" :row="row" :col="col" :setConstraintsCb="setConstraintsCb"/>
        <button v-else-if="!col.body && col.type === 'active'"
                :class="{'text-green-500': row.active, 'text-red-500': !row.active}"
                @click="setConstraintsCb(row.active)"
        >
            <i v-if="row.active" class="pi pi-check-circle" title="Active"></i>
            <i v-else class="pi pi-times-circle" title="Inactive"></i>
        </button>
        <template v-else>{{ row[col.field] }}</template>
    </td>
</template>
