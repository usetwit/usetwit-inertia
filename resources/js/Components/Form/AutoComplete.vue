<script setup>
import useDropdown from '@/composables/useDropdown'
import InputText from '@/components/Form/InputText.vue'
import InputGroup from '@/components/Form/InputGroup.vue'
import { nextTick, onMounted, useTemplateRef } from 'vue'

const props = defineProps({
    items: { type: Array, default: [] },
    dropdown: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    invalid: { type: Boolean, default: false },
    optionLabel: { type: String, required: true },
    optionGroupLabel: { type: String, default: '' },
    optionGroupItems: { type: String, default: '' },
})

const {
    inputRef,
    dropdownStyle,
    showDropdown,
    toggleDropdown,
} = useDropdown('left', 'bottom', true)

const model = defineModel()
const inputTextRef = useTemplateRef('inputTextComp')

onMounted(() => {
    if (inputTextRef.value?.inputElement instanceof HTMLElement) {
        inputRef.value = inputTextRef.value.inputElement
    }
})

const itemSelected = (item) => {
    model.value = item[props.optionLabel]
    showDropdown.value = false

    emit('completed')
}

const emit = defineEmits(['completed'])

const handleInput = () => {
    nextTick(() => {
        showDropdown.value = model.value.length > 0

        emit('completed')
    })
}
</script>

<template>
    <div class="inline-block">
        <InputGroup>
            <InputText ref="inputTextComp"
                       v-model="model"
                       :disabled="disabled"
                       @input="handleInput"
                       :invalid="invalid"
            />
            <button v-if="dropdown"
                    class="inline-flex bg-gray-200 hover:bg-gray-100 text-gray-700 items-center py-2.5 px-2 align-middle"
                    @click="toggleDropdown"
                    type="button"
                    ref="buttonRef"
            >
                <i class="pi pi-chevron-down"></i>
            </button>
        </InputGroup>
    </div>

    <Teleport to="body" v-if="showDropdown">
        <div ref="dropdownRef"
             class="dropdown z-50 w-max"
             :style="dropdownStyle"
        >
            <ul v-if="!optionGroupLabel && items.length">
                <li v-for="item in items"
                    @click="itemSelected(item)"
                    class="flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center px-2 py-1.5 rounded-sm text-nowrap"
                >
                    <slot name="item" v-bind="item">{{ item[optionLabel] }}</slot>
                </li>
            </ul>
            <ul v-else-if="optionGroupLabel && items.length">
                <li v-for="item in items">
                    <slot name="optiongroup" v-bind="item">
                        <div class="font-bold px-2 py-1.5 mx-1 rounded-sm">{{
                                item[optionGroupLabel]
                            }}
                        </div>
                    </slot>
                    <ul v-if="item[optionGroupItems] && item[optionGroupItems].length">
                        <li v-for="subitem in item[optionGroupItems]"
                            @click="itemSelected(subitem)"
                            class="flex cursor-pointer hover:bg-gray-100 text-gray-700 items-center px-2 py-1.5 rounded-sm text-nowrap"
                        >
                            <slot name="item" v-bind="subitem">{{ subitem[optionLabel] }}</slot>
                        </li>
                    </ul>
                </li>
            </ul>
            <div v-else class="italic px-2 py-1.5 mx-1">
                No Results
            </div>
        </div>
    </Teleport>
</template>
