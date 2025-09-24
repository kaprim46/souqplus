<template>
        <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
        <!-- Form Group -->
        <div>
            <label for="name" class="block text-sm mb-2 dark:text-white">
                {{ $t('Full Name') }}
            </label>
            <v-input
                type="text"
                id="name"
                :value="form.name"
                :placeholder="$t('Full Name')"
                @update:input="form.name = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="name-error" v-if="errors.name">
                @{{ errors.name[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
            <label for="email" class="block text-sm mb-2 dark:text-white">
                {{ $t('Email Address') }}
            </label>
            <v-input
                type="email"
                id="email"
                :value="form.email"
                :placeholder="$t('Email Address')"
                @update:input="form.email = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="email-error" v-if="errors.email">
                @{{ errors.email[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
            <label for="phone_number" class="block text-sm mb-2 dark:text-white">
                {{ $t('Phone Number') }}
            </label>
            <v-phoneInput
                @update:phoneNumber="form.phone_number = $event"
                @update:countryCode="form.country_code = $event"
                :value="form.phone_number"
                :valueCountryCode="form.country_code ?? '+212'"
                id="phone_number"
            />
            <p class="text-xs text-red-600 mt-2" id="phone-error" v-if="errors.phone_number">
                @{{ errors.phone_number[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!--is_verified-->
        <div>
            <label for="is_verified" class="block text-sm mb-2 dark:text-white">
                {{ $t('Is Verified') }}
            </label>
            <v-select
                id="is_verified"
                name="is_verified"
                :value="form.is_verified === true ? '1' : '0'"
                @update:value="form.is_verified = $event"
            >
                <option value="1">{{ $t('Yes') }}</option>
                <option value="0">{{ $t('No') }}</option>
            </v-select>
            <p class="text-xs text-red-600 mt-2" id="is_verified-error" v-if="errors.is_verified">
                @{{ errors.is_verified[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
            <label for="role" class="block text-sm mb-2 dark:text-white">
                {{ $t('Role') }}
            </label>
            <v-select
                id="role"
                :value="form.role"
                @update:value="form.role = $event"
            >
                <option value="customer">{{ $t('Customer') }}</option>
                <option value="business">{{ $t('Business') }}</option>
                <option value="admin">{{ $t('Admin') }}</option>
            </v-select>
            <p class="text-xs text-red-600 mt-2" id="role-error" v-if="errors.role">
                @{{ errors.role[0] }}
            </p>
        </div>

        <hr class="col-span-1 md:col-span-2">

        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 col-span-1 md:col-span-2">
            <i class="icon mgc_lock_line"></i>
            {{ $t('Password') }}
        </h1>

        <!-- Form Group -->
        <div v-if="form.id">
            <label for="current-password" class="block text-sm mb-2 dark:text-white">
                {{ $t('Current Password') }}
            </label>
            <v-input
                type="password"
                id="current-password"
                :value="form.current_password"
                :placeholder="$t('Current Password')"
                @update:input="form.current_password = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="current-password-error" v-if="errors.current_password">
                @{{ errors.current_password[0] }}
            </p>
        </div>

        <!-- Form Group -->
        <div>
            <label for="password" class="block text-sm mb-2 dark:text-white">
                {{ $t('Password') }}
            </label>
            <v-input
                type="password"
                id="password"
                :value="form.password" :placeholder="$t('Password')"
                @update:input="form.password = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="password-error" v-if="errors.password">
                @{{ errors.password[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
            <label for="confirm-password" class="block text-sm mb-2 dark:text-white">
                {{ $t('Confirm Password') }}
            </label>
            <v-input
                type="password"
                id="confirm-password"
                :value="form.confirm_password"
                :placeholder="$t('Confirm Password')"
                @update:input="form.confirm_password = $event"
            />
        </div>
        <!-- End Form Group -->

        <!-- Actions -->
        <div class="col-span-1 md:col-span-2">
            <div class="flex justify-end">
                <v-button
                    type="submit"
                    color="green"
                    :loading="loading"
                    @click="form.id ? onUpdate() : onCreate()"
                >
                    <i :class="form.id ? 'icon mgc_edit_line' : 'icon mgc_add_line'"></i>
                    {{ form.id ? $t('Update') : $t('Create') }}
                </v-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {inject, nextTick, ref} from "vue";
import {notify} from "notiwind";
import {wTrans} from "laravel-vue-i18n";

const loading = ref(false);

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:form', 'update:errors']);

/**
 * Create a new customer.
 */
const onCreate = async () => {
    loading.value = true;

    await nextTick();

    try {
        const { data } = await axios.post('/dashboard/customers', props.form);

        if(data.ok) {
            emit('update:form', {
                role: "customer",
                name: '',
                email: '',
                password: '',
                confirm_password: '',
                country_code: '',
                phone_number: ''
            });
        }

        emit('update:errors', {});

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000);

    } catch (e: any) {
        console.log(e.response.status, e.response.data.errors);
        if(e.response.status === 422) {
            emit('update:errors', e.response.data.errors);
        }
    } finally {
        loading.value = false;
    }
};

/**
 * Update the customer.
 */
const onUpdate = async () => {
    if(!props.form.id) return;

    loading.value = true;

    await nextTick();

    try {
        const { data } = await axios.put(`/dashboard/customers/${props.form.id}`, props.form);

        if(data.ok) {
            emit('update:form', data.customer);
        }

        emit('update:errors', {});

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000)
    } catch (e: any) {
        if(e.response.status === 422) {
            emit('update:errors', e.response.data.errors);
        }
    } finally {
        loading.value = false;
    }
};
</script>
