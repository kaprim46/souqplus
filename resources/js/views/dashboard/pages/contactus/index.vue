<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_messenger_line"></i>
                                {{ $t('Contact Us') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your contact us messages') }}
                            </p>
                        </div>


                        <div class="flex gap-x-2">
                            <v-input :placeholder="$t('Search')" @update:input="contactUsStore.filters.search = $event" />
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="relative overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-slate-800 rtl:text-right ltr:text-left">
                            <tr>
                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('Name') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('Subject') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                        {{ $t('Is Read') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('Is Reply') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                   <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('IP Address') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('Created At') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('Last Update') }}
                                    </span>
                                </th>

                                <th scope="col" class="text-end"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="!contactUsStore.isLoading" v-for="msg in contactUsStore.messages" :key="msg.id">
                                <td class="h-px px-6 py-2">
                                    <div class="flex items-center gap-2">
                                        <img :src="`${$assetUrl}/avatar.svg`" class="w-8 h-8 rounded-full"  alt=""/>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ msg.name }}
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ msg.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap">
                                        {{ msg.subject }}
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap">
                                        <v-badge :color="msg.is_read ? 'green' : 'yellow'" :text="msg.is_read ? $t('Yes') : $t('No')" />
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap">
                                        <v-badge :color="msg.is_replied ? 'green' : 'yellow'" :text="msg.is_replied ? $t('Yes') : $t('No')" />
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ msg.ip ?? $t('--') }}
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ msg.created_at }}
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ msg.updated_at ?? $t('--') }}
                                    </div>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <div class="whitespace-nowrap py-3 px-5 flex items-center gap-2 justify-end">
                                        <v-button color="blue" @click="router.push({ name: 'contactus.view', params: { id: msg.id } })">
                                            <i class="icon mgc_eye_2_fill" />
                                            {{ $t('View') }}
                                        </v-button>
                                        <v-button color="red" @click="contactUsStore.deleteMessage(msg.id as number)">
                                            <i class="icon mgc_delete_line" />
                                            {{ $t('Delete') }}
                                        </v-button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Loader -->
                            <tr v-else>
                                <td colspan="6">
                                    <div class="flex items-center justify-center p-4">
                                        <v-loader :size="4" :dark="true" />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div
                        v-if="!contactUsStore.isLoading"
                        class="px-6 py-2 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ contactUsStore.pagination.total }}
                                </span>
                                {{ $t('Results') }}
                            </p>
                        </div>

                        <div>
                            <div class="inline-flex gap-x-2">
                                <button
                                    @click="contactUsStore.getMessages(true, true)"
                                    :disabled="contactUsStore.pagination.current_page === 1"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    {{ $t('Previous') }}
                                </button>

                                <button
                                    @click="contactUsStore.getMessages(true)"
                                    :disabled="contactUsStore.pagination.current_page === contactUsStore.pagination.last_page"
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
import {onMounted, watch} from 'vue'
import router from "@/routes";
import _ from "lodash";
import {useContactUsStore} from "@/stores/ContactUsStore";

/**
 * States
 */
const contactUsStore = useContactUsStore()



/**
 * Watchers
 */
watch(contactUsStore.filters, _.debounce(async () => {
    await contactUsStore.getMessages()
}, 500))

onMounted(async () => {
    await contactUsStore.getMessages()
})
</script>
