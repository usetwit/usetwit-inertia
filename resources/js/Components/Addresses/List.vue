<script setup>
import {computed, ref} from 'vue';
import Button from '@/components/Form/Button.vue';
import Item from '@/components/Addresses/Item.vue';
import Modal from '@/components/Modal.vue';
import Wrapper from '@/components/Form/Wrapper.vue';
import InputText from '@/components/Form/InputText.vue';
import Select from '@/components/Form/Select.vue';
import Checkbox from '@/components/Form/Checkbox.vue';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';
import AddressForm from '@/Components/Addresses/AddressForm.vue';

const props = defineProps({
    permissions: {
        type: Object,
        required: true,
        validator: p => ['create_address', 'edit_address', 'delete_address'].every(k => typeof p[k] === 'boolean'),
    },
    countries: {type: Array, required: true},
    defaultCountry: {type: String, required: true},
    routes: {type: Object, required: true},
});

const loading = ref(false);
const addresses = defineModel({type: Array});

const sortedAddresses = computed(() => {
    return addresses.value
        .slice()
        .sort((a, b) => {
            if (a.is_default && !b.is_default) return -1;
            if (!a.is_default && b.is_default) return 1;

            return new Date(b.updated_at) - new Date(a.updated_at);
        });
});

const showNewAddressModal = ref(false);
const showEditAddressModal = ref(false);

const newAddress = ref({
    'address_line_1': '',
    'address_line_2': '',
    'address_line_3': '',
    'postcode': '',
    'country_code': '',
    'is_default': false,
});

const editingAddress = ref(null);

const edit = (address) => {
    editingAddress.value = {...address};
    showEditAddressModal.value = true;
};

newAddress.value.country_code = props.defaultCountry;

const saveNewAddress = async () => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        props.routes.create_address,
        newAddress.value,
    );

    await getResponse();

    if (status.value === 201) {
        addresses.value = data.value.addresses;

        newAddress.value = {
            'address_line_1': '',
            'address_line_2': '',
            'address_line_3': '',
            'postcode': '',
            'country_code': props.defaultCountry,
            'is_default': false,
        };

        showNewAddressModal.value = false;
        toast.success(data.value.message);
    }

    loading.value = false;
};

const makeDefault = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        address.routes.make_default,
        {},
        'patch',
    );

    await getResponse();

    if (status.value === 200) {
        addresses.value = data.value.addresses;

        toast.success(data.value.message);
    }

    loading.value = false;
};

const deleteAddress = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        address.routes.delete,
        {},
        'delete',
    );

    await getResponse();

    if (status.value === 200) {
        addresses.value = data.value.addresses;

        toast.success(data.value.message);
    }

    loading.value = false;
};

const update = async (address) => {
    loading.value = true;

    const {data, status, getResponse} = useAxios(
        editingAddress.value.routes.update,
        editingAddress.value,
        'patch',
    );

    await getResponse();

    if (status.value === 200) {
        addresses.value = data.value.addresses;

        showEditAddressModal.value = false;
        toast.success(data.value.message);
    }

    loading.value = false;
};

</script>

<template>
    <Modal v-model="showNewAddressModal"
           v-if="showNewAddressModal"
           label="Save"
           variant="success"
           title="Add Address"
           icon="pi pi-save"
           @accepted="saveNewAddress"
    >
        <AddressForm :countries="countries" :default-country="defaultCountry" v-model="newAddress"/>
        <Wrapper>
            <template #input>
                <Checkbox id="is_default"
                          name="is_default"
                          class="mt-2"
                          v-model="newAddress.is_default"
                          label="Set as Default"
                />
            </template>
        </Wrapper>
    </Modal>
    <Modal v-model="showEditAddressModal"
           v-if="showEditAddressModal"
           label="Save"
           variant="success"
           title="Edit Address"
           icon="pi pi-save"
           @accepted="update"
    >
        <AddressForm :countries="countries" :default-country="defaultCountry" v-model="editingAddress"/>
    </Modal>

    <div class="space-y-4 p-4">
        <div v-if="permissions.create_address" class="text-right">
            <Button @click="showNewAddressModal = true"
                    icon="pi pi-plus"
            >
                New Address
            </Button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <Item v-for="address in sortedAddresses"
                  :key="address.id"
                  :permissions="permissions"
                  :address="address"
                  @make-default="makeDefault"
                  @delete="deleteAddress"
                  @edit="edit"
                  :loading="loading"
            />

            <div v-if="addresses.length === 0"
                 class="col-span-full text-center text-gray-500 p-4"
            >
                No addresses found.
            </div>
        </div>
    </div>
</template>
