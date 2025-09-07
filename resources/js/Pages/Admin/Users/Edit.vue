<script setup>
import {computed, ref, useTemplateRef, watch} from 'vue';
import Tab from '@/components/Tab.vue';
import Wrapper from '@/components/Form/Wrapper.vue';
import InputText from '@/components/Form/InputText.vue';
import Datepicker from '@/components/Form/Datepicker.vue';
import useAxios from '@/composables/useAxios.js';
import {debounce} from 'lodash';
import {Form, usePage} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import Submit from '@/Components/Form/Submit.vue';
import AddressList from '@/Components/Addresses/AddressList.vue';

const page = usePage();
const user = ref({
    ...page.props.user,
    new_password: '',
    new_password_confirmation: '',
    current_password: '',
});
const auth = computed(() => page.props.auth);
const permissions = computed(() => page.props.permissions);

const usernameExists = ref(false);
const employeeIdExists = ref(false);
const loading = ref(false);
const tabs = ref([]);
const activeTab = ref(null);

const rules = [
    {
        key: 'personal_profile',
        icon: 'pi pi-user-edit',
        text: 'Personal',
        perm: permissions.value.includes('users.update.personal-profile')
            || (permissions.value.includes('users.update.self.personal-profile') && user.value.id === auth.value.id),
    },
    {
        key: 'company_profile',
        icon: 'pi pi-building',
        text: 'Company',
        perm: permissions.value.includes('users.update.company-profile')
            || (permissions.value.includes('users.update.self.company-profile') && user.value.id === auth.value.id),
    },
    {
        key: 'image',
        icon: 'pi pi-image',
        text: 'Profile Image',
        perm: permissions.value.includes('users.update.image')
            || (permissions.value.includes('users.update.self.image') && user.value.id === auth.value.id),
    },
    {
        key: 'address',
        icon: 'pi pi-map',
        text: 'Address',
        perm: permissions.value.includes('addresses.user.update')
            || (permissions.value.includes('addresses.user.update.self') && user.value.id === auth.value.id),
    },
    {
        key: 'password',
        icon: 'pi pi-key',
        text: 'Password',
        perm: permissions.value.includes('users.update.password')
            || (permissions.value.includes('users.update.self.password') && user.value.id === auth.value.id),
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
            <Form :action="route('admin.users.update.personal-profile', user)"
                  method="patch"
                  :options="{
                      preserveState: true,
                      preserveScroll: true,
                      only: ['user', 'flash']
                  }"
                  #default="{
                      processing
                  }"
            >
                <Wrapper required>
                    <template #text>
                        <label for="first_name">
                            First Name
                        </label>
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="first_name"
                                   name="first_name"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="First Name"
                                   v-model="user.first_name"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="middle_names">
                            Middle Names
                        </label>
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="middle_names"
                                   name="middle_names"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="Middle Names"
                                   v-model="user.middle_names"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="last_name">
                            Middle Names
                        </label>
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="last_name"
                                   name="last_name"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="Last Name"
                                   v-model="user.last_name"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="dob">
                            Date of Birth
                        </label>
                    </template>

                    <template #input>
                        <Datepicker v-model="user.dob"
                                    dropdown
                                    :errors="page.props.errors"
                                    name="dob"
                                    class="w-full sm:w-60"
                                    id="dob"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_number">
                            Personal Phone Number
                        </label>
                    </template>

                    <template #help>
                        Only numbers, spaces, + ( ) . -
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="personal_number"
                                   name="personal_number"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="Personal Phone Number"
                                   v-model="user.personal_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                                   title="Only numbers, spaces, + ( ) . -"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_mobile_number">
                            Personal Mobile Phone Number
                        </label>
                    </template>

                    <template #help>
                        Only numbers, spaces, + ( ) . -
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="personal_mobile_number"
                                   name="personal_mobile_number"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="Personal Mobile Phone Number"
                                   v-model="user.personal_mobile_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                                   title="Only numbers, spaces, + ( ) . -"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="personal_email">
                            Personal Email
                        </label>
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="personal_email"
                                   name="personal_email"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="85"
                                   placeholder="Personal Email"
                                   v-model="user.personal_email"
                                   type="email"
                                   title="Enter a valid email address (example@domain.com)"
                        />
                    </template>
                </Wrapper>

                <Submit :loading="processing">Update</Submit>

            </Form>
        </div>
        <div v-else-if="activeTab.key === 'company_profile'">
            <Form :action="route('admin.users.update.company-profile', user)"
                  method="patch"
                  :options="{
                      preserveState: true,
                      preserveScroll: true,
                      only: ['user', 'flash'],
                  }"
                  #default="{
                      processing
                  }"
            >
                <Wrapper>
                    <template #text>
                        <label for="company_ext">Company Ext</label>
                    </template>

                    <template #help>
                        Only numbers and spaces
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="company_ext"
                                   name="company_ext"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="255"
                                   placeholder="Company Ext"
                                   v-model="user.company_ext"
                                   pattern="^[0-9 ]*$"
                                   title="Only numbers and spaces allowed"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="company_number">Company Number</label>
                    </template>

                    <template #help>
                        Only numbers, spaces, + ( ) . -
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="company_number"
                                   name="company_number"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="255"
                                   placeholder="Company Number"
                                   v-model="user.company_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                                   title="Only numbers, spaces, + ( ) . -"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="company_mobile_number">Company Mobile Number</label>
                    </template>

                    <template #help>
                        Only numbers, spaces, + ( ) . -
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   id="company_mobile_number"
                                   name="company_mobile_number"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="255"
                                   placeholder="Company Mobile Number"
                                   v-model="user.company_mobile_number"
                                   pattern="^[0-9 \+\(\)\.\-]*$"
                                   title="Only numbers, spaces, + ( ) . -"
                        />
                    </template>
                </Wrapper>

                <Wrapper>
                    <template #text>
                        <label for="email">Email</label>
                    </template>

                    <template #input>
                        <InputText class="w-full sm:w-60"
                                   type="email"
                                   id="email"
                                   name="email"
                                   :errors="page.props.errors"
                                   rounded
                                   show-errors
                                   maxlength="255"
                                   placeholder="Email"
                                   v-model="user.email"
                                   title="Enter a valid email address (example@domain.com)"
                        />
                    </template>
                </Wrapper>

                <Submit :loading="processing">Update</Submit>

            </Form>
        </div>
        <div v-else-if="activeTab.key === 'address'" class="p-4">
            <AddressList/>
        </div>

    </div>
</template>
