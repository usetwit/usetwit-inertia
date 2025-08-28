<script setup>
const props = defineProps({
    required: {type: Boolean, default: false},
    vertical: {type: Boolean, default: false},
});
</script>

<template>
    <div class="border-b border-gray-200 p-2 sm:p-4 gap-1 sm:gap-2"
         :class="vertical ? 'flex flex-col': 'grid grid-cols-1 sm:grid-cols-2'"
    >
        <div class="min-w-0 text-slate-700 pt-1.5 break-words">
            <slot name="text"/>
            <sup v-if="required" class="text-red-500 ml-1">required</sup>
            <div v-if="$slots.help" class="text-xs sm:text-sm text-gray-500 break-words">
                <slot name="help"/>
            </div>
        </div>

        <div class="min-w-0">
            <slot name="input"/>
        </div>
    </div>
</template>


<style scoped>
@reference '../../../css/app.css';

:deep(label) {
    @apply font-bold text-sm lg:text-base;
}
</style>
