<script setup>
import Button from '@/Components/Form/Button.vue';
</script>

<template>
    <div class="p-2 sm:p-4 text-center">
        <Button type="submit" v-bind="$attrs">Save</Button>
    </div>
</template>
