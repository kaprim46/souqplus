<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header & form -->
                    <template v-if="!isLoading">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                    <i class="icon mgc_user_edit_fill"></i>
                                    {{ $t('Update customer') }} - {{ form.name }}
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $t('Update customer informations and save it to your database') }}
                                </p>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Form -->
                        <div class="px-6 py-4">
                            <customer-form
                                :form="form"
                                @update:form="form = $event"
                                :errors="errors"
                                @update:errors="errors = $event"
                            />
                        </div>
                        <!-- End Form -->
                    </template>

                    <!--Loader-->
                    <div  v-if="isLoading" class="flex items-center justify-center p-4">
                        <v-loader :size="4" :dark="true" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script lang="ts" setup>
import {onMounted, ref, nextTick, inject} from 'vue'
import CustomerForm from "./components/DataForm.vue"
import {FormCustomer} from "@/types/User";
import { useRoute,useRouter } from "vue-router";

//ref form
const form = ref<FormCustomer>({
    role: "customer",
    name: '',
    email: '',
    password: '',
    confirm_password: '',
    country_code: '',
    phone_number: '',
    is_verified: '0',
})

const errors = ref({})
const route = useRoute()
const router = useRouter()
const isLoading = ref(false)

const getDetails = async () => {
    if(!route.params.id) return;

    isLoading.value = true

    try {
        const { data } = await axios.get(`/dashboard/customers/${route.params.id}`);

        if(data.ok) {
            form.value = data.customer;
            await nextTick(() => {
                isLoading.value = false
            })
            return;
        }

        /**
         * Redirect back
         */
        await router.push({ name: 'customers.index' })
    } catch (error) {
        await router.push({ name: 'customers.index' })
    }
}

onMounted(async () => {
    await getDetails()
})
</script>
