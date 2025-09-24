<template>
  <section class="mt-10">
    <!-- Heading -->
    <div class="mb-10">
      <h2 class="text-3xl font-bold flex items-center gap-2">
        <i class="icon mgc_world_2_line"></i>
        {{ $t('Explore') }}
      </h2>
    </div>
    <!-- End Heading -->

    <!-- Carousel -->
    <div class="relative mb-10" v-if="sliderStore.sliders.length">
      <div
        class="nz-slider bg-red-500 relative overflow-hidden w-full min-h-80 rounded-[10px]"
      >
        <transition name="slide">
          <div class="nz-slider-slide">
            <img
              :src="slider.image_url"
              alt="Slider"
              class="absolute top-0 start-0 w-full h-full object-cover"
            />
            <!-- Title & content if not empty -->
            <div
              class="absolute bottom-0 start-0 end-0 p-4 bg-gradient-to-t from-black to-transparent"
              v-if="slider.title || slider.description"
            >
              <h2 class="text-2xl font-bold text-white">{{ slider.title }}</h2>
              <p class="text-white">{{ slider.description }}</p>
            </div>
          </div>
        </transition>
      </div>

      <button
        type="button"
        class="absolute top-0 start-0 flex items-center justify-center rounded-end nz-slider-prev bg-white h-16 w-16 transform translate-y-[200%]"
        @click="prevSlide"
      >
        <i class="icon mgc_left_line text-2xl rtl:transform rtl:rotate-180"></i>
      </button>
      <button
        type="button"
        class="absolute top-0 end-0 flex items-center justify-center rounded-start nz-slider-next bg-white h-16 w-16 transform translate-y-[200%]"
        @click="nextSlide"
      >
        <i
          class="icon mgc_right_line text-2xl rtl:transform rtl:rotate-180"
        ></i>
      </button>
    </div>
    <!-- End Carousel -->

    <!-- Ads -->
    <div class="flex flex-col lg:flex-row lg:items-start gap-5">
      <!-- Filters -->
      <aside
        class="bg-white w-full lg:w-1/4 rounded shadow divide-y divide-solid"
      >
        <div class="flex items-center justify-between p-3">
          <h3 class="text-xl flex items-center gap-1 text-gray-700">
            <i class="icon mgc_filter_2_fill"></i>
            {{ $t('Sort by') }}
          </h3>
          <button
            @click="resetFilters"
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50"
          >
            <i class="icon mgc_refresh_line"></i>
            {{ $t('Reset Filters') }}
          </button>
        </div>

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
                v-for="category in categoryStore.categories"
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

          <!-- Form Group -->
          <div class="p-3" v-if="!categoryStore.isLoading">
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
                v-for="category in categoryStore.subCategories"
                :key="category.id"
                :value="category.id"
              >
                @{{ category.name }}
              </option>
            </v-select>
          </div>
          <!-- End Form Group -->

          <!-- Form Group -->
          <div class="p-3" v-if="!categoryStore.isLoading">
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
        </div>
      </aside>
      <!-- End Filters -->

      <div class="w-full flex flex-col gap-4 relative overflow-hidden">
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
    <!-- End Ads -->
  </section>
</template>

<script lang="ts" setup>
import { ref, nextTick, onMounted, computed, watch, onBeforeMount } from 'vue';
import { useSliderStore } from '@/stores/SliderStore';
import { useCustomerCategoryStore } from '@/stores/Customer/CustomerCategoryStore';
import { useUserStore } from '@/stores/UserStore';
import { useCountryStore } from '@/stores/CountryStore';
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import Advertisement from '@/views/components/advertisement.vue';
import { debounce } from 'lodash';
import { useSubSubCategoryStore } from '@/stores/SubSubCategoryStore';

const categoryStore = useCustomerCategoryStore();
const userStore = useUserStore();
const countryStore = useCountryStore();
const advertisementStore = useCustomerAdvertisementStore();
const subSubCategoryStore = useSubSubCategoryStore();

const sliderStore = useSliderStore();
const currentSlide = ref(0);
const interval = ref<number | null | undefined | NodeJS.Timeout>(null);
const slider = computed(() => sliderStore.sliders[currentSlide.value] ?? {});

const showManufacturingYearFilter = computed(() => {
  const selectedCategory = categoryStore.categories.find(
    (cat) => cat.id == advertisementStore.filters.category
  );
  return (
    selectedCategory &&
    ((selectedCategory.name &&
      selectedCategory.name.toLowerCase().includes('vehicule')) ||
      selectedCategory.name === 'سيارات')
  );
});

const selectedCategoryName = computed(() => {
  const selectedCategory = categoryStore.categories.find(
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

/**
 * Methods
 */

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % sliderStore.sliders.length;
};

const prevSlide = () => {
  currentSlide.value =
    (currentSlide.value - 1 + sliderStore.sliders.length) %
    sliderStore.sliders.length;
};

const startAutoSlide = () => {
  interval.value = setInterval(() => {
    nextSlide();
  }, 5000);
};

const stopAutoSlide = () => {
  if (interval.value) {
    clearInterval(interval.value);
    interval.value = null;
  }
};

const resetFilters = () => {
  advertisementStore.resetAll({
    pagination: 1,
    search: '',
    category: null,
    sub_category: null,
    sub_sub_category: null,
    sortBy: 'latest' as const,
    country_id: null,
    city_id: null,
    manufacturing_year: null,
  });
  advertisementStore.fetchExploreAdvertisements();
};

/**
 * Watchers
 */

// Watch all filters for changes
watch(
  () => ({
    search: advertisementStore.filters.search,
    category: advertisementStore.filters.category,
    sub_category: advertisementStore.filters.sub_category,
    sub_sub_category: advertisementStore.filters.sub_sub_category,
    country_id: advertisementStore.filters.country_id,
    city_id: advertisementStore.filters.city_id,
    sortBy: advertisementStore.filters.sortBy,
    manufacturing_year: advertisementStore.filters.manufacturing_year,
  }),
  debounce(async () => {
    await advertisementStore.fetchExploreAdvertisements();
  }, 300),
  { deep: true }
);

// Existing watchers for loading related data
watch(
  () => advertisementStore.filters.country_id,
  async (value) => {
    if (value) {
      await countryStore.fetchCities(false, false, value);
    } else {
      advertisementStore.filters.city_id = null;
    }
  },
  { immediate: true }
);

watch(
  () => advertisementStore.filters.category,
  async (value) => {
    if (value) {
      await categoryStore.fetchSubCategories(value);
      advertisementStore.filters.sub_category = null;
    } else {
      categoryStore.subCategories = [];
      advertisementStore.filters.sub_category = null;
    }
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
  () => currentSlide.value,
  () => {
    stopAutoSlide();
    startAutoSlide();
  }
);

/**
 * Lifecycle hooks
 */

onBeforeMount(async () => {
  advertisementStore.resetAll({
    pagination: 1,
    search: '',
    category: null,
    sub_category: null,
    sub_sub_category: null,
    country_id: null,
    city_id: null,
    sortBy: 'latest' as const,
    manufacturing_year: null,
  });

  await countryStore.fetchCountries();
  await categoryStore.fetch();
  await advertisementStore.fetchExploreAdvertisements();
});

onMounted(async () => {
  try {
    // Start the auto-slide for the slider
    startAutoSlide();
  } catch (error) {
    console.error('Error initializing explore page:', error);
  }
});
</script>

<style scoped>
.nz-slider-slide {
  min-width: 100%;
  height: 300px;
  background-size: cover;
}

.slide-enter-active,
.slide-leave-active {
  transition: opacity 0.5s ease; /* Adjust the transition duration and easing as needed */
}
.slide-enter,
.slide-leave-to {
  opacity: 0;
}

.rounded-lg {
  border-radius: 0.5rem !important;
}
</style>
