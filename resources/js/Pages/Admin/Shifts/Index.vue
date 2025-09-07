<script setup>
import {computed, provide, ref} from 'vue';
import useAxios from '@/composables/useAxios.js';
import DataTable from '@/Components/DataTable/DataTable.vue';
import Column from '@/Components/DataTable/Column.vue';
import useTable from '@/composables/useTable.js';
import {formatDate} from '@/helpers.js';
import useStorage from '@/composables/useStorage.js';
import {Link, usePage} from '@inertiajs/vue3';
import RegexLink from '@/Components/DataTable/RegexLink.vue';
import {route} from 'ziggy-js';

const page = usePage();
const permissions = computed(() => page.props.permissions);
const paginationSettings = computed(() => page.props.paginationSettings);
const dateSettings = computed(() => page.props.dateSettings);

const rows = ref([]);
const loading = ref(false);
const defaultData = {
    filters: {
        global: {constraints: [{value: null, mode: 'contains'}]},
        name: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        created_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
        updated_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
        active: {constraints: [{value: true, mode: 'equals'}]},
    },
    filtered: [],
    columns: [
        {field: 'name', label: 'Name', visible: true, order: 2},
        {field: 'created_at', label: 'Created Date', visible: true, order: 4},
        {field: 'updated_at', label: 'Updated Date', visible: true, order: 5},
        {field: 'active', label: 'Active', visible: true, order: 6},
    ],
    sort: [{field: 'name', order: 'asc'}],
    pagination: {
        page: 1,
        per_page: paginationSettings.value.per_page.default,
        total: 0,
    },
};

const storageInstance = useStorage('shifts-index', defaultData);
const {activeData} = storageInstance;

const fetchItems = async () => {
    loading.value = true;

    const {data, errors, getResponse} = useAxios(route('admin.shifts.get-items'), {
        filters: activeData.value.filters,
        page: activeData.value.pagination.page,
        per_page: activeData.value.pagination.per_page,
        sort: activeData.value.sort,
        visible: activeData.value.columns.filter(col => col.visible).map(col => col.field),
    });

    await getResponse();

    if (!errors.value.raw) {
        rows.value = data.value.items;
        activeData.value.pagination.total = data.value.total;
    } else {
        rows.value = [];
    }

    loading.value = false;
};

const tableInstance = useTable(defaultData, fetchItems, storageInstance);

const {getColumn, isVisible, r} = tableInstance;

provide('tableInstance', tableInstance);
</script>

<template>
    <div class="content-margin">
        <div v-if="permissions.includes('shifts.create')" class="flex justify-end mb-4">
            <Link class="create"
            >
                <i class="pi pi-sparkles mr-1"></i>Create New Shift
            </Link>
        </div>

        <DataTable v-model:rows="rows"
                   v-model="activeData"
                   :loading="loading"
        >

            <Column sticky class="w-16" options>
                <template #body="{ row }">
                    <Link v-if="permissions.includes('shifts.edit')"
                          :href="route('admin.shifts.edit', row)"
                          class="edit"
                          title="Edit"
                    >
                        <i class="pi pi-pen-to-square"></i>
                    </Link>
                </template>
            </Column>
            <Column :column="getColumn('name')" v-if="isVisible('name')" sortable type="string">
                <template #body="{ row }">
                    <span v-html="r('name', row.name)"></span>
                </template>
            </Column>
            <Column :column="getColumn('created_at')" v-if="isVisible('created_at')" sortable type="date">
                <template #body="{ row }">
                    {{ formatDate(row.created_at, dateSettings.format, dateSettings.separator) }}
                </template>
            </Column>
            <Column :column="getColumn('updated_at')" v-if="isVisible('updated_at')" sortable type="date">
                <template #body="{ row }">
                    {{ formatDate(row.updated_at, dateSettings.format, dateSettings.separator) }}
                </template>
            </Column>
            <Column :column="getColumn('active')" v-if="isVisible('active')" sortable type="active"
                    class="text-center">
            </Column>
        </DataTable>
    </div>
</template>
