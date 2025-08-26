<script setup>
import { inject } from 'vue'

const props = defineProps({
    type: { type: String, required: true },
})

const constraints = defineModel()
const { getModeFromMap } = inject('tableInstance')

const addConstraint = () => {
    const mode = getModeFromMap(props.type)
    constraints.value.push({ value: null, mode })
}
</script>

<template>
    <button type="button"
            class="text-xs mt-1 rounded-md p-1 bg-white flex items-center justify-center text-blue-500 hover:bg-blue-100"
            @click.stop="addConstraint"
    >
        <i class="pi pi-plus mr-2"></i> Add Constraint
    </button>
</template>
