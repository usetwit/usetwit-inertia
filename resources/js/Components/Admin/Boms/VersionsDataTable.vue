<script setup>
import {computed, provide, ref} from 'vue';
import useAxios from '@/composables/useAxios.js';
import DataTable from '@/components/DataTable/DataTable.vue';
import Column from '@/components/DataTable/Column.vue';
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
const bom = computed(() => page.props.bom);

const rows = ref([]);
const loading = ref(false);

const defaultData = {
    filters: {
        global: {constraints: [{value: null, mode: 'contains'}]},
        version: {operator: 'or', constraints: [{value: null, mode: 'contains'}]},
        created_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
        updated_at: {operator: 'or', constraints: [{value: null, mode: 'date_equals'}]},
    },
    filtered: [],
    columns: [
        {field: 'version', label: 'Version', visible: true, order: 3},
        {field: 'created_at', label: 'Created Date', visible: true, order: 4},
        {field: 'updated_at', label: 'Updated Date', visible: true, order: 5},
    ],
    sort: [{field: 'version', order: 'desc'}],
    pagination: {
        page: 1,
        per_page: paginationSettings.value.per_page.default,
        total: 0,
    },
};

const storageInstance = useStorage('bom-versions-index', defaultData);
const {activeData} = storageInstance;

const fetchVersions = async () => {
    loading.value = true;

    const {data, errors, getResponse} = useAxios(route('admin.boms.get-versions', bom.value.slug), {
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

const tableInstance = useTable(defaultData, fetchVersions, storageInstance);

const {getColumn, isVisible, r} = tableInstance;

provide('tableInstance', tableInstance);
</script>

<template>
    <div class="content-margin">
        <DataTable v-model:rows="rows"
                   v-model="activeData"
                   :loading="loading"
        >

            <Column sticky class="w-16" options>
                <template #body="{ row }">
                    <Link
                        v-if="permissions.includes('bom-versions.update')"
                        :href="route('admin.bom-versions.edit', row)"
                        class="bg-amber-500 p-1.5 rounded-sm text-white inline-flex"
                        title="Edit"
                    >
                        <i class="pi pi-pen-to-square"></i>
                    </Link>
                </template>
            </Column>
            <Column :column="getColumn('version')" v-if="isVisible('version')" type="string">
                <template #body="{ row }">
                    <RegexLink :href="route('admin.bom-versions.edit', row)"
                               title="Edit"
                               :html="r('version', row.version)"/>
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
        </DataTable>
    </div>
</template>
