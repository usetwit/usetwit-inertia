<script setup>
import Button from '@/Components/Form/Button.vue';
import {usePage} from '@inertiajs/vue3';
import {computed} from 'vue';

const props = defineProps({
    address: {type: Object, required: true},
    loading: {type: Boolean, default: false},
});

const emit = defineEmits(['edit', 'delete', 'make-default']);

const page = usePage();
const permissions = computed(() => page.props.permissions);
const user = computed(() => page.props.user);
</script>

<template>
    <div class="rounded-2xl border bg-white p-4 shadow-sm"
    >
        <div class="mb-3 flex items-start justify-between">
            <h3 class="text-sm font-semibold">Address</h3>
            <span
                v-if="address.is_default"
                class="rounded-full bg-teal-100 px-2 py-0.5 text-xs font-medium text-teal-800"
            >
                    Default
                </span>
        </div>

        <div class="space-y-0.5 text-sm">
            <p v-if="address.address_line_1">{{ address.address_line_1 }}</p>
            <p v-if="address.address_line_2">{{ address.address_line_2 }}</p>
            <p v-if="address.address_line_3">{{ address.address_line_3 }}</p>
            <p v-if="address.postcode">{{ address.postcode }}</p>
            <p v-if="address.country_name">{{ address.country_name }}</p>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-2">
            <Button variant="warning"
                    size="sm"
                    class="text-xs"
                    @click="$emit('edit')"
                    :loading="loading"
                    v-if="permissions.includes('addresses.user.update') || permissions.includes('addresses.user.update.self')"
            >
                Edit
            </Button>
            <Button variant="danger"
                    size="sm"
                    class="text-xs"
                    @click="$emit('delete', address)"
                    :loading="loading"
                    v-if="permissions.includes('addresses.user.delete') || permissions.includes('addresses.user.delete.self')"
            >
                Delete
            </Button>
            <Button size="sm"
                    class="text-xs"
                    @click="$emit('make-default', address)"
                    :loading="loading"
                    v-if="!address.is_default && permissions.includes('addresses.user.update') || permissions.includes('addresses.user.update.self')"
            >
                Default
            </Button>
        </div>
    </div>
</template>
