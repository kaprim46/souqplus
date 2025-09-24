<template>
  <div class="relative">
    <template v-if="!isLoadingStore">
      <!-- Store cover -->
      <section class="relative block h-[350px] overflow-hidden rounded-2xl">
        <div
          class="absolute top-0 w-full h-full bg-center bg-cover"
          :style="`background-image: url('${store.business_cover_url}');`"
        >
          <span
            id="blackOverlay"
            class="w-full h-full absolute opacity-50 bg-black"
          ></span>
        </div>
        <div
          class="top-auto bottom-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden h-70-px"
          style="transform: translateZ(0px)"
        >
          <svg
            class="absolute bottom-0 overflow-hidden"
            xmlns="http://www.w3.org/2000/svg"
            preserveAspectRatio="none"
            version="1.1"
            viewBox="0 0 2560 100"
            x="0"
            y="0"
          >
            <polygon
              class="text-blueGray-200 fill-current"
              points="2560 0 2560 100 0 100"
            ></polygon>
          </svg>
        </div>
      </section>

      <!-- Store details -->
      <div class="container mx-auto px-4">
        <section
          class="translate-y-[180px] relative flex flex-col min-w-0 break-words bg-transparent w-full -mt-60"
        >
          <div class="p-6">
            <!-- Store avatar and name -->
            <figure class="relative">
              <img
                alt="Store logo"
                :src="store.business_logo_url"
                class="object-cover shadow-lg rounded-full w-24 h-24 border-4 border-blue-200 mx-auto bg-black"
              />
              <figcaption class="text-center mb-4">
                <h2
                  class="text-3xl font-semibold leading-tight flex items-center gap-2 justify-center"
                >
                  {{ store.name }}

                  <span v-if="store.business_is_verified" class="text-blue-400">
                    <svg
                      class="h-5 fill-blue-400"
                      xmlns="http://www.w3.org/2000/svg"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                      version="1.1"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      x="0"
                      y="0"
                    >
                      <path
                        d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"
                      />
                    </svg>
                  </span>
                </h2>
                <p
                  class="text-sm text-blueGray-400 mt-1"
                  v-html="store.business_bio ?? '--'"
                ></p>
              </figcaption>
            </figure>

            <!-- Store contact and follow button -->
            <div
              class="flex flex-col lg:flex-row lg:justify-between gap-5 items-center"
            >
              <!-- Store contact -->
              <div class="flex flex-col md:flex-row gap-2 lg:items-center">
                <a href="#" class="flex items-center gap-1 text-gray-600">
                  <i class="icon mgc_phone_add_fill"></i>
                  <span class="uppercase font-semibold text-[13.3px]" dir="ltr">
                    ({{ store.country_code ?? '--' }})
                    {{ store.phone_number ?? '--' }}
                  </span>
                </a>
                <div class="hidden lg:block h-[30px] w-[1px] bg-gray-300"></div>
                <a href="#" class="flex items-center gap-1 text-gray-600">
                  <i class="icon mgc_mail_fill"></i>
                  <span class="uppercase font-semibold text-[13.3px]">{{
                    store.business_email ?? '--'
                  }}</span>
                </a>
              </div>

              <!-- Advertisements, followers, and follow button -->
              <div class="flex items-center gap-5">
                <div class="flex gap-2 items-end">
                  <!-- Number of advertisements and followers -->
                  <div class="flex items-center">
                    <div class="px-2 flex flex-col items-center justify-center">
                      <h2 class="text-2xl font-semibold">
                        {{ store.advertisements }}
                      </h2>
                      <p class="text-blueGray-400">
                        {{ $t('Advertisements') }}
                      </p>
                    </div>
                    <div class="w-[2px] h-8 bg-blueGray-200"></div>
                    <div class="px-2 flex flex-col items-center justify-center">
                      <h2 class="text-2xl font-semibold">
                        {{ store.followers }}
                      </h2>
                      <p class="text-blueGray-400">{{ $t('Followers') }}</p>
                    </div>
                  </div>
                </div>
                <!-- Follow button -->
                <v-followButton
                  @on:response="onFollowResponse($event)"
                  @on:loading="followLoading = $event"
                  :is-following="store.is_following_store"
                  :user-id="store.id"
                  :followLoading="followLoading"
                  typeFollowing="business"
                  v-if="store.id && userStore.me?.id !== store.id"
                >
                  <template #button>
                    <button
                      type="button"
                      class="bg-transparent border font-semibold px-5 py-2 rounded-full transition-colors flex items-center justify-center gap-4"
                      :class="
                        store.is_following_store
                          ? 'border-red-400 text-red-400 hover:bg-red-400 hover:text-white'
                          : 'border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white'
                      "
                      :disabled="followLoading"
                    >
                      <i
                        v-if="store.is_following_store"
                        class="icon mgc_check_line text-lg"
                      ></i>
                      {{
                        store.is_following_store ? $t('Unfollow') : $t('Follow')
                      }}
                    </button>
                  </template>
                </v-followButton>
                <!-- End Follow button -->

                <!-- Chat button -->
                <router-link
                  :to="{
                    name: 'chat',
                    query: { user_id: store.id, type: 'business' },
                    params: { slug: store.business_slug },
                  }"
                  class="bg-transparent border border-black font-semibold px-5 py-2 rounded-full transition-colors flex items-center justify-center gap-4"
                >
                  <i class="icon mgc_chat_2_line text-lg"></i>
                  {{ $t('Send Message') }}
                </router-link>
                <!-- End Chat button -->
              </div>
            </div>
          </div>
        </section>

        <div class="flex flex-col lg:flex-row lg:items-start gap-5 mt-44">
          <!-- Filters -->
          <aside
            class="bg-white w-full lg:w-1/4 rounded shadow divide-y divide-solid"
          >
            <h3 class="text-xl flex items-center gap-1 p-3 text-gray-700">
              <i class="icon mgc_filter_2_fill"></i>
              {{ $t('Sort by') }}
            </h3>

            <div class="flex flex-col gap-3 divide-y divide-solid">
              <!-- Form Group country -->
              <div class="px-3 py-1">
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
              <div class="px-3 py-1">
                <label for="city" class="block text-sm mb-2 dark:text-white">{{
                  $t('City')
                }}</label>
                <v-select
                  id="city_id"
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
              <div class="px-3 py-1">
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

              <!-- Form Group -->
              <div class="px-3 pt-1 pb-3" v-if="!categoryStore.isLoading">
                <label
                  for="sub_category_id"
                  class="block text-sm mb-2 dark:text-white"
                  >{{ $t('Sub Category') }}</label
                >
                <v-select
                  id="sub_category_id"
                  :value="advertisementStore.filters.sub_category"
                  @update:value="
                    advertisementStore.filters.sub_category = $event
                  "
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
            </div>
          </aside>
          <!-- End Filters -->

          <!-- Advertisements -->
          <div v-if="!advertisementStore.isLoading" class="flex-1">
            <template v-if="advertisementStore.advertisements.length">
              <div
                class="relative flex flex-col min-w-0 break-words w-full ads-list"
              >
                <Advertisement
                  v-for="advertisement in advertisementStore.advertisements"
                  :key="advertisement.id"
                  :advertisement="advertisement"
                />
              </div>
              <!-- Footer -->
              <div
                v-if="!advertisementStore.isLoading"
                class="py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700"
              >
                <div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span
                      class="font-semibold text-gray-800 dark:text-gray-200"
                    >
                      {{ advertisementStore.pagination.total }}
                    </span>
                    {{ $t('Results') }}
                  </p>
                </div>
                <div>
                  <div class="inline-flex gap-x-2">
                    <button
                      @click="advertisementStore.fetch(true, true)"
                      :disabled="
                        advertisementStore.pagination.current_page === 1
                      "
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
            <div v-else class="flex items-center justify-center w-full h-full">
              <img
                :src="`${$assetUrl}/empty-trash.svg`"
                alt="No advertisements found"
                class="w-1/2 h-1/2"
              />
            </div>
          </div>
          <!-- End Advertisements -->

          <!-- Skeleton -->
          <div class="relative w-full" v-else>
            <v-skeleton :loop-number="12"></v-skeleton>
          </div>
          <!-- End Skeleton -->
        </div>
      </div>
    </template>
    <div v-else class="flex justify-center items-center">
      <v-loader color="blue" :size="8"></v-loader>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { useCustomerCategoryStore } from '@/stores/Customer/CustomerCategoryStore';
import { nextTick, onBeforeMount, onMounted, ref, watch } from 'vue';
import { useCountryStore } from '@/stores/CountryStore';
import { useUserStore } from '@/stores/UserStore';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/axios';

/**
 * Components
 */
import Advertisement from '@/views/components/advertisement.vue';

const categoryStore = useCustomerCategoryStore();
const userStore = useUserStore();
const countryStore = useCountryStore();
const advertisementStore = useCustomerAdvertisementStore();
const store = ref<any>({});
const $route = useRoute();
const $router = useRouter();
const followLoading = ref(false);
const isLoadingStore = ref(true);

/**
 * Methods
 */
const fetchStore = async () => {
  isLoadingStore.value = true;

  try {
    const { data } = await axios.get(`/stores/${$route.params.id}`);

    if (data.ok) {
      store.value = data.store;

      await nextTick();

      isLoadingStore.value = false;
    }

    return data.ok;
  } catch (e: any) {
    if (e.response.status === 404) {
      await $router.push({ name: '404' });
    }
  }
};

const onFollowResponse = (isFollowing: any) => {
  store.value.is_following_store = isFollowing;
};

/**
 * Watchers

 */
watch(
  () => advertisementStore.filters.country_id,
  async (value) => {
    if (value) {
      await countryStore.fetchCities(false, false, value);
    }
  },
  { immediate: true }
);

watch(
  () => advertisementStore.filters.category,
  async (value) => {
    if (value) {
      await categoryStore.fetchSubCategories(value);
    }
  },
  { immediate: true }
);

watch(
  () => advertisementStore.filters,
  async (value) => {
    if (!value.user_id || !value.pagination) return;

    await advertisementStore.fetch();
  },
  { deep: true }
);

/**
 * Hooks
 */
onBeforeMount(async () => {
  if (!$route.params.id) return;

  await fetchStore().then(async (ok) => {
    if (ok) {
      await categoryStore.fetch();

      await countryStore.fetchCountries();

      if (await advertisementStore.resetAll()) {
        advertisementStore.filters = {
          user_id: parseInt($route.params.id as string),
          pagination: 1,
        };
      }
    }
  });
});
</script>
