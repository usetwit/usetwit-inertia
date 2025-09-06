<script setup>
import Wrapper from '@/Components/Form/Wrapper.vue';
import InputText from '@/Components/Form/InputText.vue';
import Select from '@/Components/Form/Select.vue';
import {usePage} from '@inertiajs/vue3';
import {computed} from 'vue';

const address = defineModel();
const page = usePage();
const countries = computed(() => page.props.countries);
</script>

<template>
    <Wrapper required>
        <template #text>
            <label for="address_line_1">Address Line 1</label>
        </template>

        <template #input>
            <InputText id="address_line_1"
                       rounded
                       v-model="address.address_line_1"
            />
        </template>
    </Wrapper>

    <Wrapper>
        <template #text>
            <label for="address_line_2">Address Line 2</label>
        </template>

        <template #input>
            <InputText id="address_line_2"
                       rounded
                       v-model="address.address_line_2"
            />
        </template>
    </Wrapper>

    <Wrapper>
        <template #text>
            <label for="address_line_3">Address Line 3</label>
        </template>

        <template #input>
            <InputText id="address_line_3"
                       rounded
                       v-model="address.address_line_3"
            />
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
                       rounded
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
