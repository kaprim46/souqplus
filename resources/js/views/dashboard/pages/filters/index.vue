<template>
  <div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
          <!-- Header -->
          <div class="px-6 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
            <div>
              <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                <i class="icon mgc_filter_2_fill"></i>
                {{ $t('Filters') }}
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('Manage your filters') }}
              </p>
            </div>


            <div class="flex gap-x-2">
              <v-input :placeholder="$t('Search')"  />
              <v-button color="blue" @click="isModalOpen = true" custom-class="whitespace-nowrap">
                <i class="icon mgc_plus_line"></i>
                {{ $t('Add Filter') }}
              </v-button>
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
                          {{ $t('Type') }}
                      </span>
                  </th>
                  <th scope="col" class="text-end"></th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <template v-if="!filtersStore.isLoading">
                <tr v-for="filter in filtersStore.data_filters" :key="filter.id">
                  <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ filter.name }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-3 whitespace-nowrap">
                    <div class="text-sm text-gray-800 dark:text-gray-200">
                      {{ $t(filter.type) }}
                    </div>
                  </td>
                  <td class="px-6 py-3 whitespace-nowrap text-end">
                     <div class="flex items-center justify-end gap-2">
                        <v-button color="blue" @click="filtersStore.setData(filter); isModalOpen = true">
                          <i class="icon mgc_edit_line"></i>
                        </v-button>
                        <v-button color="red">
                          <i class="icon mgc_delete_2_line"></i>
                        </v-button>
                    </div>
                  </td>
                </tr>
              </template>
              <template v-else>
                <td colspan="5">
                  <div class="flex items-center justify-center p-4">
                    <v-loader :size="4" :dark="true" />
                  </div>
                </td>
              </template>
              </tbody>
            </table>
          </div>
          <!-- End Table -->


          <!-- Footer -->
          <div
              v-if="!filtersStore.isLoading"
              class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">

            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ filtersStore.pagination.total }}
                </span>
                {{ $t('Results') }}
              </p>
            </div>

            <div>
              <div class="inline-flex gap-x-2">
                <button
                    @click="filtersStore.fetchFilters(true, true)"
                    :disabled="filtersStore.pagination.current_page === 1"
                    type="button"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                  <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                  {{ $t('Previous') }}
                </button>

                <button
                    @click="filtersStore.fetchFilters(true)"
                    :disabled="filtersStore.pagination.current_page === filtersStore.pagination.last_page"
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
  <!-- Modal -->
  <v-modal :is-open="isModalOpen" @update:closeModal="isModalOpen = false; filtersStore.resetAll();">
    <template v-slot:title>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
        {{ filtersStore.data_filter?.id ? $t('Edit Filter') : $t('Add Filter') }}
      </h3>
    </template>

    <template v-slot:content>
      <form class="flex flex-col gap-y-4" @submit.prevent="filtersStore.data_filter?.id ? filtersStore.updateFilter() : filtersStore.storeFilter()">
        <!-- Name -->
        <div class="flex flex-col gap-1">
          <label for="name" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            {{ $t('Name') }}
          </label>
          <v-input
              id="name"
              :placeholder="$t('Filter Name')"
              :value="filtersStore.data_filter.name"
              @update:input="filtersStore.data_filter.name = $event"
          />
          <p v-if="filtersStore.errors.name" class="text-xs text-red-600">
            {{ filtersStore.errors.name[0] }}
          </p>
        </div>
        <!-- End Name -->

        <!-- Type -->
        <div class="flex flex-col gap-1">
          <label for="type" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            {{ $t('Type') }}
          </label>
          <v-select
              id="type"
              :options="[
                {label: $t('Text'), value: 'text'},
                {label: $t('Number'), value: 'number'},
                {label: $t('Select'), value: 'select'},
                {label: $t('Range'), value: 'range'},
                {label: $t('Checkbox'), value: 'checkbox'},
                {label: $t('Radio'), value: 'radio'},
                {label: $t('Year'), value: 'year'},
                {label: $t('Color'), value: 'color'},
              ]"
              :value="filtersStore.data_filter.type"
              @update:value="filtersStore.setFilterType($event)"
          />
          <p v-if="filtersStore.errors.type" class="text-xs text-red-600">
            {{ filtersStore.errors.type[0] }}
          </p>
        </div>
        <!-- End Type -->

        <!-- Dynamic Options -->
        <div class="flex flex-col gap-1" v-if="filtersStore.data_filter.type && filtersStore.data_filter.options">
          <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            {{ $t('Options') }}
          </span>
          <DynamicOptions
              :type-filter="filtersStore.data_filter.type"
              :options="filtersStore.data_filter.options"
              :errors="filtersStore.errors"
              @update:options="filtersStore.data_filter.options = $event"
          />
        </div>
        <!-- End Dynamic Options -->


        <div class="flex justify-end gap-2">
          <v-button color="blue" @click="isModalOpen = false; filtersStore.resetAll()">
            {{ $t('Cancel') }}
          </v-button>
          <v-button color="green" type="submit">
            {{ $t('Save') }}
          </v-button>
        </div>
      </form>
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
/**
 *   GET|HEAD        api/dashboard/filters ..........Api\Dashboard\FilterController@index
 *   POST            api/dashboard/filters .......... Api\Dashboard\FilterController@store
 *   PUT|PATCH       api/dashboard/filters/{filter} ......... Api\Dashboard\FilterController@update
 *   DELETE          api/dashboard/filters/{filter} .......... Api\Dashboard\FilterController@destroy
 */

import {onMounted, ref} from 'vue'
import {useFilterStore} from "@/stores/FilterStore";
import DynamicOptions from "@/views/dashboard/pages/filters/Components/DynamicOptions.vue";

const filtersStore  = useFilterStore();
const isModalOpen   = ref<boolean>(false);

onMounted(async() => {
  await filtersStore.fetchFilters();
})
</script>