<script setup>
import Wrapper from '@/Components/Form/Wrapper.vue';
import InputText from '@/Components/Form/InputText.vue';
import {Form, usePage} from '@inertiajs/vue3';
import Submit from '@/Components/Form/Submit.vue';

const page = usePage();
</script>

<template>
    <div class="content content-margin">
        <Form :action="route('admin.boms.store')" method="post">
            <Wrapper required>
                <template #text>
                    <label for="name">Name</label>
                </template>

                <template #input>
                    <InputText name="name" id="name" :errors="page.props.errors" rounded show-errors/>
                </template>

                <template #help>
                    Alphanumeric only
                </template>
            </Wrapper>

            <Wrapper required>
                <template #text>
                    <label for="test">Test</label>
                </template>

                <template #input>
                    <InputText name="test" id="test" :errors="page.props.errors" rounded show-errors/>
                </template>

                <template #help>
                    Alphanumeric only
                </template>
            </Wrapper>

            <Submit text="Submit"/>
        </Form>
    </div>
</template>
