<script setup>
import {computed, provide, ref} from 'vue';
import useAxios from '@/composables/useAxios.js';
import DataTable from '@/components/DataTable/DataTable.vue';
import Column from '@/components/DataTable/Column.vue';
import useTable from '@/composables/useTable.js';
import {formatDate} from '@/helpers.js';
import useStorage from '@/composables/useStorage.js';
import {Link, usePage} from '@inertiajs/vue3';
import {route} from 'ziggy-js';

const page = usePage();
const permissions = computed(() => page.props.permissions);
const user = computed(() => page.props.user);
const paginationSettings = computed(() => page.props.paginationSettings);
const dateSettings = computed(() => page.props.dateSettings);

const rows = ref([]);
const loading = ref(false);
const defaultData = {
    filters: {
        global: {constraints: [{value: null, mode: 'contains'}]},
        id: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        name: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        created_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
        updated_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
        active: {constraints: [{value: true, mode: 'equals'}]},
    },
    filtered: [],
    columns: [
        {field: 'id', label: 'ID', visible: true, order: 1},
        {field: 'name', label: 'Name', visible: true, order: 2},
        {field: 'version', label: 'Version', visible: true, order: 3},
        {field: 'created_at', label: 'Created Date', visible: true, order: 4},
        {field: 'updated_at', label: 'Updated Date', visible: true, order: 5},
        {field: 'active', label: 'Active', visible: true, order: 6},
        {field: 'username', label: 'Username', visible: true, order: 7},
    ],
    sort: [{field: 'name', order: 'asc'}],
    pagination: {
        page: 1,
        per_page: paginationSettings.value.per_page.default,
        total: 0,
    },
};

const storageInstance = useStorage('boms-index', defaultData);
const {activeData} = storageInstance;

const fetchItems = async () => {
    loading.value = true;

    const {data, errors, getResponse} = useAxios(route('admin.boms.get-boms'), {
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
        <div v-if="permissions.includes('boms.create')" class="top-links">
            <Link class="create-link" :href="route('admin.boms.create')">
                <i class="pi pi-sparkles mr-1"></i>Create New BOM
            </Link>
        </div>

        <DataTable v-model:rows="rows"
                   v-model="activeData"
                   :loading="loading"
        >

            <Column sticky class="w-16" options>
                <template #body="{ row }">
                    <Link
                        v-if="permissions.includes('boms.update') || (permissions.includes('boms.update.self') && row.user_id === user.id)"
                        :href="route('admin.boms.edit', row)"
                        class="record-edit"
                        title="Edit"
                    >
                        <i class="pi pi-pen-to-square"></i>
                    </Link>
                </template>
            </Column>
            <Column :column="getColumn('id')" v-if="isVisible('id')" sortable type="string">
                <template #body="{ row }">
                    <span v-html="r('id', row.id)"></span>
                </template>
            </Column>
            <Column :column="getColumn('name')" v-if="isVisible('name')" sortable type="string">
                <template #body="{ row }">
                    <span v-html="r('name', row.name)"></span>
                </template>
            </Column>
            <Column :column="getColumn('version')" v-if="isVisible('version')" class="text-center">
                <template #body="{ row }">
                    <Link :href="route('admin.bom-versions.edit', row)" title="Edit" v-if="row.version">v{{
                            row.version
                        }}
                    </Link>
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
            <Column :column="getColumn('active')"
                    v-if="isVisible('active')"
                    sortable
                    type="active"
                    class="text-center">
            </Column>
            <Column :column="getColumn('username')" v-if="isVisible('username')"/>
        </DataTable>
    </div>
</template>
