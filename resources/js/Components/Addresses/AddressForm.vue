<script setup>
import InputText from '@/components/Form/InputText.vue';
import Select from '@/components/Form/Select.vue';
import Wrapper from '@/components/Form/Wrapper.vue';

const props = defineProps({
    countries: {type: Array, required: true},
    defaultCountry: {type: String, required: true},
});

const address = defineModel();
</script>

<template>
    <Wrapper required>
        <template #text>
            <label for="address_line_1">
                Address Line 1
            </label>
        </template>
        <template #input>
            <InputText v-model="address.address_line_1"
                       class="rounded-md"
                       maxlength="255"
                       name="address_line_1"
                       id="address_line_1"
                       required
            ></InputText>
        </template>
    </Wrapper>
    <Wrapper>
        <template #text>
            <label for="address_line_2">
                Address Line 2
            </label>
        </template>
        <template #input>
            <InputText v-model="address.address_line_2"
                       class="rounded-md"
                       maxlength="255"
                       name="address_line_2"
                       id="address_line_2"
            ></InputText>
        </template>
    </Wrapper>
    <Wrapper>
        <template #text>
            <label for="address_line_3">
                Address Line 3
            </label>
        </template>
        <template #input>
            <InputText v-model="address.address_line_3"
                       class="rounded-md"
                       maxlength="255"
                       name="address_line_3"
                       id="address_line_3"
            ></InputText>
        </template>
    </Wrapper>
    <Wrapper>
        <template #text>
            <label for="postcode">
                Postal Code
            </label>
        </template>
        <template #input>
            <InputText v-model="address.postcode"
                       class="rounded-md"
                       maxlength="12"
                       name="postcode"
                       id="postcode"
            ></InputText>
        </template>
    </Wrapper>
    <Wrapper>
        <template #text>
            <label>Country</label>
        </template>
        <template #input>
            <Select v-model="address.country_code"
                    :options="countries"
                    option-label="name"
                    option-value="code"
                    placeholder="Select a Country"
                    class="w-full"
                    show-clear
                    filter
            />
        </template>
    </Wrapper>
</template>
