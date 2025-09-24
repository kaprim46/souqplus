<template>
  <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto flex flex-col gap-5">
    <template v-if="!advertisementStore.isLoading">
      <!-- Header -->
      <h1
        class="text-xl md:text-4xl font-extrabold text-prussianblue dark:text-gray-300 flex items-center gap-1"
      >
        <i class="icon mgc_building_1_line"></i>
        <span class="block">
          {{ $t('My ads') }}
        </span>
      </h1>

      <hr />
      <!-- End Header -->

      <!-- Data -->
      <div
        v-if="advertisementStore.advertisements"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full"
      >
        <div
          class="bg-white border border-gray-200 rounded overflow-hidden flex flex-col md:flex-row w-full gap-5 p-2"
          v-for="advertisement in advertisementStore.advertisements"
          :key="advertisement.id"
        >
          <router-link
            class="w-full md:w-[100px] h-[18rem] md:h-[100px] object-cover rounded-lg relative overflow-hidden"
            v-if="advertisement.main_media?.url"
            :to="{
              name: 'show-advertisement',
              params: {
                id: advertisement.id,
                slug: advertisement.slug || 'details',
              },
            }"
          >
            <img
              class="absolute h-full w-full object-cover"
              :src="advertisement.main_media.url"
              :alt="advertisement.name"
            />
          </router-link>
          <div class="flex flex-col gap-1 flex-1">
            <router-link
              class="text-lg font-semibold text-gray-800 dark:text-gray-300 dark:hover:text-white"
              :to="{
                name: 'show-advertisement',
                params: {
                  id: advertisement.id,
                  slug: advertisement.slug || 'details',
                },
              }"
            >
              {{ advertisement.name }}
            </router-link>
            <!-- Location -->
            <div class="flex items-center gap-1">
              <i class="icon mgc_location_line"></i>
              <span class="text-sm text-gray-500">
                {{ advertisement?.country?.name }}
                <span v-if="advertisement?.city?.id">
                  - {{ advertisement?.city?.name }}</span
                >
              </span>
            </div>
            <!-- End Location -->
            <!-- Price -->
            <div class="flex items-center gap-1">
              <i class="icon mgc_receive_money_line"></i>
              <span class="text-sm text-gray-500">
                {{
                  advertisement.price
                    ? `${advertisement.price} ${settingsStore.appSettings.app_currency}`
                    : '--'
                }}
              </span>
            </div>
            <!-- End Price -->

            <!-- Status -->
            <div class="flex items-center gap-1" v-if="advertisement.status">
              <i class="icon mgc_check_line"></i>
              <span
                class="text-sm"
                :class="$statusHexColor(advertisement.status)"
              >
                {{ $t(advertisement.status) }}
              </span>
            </div>
            <!-- End Status -->

            <!-- Created At -->
            <div class="flex items-center gap-1">
              <i class="icon mgc_calendar_line"></i>
              <span class="text-sm text-gray-500">
                {{ advertisement.created_at }}
              </span>
            </div>
            <!-- End Created At -->

            <!-- Actions -->
            <div class="flex justify-end items-center gap-2">
              <template v-if="userStore.me?.is_verified">
                <!-- Post as story -->
                <v-button
                  color="prussianblue"
                  :circle="true"
                  @click="advertisementStore.postAsStore(advertisement)"
                  v-if="!advertisement.post_as_story"
                >
                  <i class="icon mgc_history_2_line"></i>
                </v-button>
                <!-- End Post as story -->

                <!-- Unpost as story -->
                <template v-if="advertisement.post_as_story">
                  <span class="text-sm text-gray-500">
                    <i class="icon mgc_history_2_line"></i>
                    {{ $t('Posted as story') }},
                    <span
                      class="cursor-pointer text-red-800"
                      @click="advertisementStore.unpostAsStore(advertisement)"
                      >{{ $t('Unpost') }}</span
                    >
                  </span>
                </template>
                <!-- End Unpost as story -->
              </template>

              <v-button
                color="red"
                :circle="true"
                @click="onDelete(advertisement)"
              >
                <i class="icon mgc_delete_2_line"></i>
              </v-button>
              <v-button
                color="blue"
                :circle="true"
                @click="
                  router.push({
                    name: 'edit-advertisement',
                    params: { id: advertisement.id },
                  })
                "
              >
                <i class="icon mgc_edit_2_line"></i>
              </v-button>
            </div>
            <!-- End Actions -->
          </div>
        </div>
      </div>
      <!-- End Data -->

      <!-- Empty -->
      <img
        v-if="
          !advertisementStore.isLoading &&
          !advertisementStore.advertisements.length
        "
        :src="`${$assetUrl}/empty-trash.svg`"
        alt="Empty"
        class="w-[40rem]"
      />
      <!-- End Empty -->

      <!-- Footer -->
      <div
        v-if="
          !advertisementStore.isLoading &&
          advertisementStore.advertisements.length
        "
        class="py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-50 dark:border-gray-700 w-full"
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
              @click="advertisementStore.fetchMyAdvertisements(true, true)"
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
              @click="advertisementStore.fetchMyAdvertisements(true)"
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
    <div v-else class="flex justify-center items-center">
      <v-loader color="blue" :size="8"></v-loader>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { inject, nextTick, onBeforeMount, onMounted } from 'vue';
import { useSettingsStore } from '@/stores/SettingsStore';
import { useRouter } from 'vue-router';
import { trans } from 'laravel-vue-i18n';
import { useUserStore } from '@/stores/UserStore';

const router = useRouter();
const advertisementStore = useCustomerAdvertisementStore();
const settingsStore = useSettingsStore();
const userStore = useUserStore();

const sweetAlert = inject('$swal');

const onDelete = async (advertisement: object) => {
  const { value } = await sweetAlert.fire({
    title: trans('Are you sure?'),
    text: trans("You won't be able to revert this!"),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: trans('Yes, delete it!'),
    cancelButtonText: trans('No, cancel!'),
    reverseButtons: true,
  });

  if (value) {
    await advertisementStore.deleteAdvertisement({ id: advertisement.id });
  }
};

onMounted(async () => {
  if (await advertisementStore.resetAll()) {
    advertisementStore.filters.pagination = 1 as number;

    await nextTick();

    await advertisementStore.fetchMyAdvertisements();
  }
});
</script>
