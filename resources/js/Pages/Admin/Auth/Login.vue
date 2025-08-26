<script>
export default {layout: null};
</script>


<script setup>
import {Form, Head} from '@inertiajs/vue3';
import Button from '@/Components/Form/Button.vue';
</script>

<template>
    <Head title="Sign In - useTwit"/>
    <div class="bg-gradient flex items-center justify-center min-h-[100dvh]">
        <section class="flex flex-col items-center justify-center w-full max-w-md mx-1 md:mx-auto">
            <div class="w-full bg-white rounded-lg shadow-sm">
                <div class="space-y-4 p-4">
                    <h1 class="flex items-center text-xl font-bold text-gray-800">
                        <span class="ml-1 pt-1.5">Sign in</span>
                    </h1>
                    <Form method="post"
                          action="/admin/login"
                          #default="{ errors, processing }"
                    >
                        <div v-if="Object.keys(errors).length" class="mb-4 p-3 bg-red-200 text-red-600 rounded-lg">
                            <ul class="list-disc ml-5">
                                <li v-for="(msg, key) in errors" :key="key">{{ msg }}</li>
                            </ul>
                        </div>

                        <label for="username" class="block mb-1">Username</label>
                        <input type="text"
                               name="username"
                               id="username"
                               required
                               class="bg-gray-50 border rounded-lg block w-full p-2.5 focus:ring-teal-500 focus:border-teal-500"
                               :class="errors.username ? 'border-red-600' : 'border-gray-300'"
                        />

                        <label for="password" class="block mb-1 mt-3">Password</label>
                        <input type="password"
                               name="password"
                               id="password"
                               required
                               class="bg-gray-50 border rounded-lg block w-full p-2.5 focus:ring-teal-500 focus:border-teal-500"
                               :class="errors.username ? 'border-red-600' : 'border-gray-300'"
                        />

                        <Button :loading="processing"
                                :disabled="processing"
                                type="submit"
                                class="mt-4 w-full"
                                variant="login"
                                size="lg"
                        >{{ processing ? 'Signing In...' : 'Sign In' }}
                        </Button>
                    </Form>

                </div>

                <div class="text-gray-800 mt-4 text-sm drop-shadow-sm text-center mb-2">&copy;2024 useTwit</div>

            </div>
        </section>
    </div>
</template>
