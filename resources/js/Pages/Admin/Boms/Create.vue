<script setup>
import Wrapper from '@/Components/Form/Wrapper.vue';
import InputText from '@/Components/Form/InputText.vue';
</script>

<template>
    <div class="content content-margin">

        <Wrapper required>
            <template #text>
                <label for="name">Name</label>
            </template>

            <template #input>
                <InputText v-model="name" name="name" id="name" pattern="[A-Za-z0-9]+" rounded/>
            </template>

            <template #help>
                Alphanumeric only
            </template>
        </Wrapper>

    </div>
</template>

<style scoped>

</style>
