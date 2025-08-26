<script setup>
const props = defineProps({
    buttons: { type: Boolean, default: true },
})
</script>

<template>
    <div class="flex items-center text-sm border-b border-gray-200 p-2 mb-3 text-slate-700"
            :class="{'justify-between': buttons, 'justify-center': !buttons}"
    >
        <button v-if="buttons"
                type="button"
                @click.stop="$emit('change', 'decrease')"
                class="mr-2 align-middle inline-flex"
        >
            <i class="pi pi-angle-left"></i>
        </button>
        <slot></slot>
        <button v-if="buttons"
                type="button"
                @click.stop="$emit('change', 'increase')"
                class="ml-2 align-middle inline-flex"
        >
            <i class="pi pi-angle-right"></i>
        </button>
    </div>
</template>
