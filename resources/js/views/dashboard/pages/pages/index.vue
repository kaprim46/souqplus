<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_border_blank_line"></i>
                                {{ $t('Pages') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your pages') }}
                            </p>
                        </div>

                        <div class="flex flex-col md:flex-row gap-2">
                            <v-input :placeholder="$t('Search')" @update:modelValue="pageStore.filters.search = $event" />
                            <v-button color="green" class="whitespace-nowrap" @click="router.push({ name: 'page.create' })">
                                <i class="icon mgc_add_circle_fill"></i>
                                {{ $t('Create new page') }}
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
                                          {{ $t('Name') }}
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                            {{ $t('Status') }}
                                        </span>
                                    </th>
                                    <th scope="col" class="px-6 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-if="!pageStore.isLoading" v-for="page in pageStore.pages" :key="page.id">
                                    <td class="h-px px-6 py-2">
                                        <!-- START OF CHANGE: This block displays the image -->
                                        <div class="flex gap-3 items-center">
                                            <img v-if="page.icon_url" :src="page.icon_url" class="h-10 w-10 rounded-lg object-cover" alt="icon" />
                                            <div v-else class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <i class="icon mgc_image_line text-gray-500 text-xl"></i>
                                            </div>
                                            <span class="whitespace-nowrap font-medium dark:text-white">
                                                {{ page.name }}
                                            </span>
                                        </div>
                                        <!-- END OF CHANGE -->
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <v-badge
                                            :color="page.status === 'active' ? 'green' : 'red'"
                                            :text="`${page.status === 'active' ? '<i class=\'icon mgc_check_fill text-xl\'></i>' : '<i class=\'icon mgc_eye_close_line text-xl\'></i>'} ${page.status === 'active' ? $t('Active') : $t('Inactive')}`"
                                        />
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <div class="flex justify-end gap-x-2">
                                            <v-button color="blue" class="whitespace-nowrap" @click="router.push({ name: 'page.edit', params: { id: page.id } })">
                                                <i class="icon mgc_edit_line"></i>
                                                {{ $t('Edit') }}
                                            </v-button>
                                            <v-button color="red" class="whitespace-nowrap" @click="pageStore.onDelete(page.id)">
                                                <i class="icon mgc_delete_line"></i>
                                                {{ $t('Delete') }}
                                            </v-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="3">
                                        <div class="flex items-center justify-center p-4">
                                            <v-loader v-if="pageStore.isLoading" :size="4" :dark="true" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div v-if="!pageStore.isLoading" class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ pageStore.pagination.total }}</span> {{ $t('Results') }}
                            </p>
                        </div>
                        <div>
                            <div class="inline-flex gap-x-2">
                                <button @click="pageStore.getPages(true, true)" :disabled="pageStore.pagination.current_page === 1" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    {{ $t('Previous') }}
                                </button>
                                <button @click="pageStore.getPages(true)" :disabled="pageStore.pagination.current_page === pageStore.pagination.last_page" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
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
// NO CHANGES TO YOUR SCRIPT
import {onMounted, watch} from 'vue'
import router from "@/routes";
import _ from "lodash";
import {usePageStore} from "@/stores/PageStore";

const pageStore = usePageStore()

watch(pageStore.filters, _.debounce(async () => {
    await pageStore.getPages()
}, 500))

onMounted(async () => {
    await pageStore.getPages()
})
</script>