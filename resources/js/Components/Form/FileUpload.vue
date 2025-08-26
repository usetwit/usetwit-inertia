<script setup>
import { useTemplateRef } from 'vue'

const props = defineProps({
    accept: { type: String, default: '' },
    multiple: { type: Boolean, default: false },
})

const fileInput = useTemplateRef('fileInput')

defineOptions({
    inheritAttrs: false,
})

const files = defineModel()

const handleDrop = event => {
    files.value = event.dataTransfer.files
}

const handleFileChange = event => {
    files.value = event.target.files
}
</script>

<template>
    <div class="flex flex-col">
        <div
            class="border-dashed rounded-md border-2 p-2 border-gray-300 min-w-80 text-gray-400 cursor-pointer bg-gray-50 hover:bg-gray-100"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="fileInput.click()"
            v-bind="$attrs"
        >
            <span>Drag & drop your files here or click to upload</span>

            <ul v-if="files && files.length" class="m-2">
                <li v-for="file in files" class="text-gray-500">{{ file.name }}</li>
            </ul>
        </div>

        <input
            type="file"
            ref="fileInput"
            class="hidden"
            :accept="accept"
            :multiple="multiple"
            @change="handleFileChange"
        />
    </div>
</template>
