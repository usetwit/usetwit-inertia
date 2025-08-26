<script setup>
const props = defineProps({
    index: { type: Number, required: true },
})

const model = defineModel()

const removeConstraint = () => {
    model.value.splice(props.index, 1)
}
</script>

<template>
    <button type="button"
            class="text-xs mt-1 rounded-md p-1 bg-white inline-flex items-center justify-center text-red-500 hover:bg-red-100"
            @click.stop="removeConstraint"
    >
        <i class="pi pi-trash mr-2"></i> Remove Constraint
    </button>
</template>
