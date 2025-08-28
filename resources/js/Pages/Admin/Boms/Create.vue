<script setup>
import Wrapper from '@/Components/Form/Wrapper.vue';
import InputText from '@/Components/Form/InputText.vue';
import {Form, usePage} from '@inertiajs/vue3';
import Submit from '@/Components/Form/Submit.vue';

const page = usePage();
</script>

<template>
    <div class="content content-margin">
        <Form :action="route('admin.boms.store')" method="post" :options="{ preserveScroll: true }">
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
                    />
                </template>
            </Wrapper>

            <Submit>Create</Submit>
        </Form>
    </div>
</template>
