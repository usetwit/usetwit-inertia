<script setup>
import Button from '@/components/Form/Button.vue';

const props = defineProps({
    title: {type: String, default: null},
    variant: {type: String, default: 'primary'},
    icon: {type: String, default: null},
    label: {type: String, default: null},
});

const emit = defineEmits(['closed', 'accepted']);
const isVisible = defineModel();

const close = () => {
    isVisible.value = false;
    emit('closed');
};

const accept = () => {
    emit('accepted');
};
</script>

<template>
    <Teleport to="body">
        <div @click.self="isVisible = false"
             class="bg-gray-900/50 z-950 w-full h-full left-0 top-0 fixed flex items-center justify-center"
        >
            <div class="overflow-y-scroll max-h-full bg-white p-3 rounded-md border border-gray-400">
                <form @submit.prevent="accept">
                    <h2 v-if="title || $slots.title" class="text-lg font-semibold mb-2 md:mb-4">
                        {{ title }}
                        <slot name="title"/>
                    </h2>
                    <div v-if="$slots.default" class="text-gray-700 mb-3 md:mb-6 overflow-y-auto">
                        <slot/>
                    </div>
                    <div class="flex justify-end">
                        <Button type="button"
                                variant="danger"
                                @click="close"
                                class="mr-1"
                                icon="pi pi-times-circle"
                                border
                        >
                            Cancel
                        </Button>
                        <slot name="accept">
                            <Button type="submit"
                                    :variant="variant"
                                    :icon="icon"
                                    :label="label"
                                    border
                            ></Button>
                        </slot>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
