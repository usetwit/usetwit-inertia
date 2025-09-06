<script setup>
import {router, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import Button from '@/Components/Form/Button.vue';
import {route} from 'ziggy-js';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';
import Modal from '@/Components/Modal.vue';
import AddressItem from '@/Components/Addresses/AddressItem.vue';
import AddressForm from '@/Components/Addresses/AddressForm.vue';

const page = usePage();
const user = computed(() => page.props.user);
const model = ref(page.props.model);

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const loading = ref(false);
const editAddress = ref(null);

const makeDefault = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        route('admin.addresses.user.make-default', {user: model.value, address: address}),
        {},
        'patch',
    );

    await getResponse();

    if (status.value === 200) {
        model.value.addresses = data.value.addresses;

        toast.success(data.value.message);
    }

    loading.value = false;
};

const update = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        route('admin.addresses.user.update', {user: model.value, address: address}),
        editAddress.value,
        'patch',
    );

    await getResponse();

    if (status.value === 200) {
        model.value.addresses = data.value.addresses;

        toast.success(data.value.message);
    }

    showEditModal.value = false;
    loading.value = false;
};

const edit = (address) => {
    showEditModal.value = true;
    editAddress.value = {...address};
};

const deleteAddress = (address) => {
    showDeleteModal.value = true;
};
</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        <AddressItem v-for="a in model.addresses"
                     :key="a.id"
                     :address="a"
                     :loading="loading"
                     @make-default="makeDefault(a)"
                     @edit="edit(a)"
                     @delete="deleteAddress(a)"
        />
    </div>

    <Modal v-if="showEditModal"
           v-model="showEditModal"
           icon="pi pi-save"
           label="Update Address"
           @accepted="update(editAddress)"
           :loading="loading"
    >
        <AddressForm v-model="editAddress"/>
    </Modal>

</template>
