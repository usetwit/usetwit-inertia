<script setup>
import {Link} from '@inertiajs/vue3';
import {useAttrs} from 'vue';

const props = defineProps({
    href: {type: String, required: true},
    html: {type: String, required: true},
});

const attrs = useAttrs();
</script>

<template>
    <Link :href="href" v-bind="attrs">
        <span v-html="html"/>
    </Link>
</template>
