<script setup>
import Button from '@/components/Form/Button.vue';

const props = defineProps({
    permissions: {type: Object, required: true},
    address: {type: Object, required: true},
    loading: {type: Boolean, default: false},
});

const emit = defineEmits(['edit', 'delete', 'make-default']);
</script>

<template>
    <div class="relative p-4 border rounded"
         :class="address.is_default ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-100'"
    >
          <span v-if="address.is_default"
                class="absolute shadow top-2 right-2 px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded"
          >
    DEFAULT
  </span>

        <p class="text-sm font-medium">{{ address.address_line_1 }}</p>
        <p class="text-sm">{{ address.address_line_2 }}</p>
        <p class="text-sm">{{ address.address_line_3 }}</p>
        <p class="text-sm">{{ address.postcode }}</p>
        <p class="text-sm">{{ address.country_name }}</p>

        <div class="mt-4 flex space-x-2">
            <Button v-if="permissions.edit_address && !address.is_default"
                    @click="emit('make-default', address)"
                    icon="pi pi-map-marker"
                    variant="success"
                    size="sm"
                    :loading="loading"
            >
                Make Default
            </Button>

            <Button v-if="permissions.edit_address"
                    @click="emit('edit', address)"
                    icon="pi pi-file-edit"
                    variant="warning"
                    size="sm"
                    :loading="loading"
            >
                Edit
            </Button>

            <Button v-if="permissions.delete_address"
                    @click="emit('delete', address)"
                    icon="pi pi-trash"
                    variant="danger"
                    size="sm"
                    :loading="loading"
            >
                Delete
            </Button>
        </div>
    </div>
</template>
