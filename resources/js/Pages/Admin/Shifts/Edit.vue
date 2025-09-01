<script setup>
import useAxios from '@/composables/useAxios.js';
import {computed, ref} from 'vue';
import InputText from '@/Components/Form/InputText.vue';
import Wrapper from '@/Components/Form/Wrapper.vue';
import Submit from '@/Components/Form/Submit.vue';
import {route} from 'ziggy-js';
import {Form, Link, usePage} from '@inertiajs/vue3';
import {cloneDeep} from 'lodash';

const props = defineProps({
    shift: {type: Object, required: true},
});

const page = usePage();
const permissions = computed(() => page.props.permissions);
const exists = ref(false);
const shift = ref(cloneDeep(props.shift));

const nameExists = async () => {
    if (shift.value.name) {
        const {data, getResponse} = useAxios(
            route('admin.shifts.name-exists'),
            {name: shift.value.name},
            'post',
        );

        await getResponse();
        exists.value = data.value.exists;
    }
};
</script>

<template>
    <div class="content content-margin">
        <div v-if="permissions.includes('calendars.edit')" class="top-links content-margin mt-4">
            <Link class="edit-link" :href="route('admin.calendars.edit', shift.calendar.id)">
                <i class="pi pi-pen-to-square mr-1"></i>Edit Shift Calendar
            </Link>
        </div>

        <Form :action="route('admin.shifts.update', shift)" method="patch" #default="{processing}">
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
                               v-model="shift.name"
                               @input="nameExists"
                    />
                    <p v-if="!exists && shift.name.toUpperCase() !== props.shift.name.toUpperCase()"
                       class="text-green-600 text-xs mt-1">
                        <i class="pi pi-check mr-1"></i>Name available
                    </p>
                    <p v-else-if="exists && shift.name.toUpperCase() !== props.shift.name.toUpperCase()"
                       class="input-error-msg">
                        <i class="pi pi-times mr-1"></i>Name already in use
                    </p>
                </template>
            </Wrapper>

            <Submit :loading="processing">Update</Submit>
        </Form>
    </div>
</template>
