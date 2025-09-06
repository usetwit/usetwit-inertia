<script setup>
import {Link, router, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import Button from '@/Components/Form/Button.vue';
import {route} from 'ziggy-js';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';
import Modal from '@/Components/Modal.vue';
import AddressItem from '@/Components/Addresses/AddressItem.vue';
import AddressForm from '@/Components/Addresses/AddressForm.vue';

const page = usePage();
const permissions = computed(() => page.props.permissions);
const model = computed(() => page.props.model);

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showNewModal = ref(false);
const loading = ref(false);
const editItem = ref(null);
const toDeleteItem = ref(null);

const handleMakeDefault = (address) => {
    router.patch(
        route('admin.addresses.user.make-default', {address}),
        {},
        {
            preserveScroll: true,
            onStart: () => (loading.value = true),
            onSuccess: () => router.reload({only: ['model']}),
            onFinish: () => (loading.value = false),
        },
    );
};

const update = (address) => {
    router.patch(
        route('admin.addresses.user.update', {address}),
        editItem.value,
        {
            preserveScroll: true,
            onStart: () => (loading.value = true),
            onSuccess: () => {
                router.reload({only: ['model']});
                showEditModal.value = false;
            },
            onFinish: () => (loading.value = false),
        },
    );
};

const handleEdit = (address) => {
    editItem.value = {...address};
    showEditModal.value = true;
};

const doDelete = (address) => {
    router.delete(
        route('admin.addresses.user.destroy', {address}),
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (loading.value = true),
            onSuccess: () => {
                router.reload({only: ['model']});
                showDeleteModal.value = false;
            },
            onFinish: () => (loading.value = false),
        },
    );
};

const handleDelete = (address) => {
    toDeleteItem.value = address;
    showDeleteModal.value = true;
};
</script>

<template>
    <div v-if="permissions.includes('addresses.user.create')" class="top-links">
        <Button class="create-link" :href="route('admin.addresses.user.create', {user: model})">
            <i class="pi pi-sparkles mr-1"></i>Create New Address
        </Button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        <AddressItem v-for="a in model.addresses"
                     :key="a.id"
                     :address="a"
                     :loading="loading"
                     @make-default="handleMakeDefault(a)"
                     @edit="handleEdit(a)"
                     @delete="handleDelete(a)"
        />
    </div>

    <Modal v-if="showEditModal"
           v-model="showEditModal"
           icon="pi pi-save"
           label="Update Address"
           @accepted="update(editItem)"
           :loading="loading"
    >
        <AddressForm v-model="editItem"/>
    </Modal>

    <Modal v-if="showDeleteModal"
           v-model="showDeleteModal"
           icon="pi pi-trash"
           label="Delete Address"
           @accepted="doDelete(toDeleteItem)"
           :loading="loading"
    >
        Are you sure you want to delete this address?
    </Modal>

</template>
