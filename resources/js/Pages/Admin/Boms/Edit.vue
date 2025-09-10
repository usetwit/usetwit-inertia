<script setup>
import useAxios from '@/composables/useAxios.js';
import {ref} from 'vue';
import InputText from '@/Components/Form/InputText.vue';
import Wrapper from '@/Components/Form/Wrapper.vue';
import Submit from '@/Components/Form/Submit.vue';
import {route} from 'ziggy-js';
import {Form, usePage} from '@inertiajs/vue3';
import {cloneDeep} from 'lodash';
import VersionsDataTable from '@/Components/Admin/Boms/VersionsDataTable.vue';

const props = defineProps({
    bom: {type: Object, required: true},
});

const page = usePage();
const exists = ref(false);
const bom = ref(cloneDeep(props.bom));

const nameExists = async () => {
    if (bom.value.name) {
        const {data, status, getResponse} = useAxios(
            route('admin.boms.name-exists'),
            {name: bom.value.name},
            'post',
        );

        await getResponse();

        if (status.value === 200) {
            exists.value = data.value.exists;
        }
    }
};
</script>

<template>
    <div class="content content-margin">
        <Form :action="route('admin.boms.update', props.bom)" method="patch" #default="{processing}">
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
                               @input="nameExists"
                    />
                    <p v-if="!exists && bom.name.toUpperCase() !== props.bom.name.toUpperCase()"
                       class="input-success-msg">
                        <i class="pi pi-check mr-1"></i>Name available
                    </p>
                    <p v-else-if="exists && bom.name.toUpperCase() !== props.bom.name.toUpperCase()"
                       class="input-error-msg">
                        <i class="pi pi-times mr-1"></i>Name already in use
                    </p>
                </template>
            </Wrapper>

            <Submit :loading="processing">Update</Submit>
        </Form>
    </div>

    <h2 class="text-2xl content-margin font-bold text-slate-700 mb-2 mt-8">BOM Versions</h2>
    <div class="content content-margin py-4">
        <VersionsDataTable/>
    </div>
</template>
