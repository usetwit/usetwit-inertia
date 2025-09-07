<script setup>
import {router, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import Button from '@/Components/Form/Button.vue';
import {route} from 'ziggy-js';
import Modal from '@/Components/Modal.vue';
import AddressItem from '@/Components/Addresses/AddressItem.vue';
import AddressForm from '@/Components/Addresses/AddressForm.vue';

const page = usePage();
const permissions = computed(() => page.props.permissions);
const user = computed(() => page.props.user);
const defaultCountry = computed(() => page.props.defaultCountry);

const emptyAddress = {
    address_line_1: '',
    address_line_2: '',
    address_line_3: '',
    postcode: '',
    country_code: defaultCountry.value,
};

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showNewModal = ref(false);
const loading = ref(false);
const editItem = ref(null);
const deleteItem = ref(null);
const newItem = ref({...emptyAddress});

const handleMakeDefault = (address) => {
    router.patch(
        route('admin.addresses.user.make-default', {address}),
        {},
        {
            preserveScroll: true,
            onStart: () => (loading.value = true),
            onSuccess: () => router.reload({only: ['user']}),
            onFinish: () => (loading.value = false),
        },
    );
};

const update = () => {
    router.patch(
        route('admin.addresses.user.update', editItem.value),
        editItem.value,
        {
            preserveScroll: true,
            onStart: () => (loading.value = true),
            onSuccess: () => {
                router.reload({only: ['user']});
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

const doDelete = () => {
    router.delete(
        route('admin.addresses.user.destroy', deleteItem.value),
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (loading.value = true),
            onSuccess: () => {
                router.reload({only: ['user']});
                showDeleteModal.value = false;
            },
            onFinish: () => (loading.value = false),
        },
    );
};

const handleDelete = (address) => {
    deleteItem.value = address;
    showDeleteModal.value = true;
};

const doCreateNew = () => {
    router.post(
        route('admin.addresses.user.create', {user: user.value}),
        newItem.value,
        {
            preserveScroll: true,
            preserveState: true,
            onStart: () => (loading.value = true),
            onSuccess: () => {
                router.reload({only: ['user']});
                showNewModal.value = false;
                newItem.value = {...emptyAddress};
            },
            onFinish: () => (loading.value = false),
        },
    );
};
</script>

<template>
    <div v-if="permissions.includes('addresses.user.create')" class="top-links">
        <Button class="create-link"
                @click="showNewModal = true"
        >
            <i class="pi pi-sparkles mr-1"></i>Create New Address
        </Button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        <AddressItem v-for="a in user.addresses"
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
           @accepted="update"
           :loading="loading"
    >
        <AddressForm v-model="editItem"/>
    </Modal>

    <Modal v-if="showDeleteModal"
           v-model="showDeleteModal"
           icon="pi pi-trash"
           label="Delete Address"
           @accepted="doDelete"
           :loading="loading"
    >
        Are you sure you want to delete this address?
    </Modal>

    <Modal v-if="showNewModal"
           v-model="showNewModal"
           icon="pi pi-sparkles"
           label="Create Address"
           @accepted="doCreateNew"
           :loading="loading"
    >
        <AddressForm v-model="newItem"/>
    </Modal>

</template>
