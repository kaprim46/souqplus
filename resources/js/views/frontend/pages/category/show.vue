<template>
  <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto flex flex-col gap-5">
    <template v-if="!categoryStore.isLoading">
      <!-- Header -->
      <div class="flex flex-col">
        <!-- Title -->
        <h1
          class="text-4xl font-extrabold text-prussianblue dark:text-gray-300 flex items-center gap-1"
        >
          <span class="block">
            {{ categoryStore.category?.name }}
          </span>
        </h1>

        <!-- Description -->
        <p class="text-base text-gray-500 dark:text-gray-400">
          {{ categoryStore.category?.description }}
        </p>
      </div>

      <hr />
      <!-- End Header -->
    </template>

    <div class="flex flex-col md:flex-row items-start gap-5 w-full">
      <!-- Filters -->
      <div
        class="bg-white rounded-xl shadow-sm w-full md:w-[18rem] dark:bg-slate-900 dark:border-gray-700 dark:shadow-slate-700/[.7] flex flex-col"
        v-if="categoryStore.subCategories.length"
      >
        <div class="px-4 py-2 border-b">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-300">
            <i class="icon mgc_filter_3_fill"></i>
            {{ $t('Sub Categories') }}
          </h2>
        </div>

        <ul class="px-4 py-2">
          <li
            v-for="subCategory in categoryStore.subCategories"
            :key="subCategory.id"
            class="flex items-center gap-2"
          >
            <input
              type="radio"
              :id="`sub-${subCategory.id}`"
              :value="subCategory.id"
              v-model="advertisementStore.filters.sub_category"
            />
            <label
              :for="`sub-${subCategory.id}`"
              class="flex items-center gap-2 cursor-pointer"
            >
              <span class="text-gray-800 dark:text-gray-300">
                {{ subCategory.name }}
              </span>
            </label>
          </li>
        </ul>

        <!-- Sub Sub Category Filter -->
        <div
          class="px-4 py-4 bg-gray-50 rounded-lg mt-4 mb-4 shadow-sm border border-gray-200"
        >
          <label
            class="block text-base font-bold mb-4 text-gray-800 dark:text-white tracking-wide"
          >
            <i class="icon mgc_layers_fill mr-2"></i>
            {{ $t('Sub Sub Category') }}
          </label>
          <ul class="space-y-3">
            <li
              v-for="subSub in subSubCategoryStore.subSubCategories"
              :key="subSub.id"
              class="flex items-center gap-3"
            >
              <input
                type="radio"
                :id="`subsub-${subSub.id}`"
                :value="subSub.id"
                v-model="advertisementStore.filters.sub_sub_category"
                :disabled="!subSubCategoryStore.subSubCategories.length"
                class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring focus:ring-blue-200"
              />
              <label
                :for="`subsub-${subSub.id}`"
                class="flex items-center gap-2 cursor-pointer text-gray-700 dark:text-gray-300 text-sm"
              >
                {{ subSub.name }}
              </label>
            </li>
          </ul>
        </div>

        <div class="px-4 py-2">
          <v-options
            :data="filterStore.data_filters_category"
            @on:update="onOptionsUpdate"
            :initial-data="categoryStore.category.filters"
          ></v-options>
        </div>
      </div>
      <!-- End Filters -->

      <!-- Content -->
      <div class="w-full flex-1 flex flex-col gap-5">
        <!-- Search -->
        <div>
          <label for="search_ads" class="sr-only">Label</label>
          <div class="flex rounded-lg shadow-sm">
            <input
              class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-s-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
              name="search_ads"
              id="search_ads"
              type="text"
              :placeholder="$t('Search for ads')"
              v-model="advertisementStore.filters.search"
            />
            <button
              type="button"
              class="w-[2.875rem] h-[2.875rem] flex-shrink-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-e-md border border-transparent bg-[#0C74BB] text-white hover:bg-[#0C74CC]"
            >
              <svg
                class="flex-shrink-0 size-4"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
              </svg>
            </button>
          </div>
        </div>
        <!-- End Search -->

        <template
          v-if="!advertisementStore.isLoading && !categoryStore.isLoading"
        >
          <!--Advertisements-->
          <div class="flex flex-col divide-y divide-gray-200 ads-list">
            <Advertisement
              v-for="advertisement in advertisementStore.advertisements"
              :key="advertisement.id"
              :advertisement="advertisement"
            />
          </div>
          <!-- End Advertisements -->

          <!-- Footer -->
          <div
            v-if="
              !advertisementStore.isLoading &&
              advertisementStore.advertisements?.length
            "
            class="w-full max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700"
          >
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                  {{ advertisementStore.pagination.total }}
                </span>
                {{ $t('Results') }}
              </p>
            </div>

            <div>
              <div class="inline-flex gap-x-2">
                <button
                  @click="advertisementStore.fetch(true, true)"
                  :disabled="advertisementStore.pagination.current_page === 1"
                  type="button"
                  class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                >
                  <svg
                    class="flex-shrink-0 w-4 h-4"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="m15 18-6-6 6-6" />
                  </svg>
                  {{ $t('Previous') }}
                </button>

                <button
                  @click="advertisementStore.fetch(true)"
                  :disabled="
                    advertisementStore.pagination.current_page ===
                    advertisementStore.pagination.last_page
                  "
                  type="button"
                  class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                >
                  {{ $t('Next') }}
                  <svg
                    class="flex-shrink-0 w-4 h-4"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="m9 18 6-6-6-6" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <!-- End Footer -->

          <!-- Empty -->
          <img
            v-if="
              !advertisementStore.isLoading &&
              !advertisementStore.advertisements?.length
            "
            :src="`${$assetUrl}/empty-trash.svg`"
            alt="Empty"
            class="w-[40rem]"
          />
          <!-- End Empty -->
        </template>
        <template v-else>
          <div class="flex flex-col gap-5">
            <v-skeleton :loop-number="3" v-for="i in 5" :key="i" />
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { useCustomerCategoryStore } from '@/stores/Customer/CustomerCategoryStore';
import { useFilterStore } from '@/stores/FilterStore';
import { useSubSubCategoryStore } from '@/stores/SubSubCategoryStore';
import { nextTick, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';

// Components
import VOptions from '@/views/dashboard/pages/advertisements/components/Options.vue';
import Advertisement from '@/views/components/advertisement.vue';

const advertisementStore = useCustomerAdvertisementStore();
const categoryStore = useCustomerCategoryStore();
const filterStore = useFilterStore();
const subSubCategoryStore = useSubSubCategoryStore();
const router = useRouter();

/**
 * Update the options
 * @param filters
 */
const onOptionsUpdate = (filters: any) => {
  advertisementStore.filters.category_filter = filters;

  advertisementStore.fetch();
};

/**
 * Fetch category and update advertisements
 */
const fetchCategory = async () => {
  if (
    !router.currentRoute.value.params.slug &&
    !router.currentRoute.value.params.sub
  )
    return;

  await categoryStore
    .fetchCategory(router.currentRoute.value.params.slug as string)
    .then(async () => {
      if (await advertisementStore.resetAll()) {
        advertisementStore.filters.category = categoryStore.category
          ?.id as number;
        advertisementStore.filters.pagination = 1;

        if (router.currentRoute.value.params.sub) {
          advertisementStore.filters.sub_category = parseInt(
            router.currentRoute.value.params.sub as string
          );
          await filterStore.getFiltersBySubCategory(
            advertisementStore.filters.sub_category
          );
        } else {
          await filterStore.getFiltersByCategory(
            categoryStore.category?.id as number
          );
        }

        await nextTick();

        await categoryStore.fetchSubCategories(
          categoryStore.category?.id as number
        );

        await advertisementStore.fetch();
      }
    });
};

/**
 * Watch if the route changes
 */
watch(
  () => router.currentRoute.value.params,
  async (oldValue, newValue) => {
    if (!oldValue.slug && !oldValue.sub) return;

    await fetchCategory();
  },
  { immediate: true }
);

watch(
  () => advertisementStore.filters.sub_category,
  async (value) => {
    if (value) {
      await subSubCategoryStore.fetchSubSubCategories(value);
      advertisementStore.filters.sub_sub_category = null;
    } else {
      subSubCategoryStore.subSubCategories = [];
      advertisementStore.filters.sub_sub_category = null;
    }
  },
  { immediate: true }
);

watch(
  () => advertisementStore.filters.sub_sub_category,
  async (value) => {
    await advertisementStore.fetch();
  }
);

watch(
  () => advertisementStore.filters.search,
  async (oldValue, newValue) => {
    if (!newValue) return;

    await advertisementStore.fetch();
  },
  { immediate: true }
);
</script>
