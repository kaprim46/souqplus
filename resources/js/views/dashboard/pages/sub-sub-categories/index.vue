<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_org_tree_line"></i>
                                {{ $t('Sub Sub Categories') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your sub sub categories') }}
                            </p>
                        </div>
                        <div class="flex flex-col md:flex-row gap-2">
                            <v-input :placeholder="$t('Search')" @update:input="store.filters.search = $event" />
                            <v-button color="green" class="whitespace-nowrap" @click="router.push({ name: 'sub-sub-category.create', params: { sub_category_id: route.params.sub_category_id } })">
                                <i class="icon mgc_add_circle_fill"></i>
                                {{ $t('Create new sub sub category') }}
                            </v-button>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="relative overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-slate-800">
                                <tr>
                                    <th scope="col" class="px-6 py-2 text-left"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ $t('Name') }}</span></th>
                                    <th scope="col" class="px-6 py-2 text-left"><span class="text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ $t('Status') }}</span></th>
                                    <th scope="col" class="px-6 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-if="!store.isLoading" v-for="item in store.subSubCategories" :key="item.id">
                                    <td class="h-px px-6 py-2 whitespace-nowrap">{{ item.name }}</td>
                                    <td class="h-px px-6 py-2">
                                        <v-badge :color="item.status === 'active' ? 'green' : 'red'" :text="item.status === 'active' ? $t('Active') : $t('Inactive')" />
                                    </td>
                                    <td class="h-px px-6 py-2">
                                        <div class="flex justify-end gap-x-2">
                                            <v-button color="blue" @click="router.push({ name: 'sub-sub-category.edit', params: { id: item.id } })">
                                                <i class="icon mgc_edit_line"></i> {{ $t('Edit') }}
                                            </v-button>
                                            <v-button color="red" @click="store.onDelete(item.id as number, subCategoryId as number)">
                                                <i class="icon mgc_delete_line"></i> {{ $t('Delete') }}
                                            </v-button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else><td colspan="3" class="text-center"><v-loader class="p-4" v-if="store.isLoading" /></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div
                        v-if="!store.isLoading && store.pagination.total > 0"
                        class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ store.pagination.total }}
                                </span>
                                {{ $t('Results') }}
                            </p>
                        </div>

                        <div>
                            <div class="inline-flex gap-x-2">
                                <button
                                    @click="store.fetch(subCategoryId, false, true)"
                                    :disabled="store.pagination.current_page === 1"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
                                    {{ $t('Previous') }}
                                </button>

                                <button
                                    @click="store.fetch(subCategoryId, true, false)"
                                    :disabled="store.pagination.current_page === store.pagination.last_page"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
                                    {{ $t('Next') }}
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
import { useDashboardSubSubCategoryStore } from "@/stores/DashboardSubSubCategoryStore";
import { onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import _ from "lodash";

const store = useDashboardSubSubCategoryStore()
const router = useRouter()
const route = useRoute()
const subCategoryId = Number(route.params.sub_category_id);

watch(store.filters, _.debounce(async () => {
    if (subCategoryId) await store.fetch(subCategoryId)
}, 500), { deep: true })

onMounted(async () => {
    // Reset filters and page on mount to ensure fresh state
    store.filters.search = '';
    store.pagination.current_page = 1;
    if (subCategoryId) await store.fetch(subCategoryId)
})
</script>