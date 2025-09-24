<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_group_line"></i>
                                {{ $t('Customers') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your customers') }}
                            </p>
                        </div>


                        <div class="flex flex-col md:flex-row gap-2">
                            <v-input :placeholder="$t('Search')" @update:input="filters.search = $event" />

                            <v-button color="green" class="whitespace-nowrap" @click="router.push({ name: 'customer.create' })">
                                <i class="icon mgc_add_circle_fill"></i>
                                {{ $t('Create new customer') }}
                            </v-button>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="relative overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 rtl:text-right ltr:text-left">
                                <tr>
                                    <th scope="col" class="px-6 py-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                          {{ $t('Full name') }}
                                        </span>
                                    </th>

                                    <th scope="col" class="px-6 py-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                            {{ $t('Phone') }}
                                        </span>
                                    </th>

                                    <th scope="col" class="px-6 py-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                          {{ $t('Role') }}
                                        </span>
                                    </th>

                                    <!-- How many advertisements -->
                                    <th scope="col" class="px-6 py-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                          {{ $t('Advertisements') }}
                                        </span>
                                    </th>

                                    <th scope="col" class="px-6 py-2"></th>

                                    <th scope="col" class="px-6 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="customer in customers" :key="customer.id" v-if="!isLoading">
                                    <td class="h-px px-6 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <img class="inline-block h-[2.375rem] w-[2.375rem] rounded-full object-cover" :src="`${$assetUrl}/avatar.svg`" :alt="customer.name" />
                                            <div class="grow">
                                               <span class="flex items-center gap-1">
                                                   <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ customer.name }}</span>
                                                    <span v-if="customer.is_verified"  class="w-[20px] h-[20px] bg-blue-500 rounded-full flex items-center justify-center text-white">
                                                        <i class="icon mgc_check_2_fill"></i>
                                                    </span>
                                                </span>
                                                <span class="block text-sm text-gray-500">
                                                        {{ customer.email }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <a
                                            :href="`tel:${customer.country_code}${customer.phone_number}`"
                                            dir="ltr"
                                            class="whitespace-nowrap cursor-pointer px-6 py-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                            <i class="icon mgc_phone_line"></i>
                                            ({{ customer.country_code }}) {{  customer.phone_number }}
                                        </a>
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <v-badge
                                            :color="customer.role === 'admin' ? 'purple' : (customer.role === 'customer' ? 'blue' : 'green')"
                                            :text="`${customer.role === 'admin' ? '<i class=\'icon mgc_user_star_line text-xl\'></i>' : '<i class=\'icon mgc_user_1_line text-xl\'></i>'} ${customer.role === 'admin' ? $t('Admin') : (customer.role === 'customer' ? $t('Customer') : $t('Business'))}`"
                                        />
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <v-badge color="indigo" :text="$t(':count Advertisements', { count: customer.advertisements_count })" />
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <v-badge v-if="customer.deleted_at" color="red" :text="$t('Account deleted at :date', { date: customer.deleted_at })" />
                                    </td>
                                    <td  class="h-px px-6 py-2">
                                        <div class="flex justify-end gap-x-2">
                                            <!-- Ads list -->
                                            <v-button color="indigo" class="whitespace-nowrap" @click="router.push({ name: 'advertisements.index', query: { user_id: customer.id } })">
                                                <i class="icon mgc_list_check_2_fill"></i>
                                                {{ $t('Ads list') }}
                                            </v-button>


                                            <v-button color="blue" class="whitespace-nowrap" @click="router.push({ name: 'customer.edit', params: { id: customer.id } })">
                                                <i class="icon mgc_edit_line"></i>
                                                {{ $t('Edit') }}
                                            </v-button>

                                            <v-button color="red" class="whitespace-nowrap" @click="onDelete(customer.id)" v-if="!customer.deleted_at">
                                                <i class="icon mgc_delete_line"></i>
                                                {{ $t('Delete') }}
                                            </v-button>

                                            <v-button color="green" class="whitespace-nowrap" v-else @click="onRestore(customer.id)">
                                                <i class="icon mgc_restore_line"></i>
                                                {{ $t('Restore') }}
                                            </v-button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Loader -->
                                <tr v-else>
                                    <td colspan="6">
                                        <div class="flex items-center justify-center p-4">
                                            <v-loader v-if="isLoading" :size="4" :dark="true" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ pagination.total }}
                                </span>
                                {{ $t('Results') }}
                            </p>
                        </div>

                        <div>
                            <div class="inline-flex gap-x-2">
                                <button
                                        @click="getCustomers(true, true)"
                                        :disabled="pagination.current_page === 1"
                                        type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    {{ $t('Previous') }}
                                </button>

                                <button
                                        @click="getCustomers(true)"
                                        :disabled="pagination.current_page === pagination.last_page"
                                        type="button"
                                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    {{ $t('Next') }}
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer -->
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {inject, nextTick, onMounted, ref, watch} from 'vue'
import _ from "lodash";
import {CustomersList} from "@/types/User";
import {trans, wTrans} from "laravel-vue-i18n";
import router from "@/routes";
import {notify} from "notiwind";

/**
 * States
 */
const customers = ref<CustomersList[]>([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 1,
    total: 1,
});
const filters = ref({search: '', orderBy: ''});
const isLoading = ref(false);
const swal = inject('$swal')

const getCustomers = async (isPagination = false, isLess = false) => {
    isLoading.value             = true;
    let params                  = {};


    if(filters.value.search) {
        params.search        = filters.value.search;
    }

    if(filters.value.orderBy) {
        params.order_by      = filters.value.orderBy;
    }

    if(isPagination) {
        if(isLess) { pagination.value.current_page--; } else { pagination.value.current_page++; }
    }

    try {
        const { data }          = await axios.get(`/dashboard/customers?page=${pagination.value.current_page}`, {
            params: {
                ...params
            }
        });

        if(data.ok) {
            customers.value     = data.customers.data;

            pagination.value    = {
                current_page: data.customers.current_page,
                last_page: data.customers.last_page,
                per_page: data.customers.per_page,
                total: data.customers.total,
            }
        }

        await nextTick();

        isLoading.value         = false;
    } catch (error) {
        isLoading.value         = false;
    }
}

const onDelete = async (id: number) => {
    /**
     * confirm sweet alert
     */
    const { value } = await swal.fire({
        title: trans('Are you sure?'),
        text: trans('You will not be able to recover this customer!'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: trans('Yes, delete it!'),
        cancelButtonText: trans('No, keep it')
    })

    if(value) {
        try {
            const { data } = await axios.delete(`/dashboard/customers/${id}`);

            if(data.ok) {
                customers.value = customers.value.filter(customer => customer.id !== id);
            }

            await nextTick();

            notify({
                group: "dashboard",
                title: data.ok ? trans("Success") : trans("Error"),
                text: data.message
            }, 2000);
        } catch (error) {
            console.log(error)
        }
    }
}

/**
 * Watchers
 */
watch(filters.value, _.debounce(async () => {
    console.log('watching')
    await getCustomers()
}, 500))

onMounted(async () => {
    await getCustomers()
})
</script>
