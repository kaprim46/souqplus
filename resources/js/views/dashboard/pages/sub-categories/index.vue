<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_transfer_fill"></i>
                                {{ $t('Sub Categories') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your sub categories') }}
                            </p>
                        </div>


                        <div class="flex flex-col md:flex-row gap-2">
                            <v-input :placeholder="$t('Search')" @update:input="categoryStore.filters.search = $event" />

                            <v-button color="green" class="whitespace-nowrap" @click="router.push({ name: 'sub-category.create' })">
                                <i class="icon mgc_add_circle_fill"></i>
                                {{ $t('Create new sub category') }}
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
                                            {{ $t('Main Category') }}
                                        </span>
                                    </th>

                                    <th scope="col" class="px-6 py-2">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ $t('Description') }}
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
                            <tr v-if="!categoryStore.isLoading" v-for="category in categoryStore.categories" :key="category.id">
                                <td class="h-px px-6 py-2">
                                    <span class="whitespace-nowrap">
                                        {{ category.name }}
                                    </span>
                                </td>

                                <td class="h-px px-6 py-2">
                                    <span class="whitespace-nowrap">
                                        <i class="icon mgc_sitemap_line"></i>
                                        {{ category.category?.name ?? "--" }}
                                    </span>
                                </td>

                                <td class="h-px px-6 py-2">
                                    <span class="whitespace-nowrap">
                                        {{ category.description }}
                                    </span>
                                </td>
                                <td class="h-px px-6 py-2">
                                    <v-badge
                                        :color="category.status === 'active' ? 'green' : 'red'"
                                        :text="`${category.status === 'active' ? '<i class=\'icon mgc_check_fill text-xl\'></i>' : '<i class=\'icon mgc_eye_close_line text-xl\'></i>'} ${category.status === 'active' ? $t('Active') : $t('Inactive')}`"
                                    />
                                </td>
                                <td  class="h-px px-6 py-2">
                                    <div class="flex justify-end gap-x-2">
                                        <!-- THIS BUTTON NOW NAVIGATES DIRECTLY TO THE CREATE PAGE -->
                                        <v-button color="purple" class="whitespace-nowrap" @click="router.push({ name: 'sub-sub-category.create', params: { sub_category_id: category.id } })">
                                            <i class="icon mgc_org_tree_line"></i>
                                            {{ $t('Sub Sub Categories') }}
                                        </v-button>

                                        <v-button color="blue" class="whitespace-nowrap" @click="router.push({ name: 'sub-category.edit', params: { id: category.id } })">
                                            <i class="icon mgc_edit_line"></i>
                                            {{ $t('Edit') }}
                                        </v-button>

                                        <v-button color="red" class="whitespace-nowrap" @click="categoryStore.onDelete(category.id as number)">
                                            <i class="icon mgc_delete_line"></i>
                                            {{ $t('Delete') }}
                                        </v-button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Loader -->
                            <tr v-else>
                                <td colspan="6">
                                    <div class="flex items-center justify-center p-4">
                                        <v-loader v-if="categoryStore.isLoading" :size="4" :dark="true" />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div
                            v-if="!categoryStore.isLoading"
                            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ categoryStore.pagination.total }}
                                </span>
                                {{ $t('Results') }}
                            </p>
                        </div>

                        <div>
                            <div class="inline-flex gap-x-2">
                                <button
                                    @click="categoryStore.fetch(true, true)"
                                    :disabled="categoryStore.pagination.current_page === 1"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    {{ $t('Previous') }}
                                </button>

                                <button
                                    @click="categoryStore.fetch(true)"
                                    :disabled="categoryStore.pagination.current_page === categoryStore.pagination.last_page"
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
import {useSubCategoryStore} from "@/stores/SubCategoryStore";
import { onMounted, watch } from 'vue'
import router from "@/routes";
import _ from "lodash";

/**
 * States
 */
const categoryStore = useSubCategoryStore()


/**
 * Watchers
 */
watch(categoryStore.filters, _.debounce(async () => {
    await categoryStore.fetch()
}, 500))


/**
 * Hooks
 */
onMounted(async () => {
    await categoryStore.fetch()
})
</script>