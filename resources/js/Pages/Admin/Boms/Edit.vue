<script setup>
import useAxios from '@/composables/useAxios.js';
import {ref} from 'vue';
import InputText from '@/Components/Form/InputText.vue';
import Wrapper from '@/Components/Form/Wrapper.vue';
import Submit from '@/Components/Form/Submit.vue';
import {route} from 'ziggy-js';
import {Form, usePage} from '@inertiajs/vue3';
import {cloneDeep} from 'lodash';

const props = defineProps({
    bom: {type: Object, required: true},
});

const page = usePage();
const exists = ref(false);
const bom = ref(cloneDeep(props.bom));

const checkName = async () => {
    if (bom.value.name) {
        const {data, getResponse} = useAxios(
            route('admin.boms.name-exists'),
            {name: bom.value.name},
            'post',
        );

        await getResponse();
        exists.value = data.value.exists;
    }
};
</script>

<template>
    <div class="content content-margin">
        <Form :action="route('admin.boms.update', props.bom)" method="patch">
            <Wrapper required>
                <template #text>
                    <label for="name">Name</label>
                </template>

                <template #input>
                    <InputText name="name"
                               id="name"
                               maxlength="255"
                               :errors="page.props.errors"
                               required
                               rounded
                               show-errors
                               v-model="bom.name"
                               @input="checkName"
                    />
                    <p v-if="!exists && bom.name !== props.bom.name" class="text-green-600 text-xs mt-1">
                        <i class="pi pi-check mr-1"></i>Name available
                    </p>
                    <p v-else-if="exists && bom.name !== props.bom.name" class="input-error-msg">
                        <i class="pi pi-times mr-1"></i>Name already in use
                    </p>
                </template>
            </Wrapper>
            <Submit>Update</Submit>
        </Form>
    </div>
</template>

<style scoped>

</style>
