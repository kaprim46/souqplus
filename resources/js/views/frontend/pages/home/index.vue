<template>
  <div class="flex flex-col lg:flex-row lg:items-start gap-5">
    <div class="w-full lg:w-1/4 flex flex-col gap-5">
      <!-- Filters -->
      <aside class="bg-white rounded shadow divide-y divide-solid">
        <h3 class="text-xl flex items-center gap-1 p-3 text-gray-700">
          <i class="icon mgc_filter_2_fill"></i>
          {{ $t('Sort by') }}
        </h3>

        <div class="flex flex-col gap-3 divide-y divide-solid">
          <!-- Form Group country -->
          <div class="p-3">
            <label
              for="country_id"
              class="block text-sm mb-2 dark:text-white"
              >{{ $t('Country') }}</label
            >
            <v-select
              id="country_id"
              name="country_id"
              :value="advertisementStore.filters.country_id"
              @update:value="advertisementStore.filters.country_id = $event"
            >
              <option value="0">{{ $t('Select Country') }}</option>
              <option
                v-for="country in countryStore.countries"
                :key="country.id"
                :value="country.id"
              >
                @{{ country.name }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group -->

          <!-- Form Group city -->
          <div class="p-3">
            <label for="city" class="block text-sm mb-2 dark:text-white">{{
              $t('City')
            }}</label>
            <v-select
              id="city"
              name="city_id"
              :value="advertisementStore.filters.city_id"
              @update:value="advertisementStore.filters.city_id = $event"
            >
              <option value="0">{{ $t('Select City') }}</option>
              <option
                v-for="city in countryStore.cities"
                :key="city.id"
                :value="city.id"
              >
                @{{ city.name }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group -->

          <!-- Form Group -->
          <div class="p-3">
            <label
              for="category_id"
              class="block text-sm mb-2 dark:text-white"
              >{{ $t('Category') }}</label
            >
            <v-select
              id="category_id"
              :value="advertisementStore.filters.category"
              @update:value="advertisementStore.filters.category = $event"
            >
              <option value="" disabled selected>
                {{ $t('Select Category') }}
              </option>
              <option
                v-for="category in customerCategoryStore.categories"
                :key="category.id"
                :value="category.id"
              >
                @{{ category.name }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group -->

          <!-- Form Group -->
          <div class="p-3" v-if="!customerCategoryStore.isLoading">
            <label
              for="sub_category_id"
              class="block text-sm mb-2 dark:text-white"
              >{{ $t('Sub Category') }}</label
            >
            <v-select
              id="sub_category_id"
              :value="advertisementStore.filters.sub_category"
              @update:value="advertisementStore.filters.sub_category = $event"
            >
              <option value="" disabled selected>
                {{ $t('Select Sub Category') }}
              </option>
              <option
                v-for="category in customerCategoryStore.subCategories"
                :key="category.id"
                :value="category.id"
              >
                @{{ category.name }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group -->

          <!-- Form Group Manufacturing Year -->
          <div
            class="p-3"
            v-if="
              selectedCategoryName === 'Vehicule' ||
              selectedCategoryName === 'سيارات'
            "
          >
            <label
              for="manufacturing_year"
              class="block text-sm mb-2 dark:text-white"
              >{{ $t('Manufacturing Year') }}</label
            >
            <v-select
              id="manufacturing_year"
              :value="advertisementStore.filters.manufacturing_year"
              @update:value="
                advertisementStore.filters.manufacturing_year = $event
              "
            >
              <option value="" disabled selected>
                {{ $t('Select Year') }}
              </option>
              <option v-for="year in availableYears" :key="year" :value="year">
                @{{ year }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group Manufacturing Year -->

          <!-- Form Group Sub Sub Category -->
          <div
            class="p-3"
            v-if="
              !customerCategoryStore.isLoading &&
              advertisementStore.filters.sub_category
            "
          >
            <label
              for="sub_sub_category_id"
              class="block text-sm mb-2 dark:text-white"
              >{{ $t('Sub Sub Category') }}</label
            >
            <v-select
              id="sub_sub_category_id"
              :value="advertisementStore.filters.sub_sub_category"
              @update:value="
                advertisementStore.filters.sub_sub_category = $event
              "
            >
              <option value="" disabled selected>
                {{ $t('Select Sub Sub Category') }}
              </option>
              <option
                v-for="category in subSubCategoryStore.subSubCategories"
                :key="category.id"
                :value="category.id"
              >
                @{{ category.name }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group -->

          <!-- Sort By -->
          <div class="p-3">
            <label for="sort_by" class="block text-sm mb-2 dark:text-white">{{
              $t('Sort By')
            }}</label>
            <v-select
              id="sort_by"
              :value="advertisementStore.filters.sortBy"
              @update:value="advertisementStore.filters.sortBy = $event"
            >
              <option value="latest">{{ $t('Latest') }}</option>
              <option value="popular">{{ $t('Popular') }}</option>
              <option value="oldest">{{ $t('Oldest') }}</option>
            </v-select>
          </div>
          <!-- End Sort By -->

          <!-- Reset Filters -->
          <div class="p-3">
            <button
              @click="resetFilters"
              type="button"
              class="w-full py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
            >
              <i class="icon mgc_refresh_line"></i>
              {{ $t('Reset Filters') }}
            </button>
          </div>
          <!-- End Reset Filters -->
        </div>
      </aside>
      <!-- End Filters -->
    </div>

    <div class="w-full flex flex-col gap-4 relative overflow-hidden">
      <!-- Stories -->
      <stories />
      <!-- End Stories -->

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

      <template v-if="!advertisementStore.isLoading">
        <!-- Check if there are advertisements -->
        <template
          v-if="
            typeof advertisementStore.advertisements !== 'undefined' &&
            advertisementStore.advertisements.length
          "
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
            v-if="!advertisementStore.isLoading"
            class="py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700"
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
        </template>
        <!-- End Check if there are advertisements -->

        <!-- No advertisements found -->
        <div v-else class="flex items-center justify-center w-full h-full">
          <img
            :src="`${$assetUrl}/empty-trash.svg`"
            alt="No advertisements found"
            class="w-1/2 h-1/2"
          />
        </div>
        <!-- End No advertisements found -->
      </template>

      <!-- Skeleton -->
      <div class="relative w-full" v-else>
        <v-skeleton :loop-number="12"></v-skeleton>
      </div>
      <!-- End Skeleton -->
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { onBeforeMount, watch, computed } from 'vue';
import _ from 'lodash';

/**
 * Components
 */
import Advertisement from '@/views/components/advertisement.vue';
import { useCustomerCategoryStore } from '@/stores/Customer/CustomerCategoryStore';
import Stories from '@/views/components/stories.vue';
import { useCountryStore } from '@/stores/CountryStore';
import { useSubSubCategoryStore } from '@/stores/SubSubCategoryStore';

/**
 * States
 */
const advertisementStore = useCustomerAdvertisementStore();
const customerCategoryStore = useCustomerCategoryStore();
const countryStore = useCountryStore();
const subSubCategoryStore = useSubSubCategoryStore();

// Watch for country changes
watch(
  () => advertisementStore.filters.country_id,
  async (value) => {
    if (value) {
      await countryStore.fetchCities(false, false, value);
    }
  },
  { immediate: true }
);

// Watch for category changes
watch(
  () => advertisementStore.filters.category,
  async (value) => {
    if (value) {
      await customerCategoryStore.fetchSubCategories(value);
    } else {
      customerCategoryStore.subCategories = [];
      advertisementStore.filters.sub_category = null;
      advertisementStore.filters.sub_sub_category = null;
    }
  }
);

// Watch for sub-category changes
watch(
  () => advertisementStore.filters.sub_category,
  async (value) => {
    if (value) {
      await subSubCategoryStore.fetchSubSubCategories(value);
    } else {
      subSubCategoryStore.subSubCategories = [];
      advertisementStore.filters.sub_sub_category = null;
    }
  }
);

// Watch for sub-subcategory changes
watch(
  () => advertisementStore.filters.sub_sub_category,
  async (value) => {
    if (value) {
      await advertisementStore.fetch();
    }
  }
);

watch(
  () => advertisementStore.filters,
  _.debounce(async () => {
    await advertisementStore.fetch();
  }, 100),
  {
    immediate: true,
    deep: true,
  }
);

onBeforeMount(async () => {
  advertisementStore.resetAll({
    search: '',
    category: null,
    sub_category: null,
    sub_sub_category: null,
    user_id: null,
    sortBy: 'latest',
    pagination: 1,
    manufacturing_year: null,
  });

  await countryStore.fetchCountries();
  await customerCategoryStore.fetch();
});

/**
 * Methods
 */
const resetFilters = () => {
  advertisementStore.resetAll({
    search: '',
    category: null,
    sub_category: null,
    sub_sub_category: null,
    country_id: 0,
    city_id: 0,
    user_id: null,
    sortBy: 'latest',
    pagination: 1,
    manufacturing_year: null,
  });
};

const selectedCategoryName = computed(() => {
  const selectedCategory = customerCategoryStore.categories.find(
    (cat) => cat.id == advertisementStore.filters.category
  );
  return selectedCategory ? selectedCategory.name : null;
});

const availableYears = computed(() => {
  const currentYear = new Date().getFullYear();
  const startYear = 1900; // You can adjust this as needed
  const years = [];
  for (let year = currentYear; year >= startYear; year--) {
    years.push(year);
  }
  return years;
});
</script>

<style lang="scss"></style>
