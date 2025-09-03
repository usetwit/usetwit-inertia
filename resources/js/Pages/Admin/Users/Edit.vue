<script setup>
import {computed, ref, useTemplateRef, watch} from 'vue';
import Tab from '@/components/Tab.vue';
import Wrapper from '@/components/Form/Wrapper.vue';
import InputText from '@/components/Form/InputText.vue';
import Button from '@/components/Form/Button.vue';
import FormWrapper from '@/components/Form/Wrapper.vue';
import Select from '@/components/Form/Select.vue';
import Datepicker from '@/components/Form/Datepicker.vue';
import Password from '@/components/Form/Password.vue';
import useAxios from '@/composables/useAxios.js';
import {debounce, pick} from 'lodash';
import {toast} from 'vue3-toastify';
import Modal from '@/components/Modal.vue';
import AddressList from '@/components/Addresses/List.vue';
import {Form, router, usePage} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import Submit from '@/Components/Form/Submit.vue';

const page = usePage();
const model = ref({
    ...page.props.model,
    new_password: '',
    new_password_confirmation: '',
    current_password: '',
});
const user = computed(() => page.props.user);
const permissions = computed(() => page.props.permissions);
const countries = computed(() => page.props.countries);
const defaultCountry = computed(() => page.props.defaultCountry);
const roles = computed(() => page.props.roles);

const usernameExists = ref(false);
const employeeIdExists = ref(false);
const loading = ref(false);
const tabs = ref([]);
const activeTab = ref(null);
const errorFields = ref([]);
const deleteModalIsVisible = ref(false);
const deleteUserForm = useTemplateRef('deleteUserForm');

const rules = [
    {
        key: 'personal_profile',
        icon: 'pi pi-user-edit',
        text: 'Personal',
        perm: permissions.value.includes('users.update.personal-profile')
            || (permissions.value.includes('users.update.self.personal-profile') && model.value.id === user.value.id),
    },
    {
        key: 'company_profile',
        icon: 'pi pi-building',
        text: 'Company',
        perm: permissions.value.includes('users.update.company-profile')
            || (permissions.value.includes('users.update.self.company-profile') && model.value.id === user.value.id),
    },
    {
        key: 'image',
        icon: 'pi pi-image',
        text: 'Profile Image',
        perm: permissions.value.includes('users.update.image')
            || (permissions.value.includes('users.update.self.image') && model.value.id === user.value.id),
    },
    {
        key: 'address',
        icon: 'pi pi-map',
        text: 'Address',
        perm: permissions.value.includes('addresses.user.update')
            || (permissions.value.includes('addresses.user.update.self') && model.value.id === user.value.id),
    },
    {
        key: 'password',
        icon: 'pi pi-key',
        text: 'Password',
        perm: permissions.value.includes('users.update.password')
            || (permissions.value.includes('users.update.self.password') && model.value.id === user.value.id),
    },
    {
        key: 'protected_info',
        icon: 'pi pi-exclamation-circle',
        text: 'Admin',
        perm: permissions.value.includes('users.update.protected-info'),
    },
];

rules.forEach(rule => {
    if (rule.perm) {
        tabs.value.push({
            key: rule.key,
            text: `<i class="${rule.icon} hidden md:inline-block mr-2"></i>${rule.text}`,
        });
    }
});

const currentHash = window.location.hash.slice(1);
const activeTabFromHash = tabs.value.find(tab => tab.key === currentHash);
activeTab.value = activeTabFromHash || tabs.value[0];

const handleClick = text => {
    activeTab.value = text;
};

const updateUsernameExists = async () => {
    if (user.value.username) {
        loading.value = true;

        const {data, errors, getResponse} = useAxios(
            route('admin.users.username-exists'),
            {username: user.value.username},
            'post',
        );
        await getResponse();

        if (!errors.value.raw) {
            usernameExists.value = data.value.exists;
        }

        loading.value = false;
    } else {
        usernameExists.value = false;
    }
};

const updateEmployeeIdExists = async () => {
    if (user.value.employee_id) {
        loading.value = true;

        const {data, errors, getResponse} = useAxios(
            route('admin.users.employee-id-exists'),
            {employee_id: user.value.employee_id},
            'post',
        );
        await getResponse();

        if (!errors.value.raw) {
            employeeIdExists.value = data.value.exists;
        }

        loading.value = false;
    } else {
        employeeIdExists.value = false;
    }
};

const debouncedUpdatedUsernameExists = debounce(updateUsernameExists, 300, {leading: true, trailing: true});
const debouncedUpdateEmployeeIdExists = debounce(updateEmployeeIdExists, 300, {leading: true, trailing: true});

watch(() => user.value.username, (newValue) => {
    user.value.username = newValue.toLowerCase();
});

const keepHash = (hash) => {
    location.hash = hash;
    toast.success('User updated');
};
</script>

<template>
    <ul v-if="tabs.length" class="flex mx-0 lg:mx-4 overflow-x-auto -mb-[1px]">
        <Tab v-for="tab in tabs"
             :tab="tab"
             :key="tab.key"
             :active="tab.key === activeTab.key"
             @clicked="handleClick"
        />
    </ul>

    <div class="content content-margin">

        <div v-if="activeTab.key === 'personal_profile'">
            <Form :action="route('admin.users.update.personal-profile', model)"
                  method="patch"
                  @success="() => keepHash('personal_profile')"
            >
                <Wrapper required>
                    <template #text>
                        <label for="first_name">
                            First Name
                        </label>
                    </template>

                    <template #input>
                        <InputText class="rounded-md w-full sm:w-60"
                                   id="first_name"
                                   name="first_name"
                                   :errors="page.props.errors"
                                   required
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="First Name"
                                   v-model="model.first_name"
                        />
                    </template>
                </Wrapper>

                <Submit>Update</Submit>

            </Form>
        </div>
    </div>
</template>
