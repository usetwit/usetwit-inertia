<script setup>
import {getCurrentInstance, inject, onBeforeUnmount, onMounted} from 'vue';

const props = defineProps({
    column: {type: Object, default: null},
    sortable: {type: Boolean, default: false},
    sticky: {type: Boolean, default: false},
    label: {type: String, default: null},
    type: {type: String, default: null},
    options: {type: Boolean, default: false},
});

defineOptions({
    inheritAttrs: false,
    render() {
        return null;
    },
});

const registerColumn = inject('registerColumn');
const deregisterColumn = inject('deregisterColumn');

const instance = getCurrentInstance();
const column = {
    field: props.column?.field,
    order: props.column?.order,
    width: props.column?.width,
    label: props.column?.label || props.label,
    sticky: props.sticky,
    sortable: props.sortable,
    body: instance.vnode.children?.body,
    filter: instance.vnode.children?.filter,
    type: props.type,
    attrs: instance.attrs,
    props: instance.props,
    options: props.options,
};

onMounted(() => {
    registerColumn(column);
});

onBeforeUnmount(() => {
    deregisterColumn(column);
});
</script>
