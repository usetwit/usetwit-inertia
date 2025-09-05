<script setup>
import {router, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import Button from '@/Components/Form/Button.vue';
import {route} from 'ziggy-js';
import useAxios from '@/composables/useAxios.js';
import {toast} from 'vue3-toastify';

const page = usePage();
const user = computed(() => page.props.user);
const model = ref(page.props.model);

const showEditModal = ref(false);
const showDeleteModal = ref(false);
const loading = ref(false);

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

</script>

<template>
    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
        <div
            v-for="a in model.addresses"
            :key="a.id"
            class="rounded-2xl border bg-white p-4 shadow-sm"
        >
            <div class="mb-3 flex items-start justify-between">
                <h3 class="text-sm font-semibold">Address</h3>
                <span
                    v-if="a.is_default"
                    class="rounded-full bg-teal-100 px-2 py-0.5 text-xs font-medium text-teal-800"
                >
                    Default
                </span>
            </div>

            <div class="space-y-0.5 text-sm">
                <p v-if="a.address_line_1">{{ a.address_line_1 }}</p>
                <p v-if="a.address_line_2">{{ a.address_line_2 }}</p>
                <p v-if="a.address_line_3">{{ a.address_line_3 }}</p>
                <p v-if="a.postcode">{{ a.postcode }}</p>
                <p v-if="a.country_name">{{ a.country_name }}</p>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2">
                <Button variant="warning" size="sm" class="text-xs">Edit</Button>
                <Button variant="danger" size="sm" class="text-xs">Delete</Button>
                <Button size="sm" v-if="!a.is_default" class="text-xs" @click="makeDefault(a)">Make Default</Button>
            </div>
        </div>
    </div>
</template>
